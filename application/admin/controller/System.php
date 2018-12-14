<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/14
 * Time: 上午11:19
 */

namespace app\admin\controller;

use app\admin\model\AdminConfig as ConfigModel;
use app\admin\model\AdminModule as ModuleModel;

/**
 * 系统设置控制器
 * @package app\admin\controller
 */

class System extends Admin
{

    /**
     * 系统基础配置
     * @author 橘子俊 <364666827@qq.com>
     * @return mixed
     */
    public function index($group = 'base')
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $types = $data['type'];
            if (isset($data['id'])) {
                $ids = $data['id'];
            } else {
                $ids = $data['id'] = '';
            }

            // 清除上传字段
            unset($data['upload']);

            // token 验证
            $validate = new Validate([
                '__token__' => 'token',
            ]);
            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }

            // 非系统模块配置保存
            if (isset($data['module'])) {
                $row = ModuleModel::where('name', $data['module'])->field('id,config')->find()->toArray();
                if (!isset($row['config'])) {
                    return $this->error('保存失败！(原因：'.$data['module'].'模块无需配置)');
                }
                $row['config'] = json_decode($row['config'], 1);
                foreach ($row['config'] as $key => &$conf) {
                    if (!isset($ids[$key]) && $conf['type'] =='switch') {
                        $conf['value'] = 0;
                    } else if ($conf['type'] =='checkbox' && isset($ids[$key])) {
                        $conf['value'] = json_encode($ids[$key], 1);
                    } else {
                        $conf['value'] = $ids[$key];
                    }
                }

                if (ModuleModel::where('id', $row['id'])->setField('config', json_encode($row['config'], 1)) === false) {
                    return $this->error('保存失败');
                }
                ModuleModel::getConfig('', true);
                return $this->success('保存成功');
            }

            // 系统模块配置保存
            if (!$types) return false;
            $admin_path = config('sys.admin_path');
            foreach ($types as $k => $v) {
                if ($v == 'switch' && !isset($ids[$k])) {
                    ConfigModel::where('name', $k)->update(['value' => 0]);
                    continue;
                }

                if ($v == 'checkbox' && isset($ids[$k])) {
                    $ids[$k] = json_encode($ids[$k], 1);
                }

                // 修改后台管理目录
                if ($k == 'admin_path' && $ids[$k] != config('sys.admin_path')) {
                    if (is_file(ROOT_PATH.config('sys.admin_path')) && is_writable(ROOT_PATH.config('sys.admin_path'))) {
                        @rename(ROOT_PATH.config('sys.admin_path'), ROOT_PATH.$ids[$k]);
                        if (!is_file(ROOT_PATH.$ids[$k])) {
                            $ids[$k] = config('sys.admin_path');
                        }
                        $admin_path = $ids[$k];
                    }
                }
                ConfigModel::where('name', $k)->update(['value' => $ids[$k]]);
            }

            // 更新配置缓存
            $config = ConfigModel::getConfig('', true);

            if ($group == 'sys') {
                if (file_exists(ROOT_PATH.'.env')) {
                    unlink(ROOT_PATH.'.env');
                }
                $env = "//设置开启调试模式\napp_debug = " . ($config['sys']['app_debug'] ? 'true' : 'false');
                $env .= "\n//应用Trace\napp_trace = " . ($config['sys']['app_trace'] ? 'true' : 'false');
                file_put_contents(ROOT_PATH.'.env', $env);
            }

            return $this->success('保存成功', ROOT_DIR.$admin_path.'/admin/system/index/group/'.$group.'.html');
        }

        $tab_data = [];
        foreach (config('sys.config_group') as $key => $value) {
            $arr = [];
            $arr['title'] = $value;
            $arr['url'] = '?group='.$key;
            $tab_data['menu'][] = $arr;
        }

        $map = [];
        $map['group'] = $group;
        $map['status'] = 1;
        $data_list = ConfigModel::where($map)->order('sort,id')->column('id,name,title,group,url,value,type,options,tips');
        foreach ($data_list as $k => &$v) {
            $v['id'] = $v['name'];
            if (!empty($v['options'])) {
                $v['options'] = parse_attr($v['options']);
            }
        }

        // 模块配置
        $module = ModuleModel::where('status', 2)->column('name,title,config', 'name');
        foreach ($module as $mod) {
            if (empty($mod['config'])) continue;
            $arr = [];
            $arr['title'] = $mod['title'];
            $arr['url'] = '?group='.$mod['name'];
            $tab_data['menu'][] = $arr;
            if ($group == $mod['name']) {
                $data_list = json_decode($mod['config'], 1);
                foreach ($data_list as $k => &$v) {
                    if (!empty($v['options'])) {
                        $v['options'] = parse_attr($v['options']);
                    }
                    $v['id'] = $k;
                    $v['module'] = $mod['name'];
                }
            }
        }

        $tab_data['current'] = url('?group='.$group);
        $_GET['group'] = $group;

        $this->assign('data_list', $data_list);
        $this->assign('tab_data', $tab_data);
        $this->assign('tab_type', 1);
        return $this->fetch();
    }
}
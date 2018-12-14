<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/12
 * Time: 上午9:57
 */

namespace app\common\behavior;
use app\admin\model\AdminModule as ModuleModel;
use app\admin\model\AdminConfig as ConfigModel;
use think\Config;
use think\Lang;
use think\Request;
use think\View as ViewTemplate;

/**
 * 初始化基础配置行为
 * 将扩展的全局配置本地化
 */
class Base
{
    public function run(&$params)
    {
        // 获取当前模块名称
        $module = '';
        $dispatch = request()->dispatch();
        if (isset($dispatch['module'])) {
            $module = $dispatch['module'][0];
        }
        // 系统版本
        $version = include_once(ROOT_PATH.'version.php');
        config($version);

        // 设置模块配置
        config(ModuleModel::getConfig());
        // 设置系统配置
        config(ConfigModel::getConfig());

        // 判断模块是否存在且已安装
        $theme = 'default';
        if ($module != 'index' && !defined('ENTRANCE')) {
            if (empty($module)) {
                $module = config('default_module');
            }
            $mod_info = ModuleModel::where(['name' => $module, 'status' => 2])->find();
            if (!$mod_info) {
                exit(self::error($module.' 模块可能未启用或者未安装！'));
            }
            // 设置模块的默认主题
            $theme = $mod_info['theme'] ? $mod_info['theme'] : 'default';
        }

        // 获取站点根目录
        $root_dir = request()->baseFile();
        $root_dir  = preg_replace(['/index.php$/', '/plugins.php$/', '/'.config('sys.admin_path').'$/'], ['', '', ''], $root_dir);
        define('ROOT_DIR', $root_dir);

        //静态目录扩展配置
        $view_replace_str = [
            // 站点根目录
            '__ROOT_DIR__'      => ROOT_DIR,
            // 静态资源根目录
            '__STATIC__'        => ROOT_DIR.'static',
            // 文件上传目录
            '__UPLOAD__'        => ROOT_DIR.'upload',
            // 插件目录
            '__PLUGINS__'       => ROOT_DIR.'plugins',
            // 后台公共静态目录
            '__ADMIN_CSS__'     => ROOT_DIR.'static/admin/css',
            '__ADMIN_JS__'      => ROOT_DIR.'static/admin/js',
            '__ADMIN_IMG__'     => ROOT_DIR.'static/admin/image',
            // 后台模块静态目录
            '__ADMIN_MOD_CSS__' => ROOT_DIR.'static/'.$module.'/css',
            '__ADMIN_MOD_JS__'  => ROOT_DIR.'static/'.$module.'/js',
            '__ADMIN_MOD_IMG__' => ROOT_DIR.'static/'.$module.'/image',
            // 前台公共静态目录
            '__PUBLIC_CSS__'    => ROOT_DIR.'static/css',
            '__PUBLIC_JS__'     => ROOT_DIR.'static/js',
            '__PUBLIC_IMG__'    => ROOT_DIR.'static/image',
        ];

        config('view_replace_str', array_merge(config('view_replace_str'), $view_replace_str));

        // 如果定义了入口为admin，则修改默认的访问控制器层
        if(defined('ENTRANCE') && ENTRANCE == 'admin') {
            if ($module == '') {
                header('Location: '.url('admin/publics/index'));
                exit;
            }
            if ($module != 'admin' && $module != 'index' && $module != 'extra') {
                config('url_controller_layer', 'admin');
                // 后台模板路径保持系统默认
                config('template.view_path', '');
            }
            if ($dispatch['module'][1] != 'publics') {
                config('template.layout_on', true);
            }
            // 设置后台默认语言到cookie
            if (isset($_GET['lang']) && !empty($_GET['lang'])) {
                cookie('admin_language', $_GET['lang']);
            } elseif (cookie('admin_language')) {
                Lang::range(cookie('admin_language'));
            } else {
                cookie('admin_language', config('default_lang'));
            }
        } else {
            if (empty($module)) {
                $module = config('default_module');
            }

            if ($module != 'index') {
                // 定义前台模板路径[分手机和PC]
                if (request()->isMobile() === true && config('base.wap_site_status') && file_exists('.'.ROOT_DIR.'theme'.DS.$module.DS.$theme.DS.'wap'.DS)) {
                    // 如果有移动端域名，强制跳转
                    $wap_domain = preg_replace(['/http:\/\/$/', '/https:\/\/$/'], ['', ''], config('base.wap_domain'));
                    if ($wap_domain && input('server.http_host') != $wap_domain) {
                        if (input('server.https') && input('server.https') == 'on') {
                            header('Location: https://'.$wap_domain);
                        }
                        header('Location: http://'.$wap_domain);
                    }
                    config('template.view_path', 'theme'.DS.$module.DS.$theme.DS.'wap'.DS);
                } else {
                    config('template.view_path', 'theme'.DS.$module.DS.$theme.DS);
                }
            }

            if (config('base.site_status') != 1) {
                exit(self::error('站点已关闭！'));
            }
            // 设置前台默认语言到cookie
            if (isset($_GET['lang']) && !empty($_GET['lang'])) {
                cookie('_language', $_GET['lang']);
            } elseif (cookie('_language')) {
                Lang::range(cookie('_language'));
            } else {
                cookie('_language', Lang::range());
            }
        }
    }

    private function error($msg = '', $url = null, $data = '', $wait = 3, array $header = [])
    {
        if (is_null($url)) {
            $url = Request::instance()->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ('' !== $url && !strpos($url, '://') && 0 !== strpos($url, '/')) {
            $url = Url::build($url);
        }

        $result = [
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];

        $template = Config::get('template');
        $view = Config::get('view_replace_str');

        return ViewTemplate::instance($template, $view)
            ->fetch(Config::get('dispatch_error_tmpl'), $result);

    }


}
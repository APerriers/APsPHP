<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/13
 * Time: 下午2:22
 */

namespace app\admin\controller;

use app\admin\model\AdminMenu as MenuModel;

/**
 * 菜单控制器
 * @package app\admin\controller
 */

class Menu extends Admin
{
    /**
     * 菜单管理
     * @author 橘子俊 <364666827@qq.com>
     * @return mixed
     */
    public function index()
    {
        $menu_list = MenuModel::getAllChild(0, 0);
        $tab_data = [];
        foreach ($menu_list as $key => $value) {
            $tab_data['menu'][$key]['title'] = $value['title'];
        }
        $push['title'] = '模块排序';
        array_push($tab_data['menu'], $push);
        $this->assign('menu_list', $menu_list);
        $this->assign('tab_data', $tab_data);
        $this->assign('tab_type', 2);
        return $this->fetch();
    }

    /**
     * 添加快捷菜单
     * @author 橘子俊 <364666827@qq.com>
     * @return string
     */
    public function quick()
    {
        $id = input('param.id/d');
        if (!$id) {
            return $this->error('参数传递错误');
        }
        $map = [];
        $map['id'] = $id;

        $row = MenuModel::where($map)->find()->toArray();
        if (!$row) {
            return $this->error('您添加的菜单不存在');
        }

        unset($row['id'], $map['id']);
        $map['url'] = $row['url'];
        $map['param'] = $row['param'];
        $map['uid'] = ADMIN_ID;
        $row['pid'] = $map['pid'] = 4;
        if (MenuModel::where($map)->find()) {
            return $this->error('您已添加过此快捷菜单');
        }
        $row['uid'] = ADMIN_ID;
        $row['debug'] = 0;
        $row['system'] = 0;
        $row['ctime'] = time();
        $model = new MenuModel();
        $res = $model->storage($row);
        if ($res === false) {
            return $this->error('快捷菜单添加失败');
        }
        return $this->success('快捷菜单添加成功');
    }

    /**
     * 删除菜单
     * @author 橘子俊 <364666827@qq.com>
     * @return mixed
     */
    public function del()
    {
        $id = input('param.ids/a');
        $model = new MenuModel();
        if ($model->del($id)) {
            return $this->success('删除成功');
        }
        return $this->error($model->getError());
    }

}
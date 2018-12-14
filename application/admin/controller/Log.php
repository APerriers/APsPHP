<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/14
 * Time: 下午2:08
 */

namespace app\admin\controller;


use app\admin\model\AdminLog as LogModel;
/**
 * 日志管理控制器
 * @package app\admin\controller
 */
class Log extends Admin
{
    /**
     * 日志首页
     * @author 橘子俊 <364666827@qq.com>
     * @return mixed
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $where = $data = [];
            $page = input('param.page/d', 1);
            $limit = input('param.limit/d', 15);
            $uid = input('param.uid/d');
            if ($uid) {
                $where['uid'] = $uid;
            }
            $data['data'] = LogModel::with('user')->where($where)->page($page)->limit($limit)->select();
            $data['count'] = LogModel::where($where)->count('id');
            $data['code'] = 0;
            $data['msg'] = '';
            return json($data);
        }
        return $this->fetch();
    }

    /**
     * 清空日志
     * @author 橘子俊 <364666827@qq.com>
     * @return mixed
     */
    public function clear()
    {
        if (!LogModel::where('id > 0')->delete()) {
            return $this->error('日志清空失败');
        }
        return $this->success('日志清空成功');
    }
}
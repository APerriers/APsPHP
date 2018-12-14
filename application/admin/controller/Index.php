<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/10
 * Time: 下午2:32
 */

namespace app\admin\controller;


use app\common\util\Dir;
use think\Controller;

class Index extends Admin
{
    /**
     * 首页
     * @author 橘子俊 <364666827@qq.com>
     * @return mixed
     */
    public function index()
    {
        if (cookie('aps_iframe')) {
            $this->view->engine->layout(false);
            return $this->fetch('iframe');
        } else {
            return $this->fetch();
        }
    }

    /**
     * 清理缓存
     * @author 橘子俊 <364666827@qq.com>
     * @return mixed
     */
    public function clear()
    {
        if (Dir::delDir(RUNTIME_PATH) === false) {
            return $this->error('缓存清理失败！');
        }
        return $this->success('缓存清理成功！');
    }

}
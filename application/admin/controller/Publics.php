<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/12
 * Time: 上午11:04
 */

namespace app\admin\controller;


use app\common\controller\BaseController;

class Publics extends BaseController
{
    /**
     * 登陆页面
     * @author 橘子俊 <364666827@qq.com>
     * @return mixed
     */
    public function index()
    {
        $model = model('AdminUser');
        if ($this->request->isPost()) {
            $username = input('post.username/s');
            $password = input('post.password/s');
            if (!$model->login($username, $password)) {
                return $this->error($model->getError(), url('index'));
            }
            return $this->success('登陆成功，页面跳转中...', url('index/index'));
        }

        if ($model->isLogin()) {
            $this->redirect(url('index/index', '', true, true));
        }

        return $this->fetch();
    }

    /**
     * 退出登陆
     * @author 橘子俊 <364666827@qq.com>
     * @return mixed
     */
    public function logout(){
        model('AdminUser')->logout();
        $this->redirect(ROOT_DIR);
    }

    /**
     * 解锁屏幕
     * @author 橘子俊 <364666827@qq.com>
     * @return mixed
     */
    public function unlocked()
    {
        $_pwd = input('post.password');
        $model = model('AdminUser');
        $login = $model->isLogin();
        if (!$login) {
            return $this->error('登录信息失效，请重新登录！');
        }
        $password = $model->where('id', $login['uid'])->value('password');
        if (!$password) {
            return $this->error('登录异常，请重新登录！');
        }
        if (!password_verify($_pwd, $password)) {
            return $this->error('密码错误，请重新输入！');
        }
        return $this->success('解锁成功');
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/13
 * Time: 上午10:21
 */

namespace app\admin\controller;
use app\common\model\AdminLanguage as LanguageModel;

/**
 * 语言包管理控制器
 * @package app\admin\controller
 */

class Language extends Admin
{
    /**
     * 语言包管理首页
     * @author 橘子俊 <364666827@qq.com>
     * @return mixed
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $data = [];
            $data['data'] = LanguageModel::order('sort asc')->select();
            $data['count'] = 0;
            $data['code'] = 0;
            $data['msg'] = '';
            return json($data);
        }

        return $this->fetch();
    }

}
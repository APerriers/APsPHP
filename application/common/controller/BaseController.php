<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/11
 * Time: 下午5:34
 */

namespace app\common\controller;


use think\Controller;

class BaseController extends Controller
{
    protected function _initialize() {}

    /**
     * 解析和获取模板内容 用于输出
     * @param string    $template 模板文件名或者内容
     * @param array     $vars     模板输出变量
     * @param array     $replace 替换内容
     * @param array     $config     模板参数
     * @param bool      $renderContent     是否渲染内容
     * @return string
     * @throws Exception
     * @author 橘子俊 <364666827@qq.com>
     */
    final protected function fetch($template = '', $vars = [], $replace = [], $config = [], $renderContent = false)
    {
        return parent::fetch($template , $vars , $replace , $config , $renderContent);
    }
}
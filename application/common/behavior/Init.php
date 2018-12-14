<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/12
 * Time: 上午9:57
 */

namespace app\common\behavior;
use app\admin\model\AdminModule as ModuleModel;

/**
 * 应用初始化行为
 */
class Init
{
    public function run(&$params)
    {
        // 后台强制关闭路由
        if (defined('ENTRANCE') && ENTRANCE == 'admin') {
            config('url_route_on', false);
            config('url_controller_layer', 'controller');
        } else {
            // 设置路由
            config('route_config_file', ModuleModel::moduleRoute());
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/14
 * Time: 下午3:54
 */

/**
 * 系统扩展配置，非TP框架配置
 */
return [
    // +----------------------------------------------------------------------
    // | 系统相关设置
    // +----------------------------------------------------------------------
    // 系统数据表
    'tables'            => [
        'admin_config',
        'admin_menu',
        'admin_module',
        'admin_role',
        'admin_user',
        'admin_hook',
        'admin_hook_plugins',
        'admin_plugins',
        'admin_member',
        'admin_member_level',
    ],
    // 系统会员等级，此处只为声明配置，app/common/behavior/Base.php 里面赋值
    'member_level'      => [],
    // 系统设置分组
    'config_group'      => [
        'base'      => '基础',
        'sys'       => '系统',
        'upload'    => '上传',
        'databases'  => '数据库',
    ],
    // 系统标准模块
    'modules' => ['admin', 'common', 'index', 'install', 'apsphp', 'plugin'],
    // 系统标准配置文件
    'config' => ['app', 'cache', 'cookie', 'database', 'log', 'queue', 'session', 'template', 'trace', 'aps_auth', 'aps_cloud', 'aps_system', 'apsphp'],
];
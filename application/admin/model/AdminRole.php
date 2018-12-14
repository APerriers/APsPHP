<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/12
 * Time: 下午5:04
 */

namespace app\admin\model;

use think\Model;

/**
 * 后台角色模型
 * @package app\admin\model
 */

class AdminRole extends Model
{
    // 定义时间戳字段名
    protected $createTime = 'ctime';
    protected $updateTime = 'mtime';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    // 写入时，将权限ID转成JSON格式
    public function setAuthAttr($value)
    {
        return json_encode($value);
    }

    /**
     * 检查访问权限
     * @param int $id 需要检查的节点ID
     * @author 橘子俊 <364666827@qq.com>
     * @return bool
     */
    public static function checkAuth($id = 0)
    {
        $login = session('admin_user');
        // 超级管理员直接返回true
        if ($login['uid'] == '1' || $login['role_id'] == '1') {
            return true;
        }
        // 获取当前角色的权限明细
        $role_auth = (array)session('role_auth_'.$login['role_id']);
        if (!$role_auth) {
            $map = [];
            $map['id'] = $login['role_id'];
            $auth = self::where($map)->value('auth');
            if (!$auth) {
                return false;
            }
            $role_auth = json_decode($auth, true);
            // 非开发模式，缓存数据
            if (config('sys.app_debug') == 0) {
                session('role_auth_'.$login['role_id'], $role_auth);
            }
        }
        if (!$role_auth) return false;
        return in_array($id, $role_auth);
    }

}
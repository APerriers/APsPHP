<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/12
 * Time: 下午4:29
 */

namespace app\admin\model;


use think\Model;
use app\admin\model\AdminRole as RoleModel;

/**
 * 后台用户模型
 * @package app\admin\model
 */
class AdminUser extends  Model
{

    // 定义时间戳字段名
    protected $createTime = 'ctime';
    protected $updateTime = 'mtime';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    // 对密码进行加密
    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    // 写入时，将权限ID转成JSON格式
    public function setAuthAttr($value)
    {
        if (empty($value)) return '';
        return json_encode($value);
    }

    // 获取最后登陆ip
    public function setLastLoginIpAttr()
    {
        return get_client_ip();
    }

    // 最后登录时间
    public function getLastLoginTimeAttr($value)
    {
        if (!$value) return '';
        return date('Y-m-d H:i', $value);
    }

    // 权限
    public function role()
    {
        return $this->hasOne('AdminRole', 'id', 'role_id');
    }

    /**
     * 用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @param bool $remember 记住登录 TODO
     * @author 橘子俊 <364666827@qq.com>
     * @return bool|mixed
     */
    public function login($username = '', $password = '', $remember = false)
    {
        $username = trim($username);
        $password = trim($password);
        $map = [];
        $map['status'] = 1;
        $map['username'] = $username;
        if ($this->validateData(input('post.'), 'AdminUser.login') != true) {
            $this->error = $this->getError();
            return false;
        }

        $user = self::where($map)->find();
        if (!$user) {
            $this->error = '用户不存在或被禁用！';
            return false;
        }

        // 密码校验
        if (!password_verify($password, $user->password)) {
            $this->error = '登陆密码错误！';
            return false;
        }

        // 检查是否分配角色
        if ($user->role_id == 0) {
            $this->error = '禁止访问(原因：未分配角色)！';
            return false;
        }

        // 角色信息
        $role = RoleModel::where('id', $user->role_id)->find()->toArray();
        if (!$role || $role['status'] == 0) {
            $this->error = '禁止访问(原因：角色分组可能被禁用)！';
            return false;
        }

        // 更新登录信息
        $user->last_login_time = time();
        $user->last_login_ip   = get_client_ip();
        if ($user->save()) {
            // 执行登陆
            $login = [];
            $login['uid'] = $user->id;
            $login['role_id'] = $user->role_id;
            $login['role_name'] = $role['name'];
            $login['nick'] = $user->nick;
            cookie('aps_iframe', $user->iframe);
            // 主题设置
            self::setTheme(isset($user->theme) ? $user->theme : 0);
            self::getThemes(true);
            // 缓存角色权限
            session('role_auth_'.$user->role_id, $user->auth ? json_decode($user->auth, true) : json_decode($role['auth'], true));
            // 缓存登录信息
            session('admin_user', $login);
            session('admin_user_sign', $this->dataSign($login));
            return $user->id;
        }
        return false;
    }

    /**
     * 获取主题列表
     * @author 橘子俊 <364666827@qq.com>
     * @return bool
     */
    public static function getThemes($cache = false)
    {
        $themeFile = '.'.config('view_replace_str.__ADMIN_CSS__').'/theme.css';
        $themes = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        if (is_file($themeFile)) {
            $content = file_get_contents($themeFile);
            preg_match_all("/\/\*{6}(.+?)\*{6}\//", $content, $diyTheme);
            if (isset($diyTheme[1]) && count($diyTheme[1]) > 0) {
                foreach ($diyTheme[1] as $v) {
                    if (preg_match("/^[A-Za-z0-9\-\_]+$/", trim($v))) {
                        array_push($themes, trim($v));
                    }
                }
                $themes = array_unique($themes);
            }
        }
        if ($cache) {
            session('aps_admin_themes', $themes);
        }
        return $themes;
    }

    /**
     * 设置主题
     * @author 橘子俊 <364666827@qq.com>
     * @return bool
     */
    public static function setTheme($name = 'default', $update = false)
    {
        cookie('aps_admin_theme', $name);
        $result = true;
        if ($update && defined('ADMIN_ID')) {
            $result = self::where('id', ADMIN_ID)->setField('theme', $name);
        }
        return $result;
    }


    /**
     * 判断是否登录
     * @author 橘子俊 <364666827@qq.com>
     * @return bool|array
     */
    public function isLogin()
    {
        $user = session('admin_user');
        if (isset($user['uid'])) {
            if (!self::where('id', $user['uid'])->find()) {
                return false;
            }
            return session('admin_user_sign') == $this->dataSign($user) ? $user : false;
        }
        return false;
    }

    /**
     * 数据签名认证
     * @param array $data 被认证的数据
     * @author 橘子俊 <364666827@qq.com>
     * @return string 签名
     */
    public function dataSign($data = [])
    {
        if (!is_array($data)) {
            $data = (array) $data;
        }
        ksort($data);
        $code = http_build_query($data);
        $sign = sha1($code);
        return $sign;
    }

    /**
     * 退出登陆
     * @author 橘子俊 <364666827@qq.com>
     * @return bool
     */
    public function logout()
    {
        session('admin_user', null);
        session('admin_user_sign', null);
    }
}
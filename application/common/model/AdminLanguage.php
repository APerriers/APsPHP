<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/13
 * Time: 上午10:23
 */

namespace app\common\model;


use think\Model;

/**
 * 语言包模型
 * @package app\common\model
 */
class AdminLanguage extends Model
{
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;

    /**
     * 获取语言包列表
     * @param  string $name 配置名
     * @param  bool $update 是否更新缓存
     * @author 橘子俊 <364666827@qq.com>
     * @return mixed
     */
    public function lists($name = '', $update = false)
    {
        $result = cache('sys_language');
        if (!$result || $update == true) {
            $result = self::order('sort asc')->column('id,code,name,icon,pack', 'code');
            cache('sys_language', $result);
        }
        $lang = config('default_lang');
        if ($name) {
            if (isset($result[$name])) {
                return $result[$name]['id'];
            } else {
                $lang = current($result);
                return $lang['id'];
            }
        }
        return $result;
    }
}
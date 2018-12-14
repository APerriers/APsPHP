<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/13
 * Time: 下午5:13
 */

namespace app\admin\model;


use think\Model;

/**
 * 系统配置模型
 * @package app\admin\model
 */
class AdminConfig extends Model
{
    // 定义时间戳字段名
    protected $createTime = 'ctime';
    protected $updateTime = 'mtime';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    /**
     * 获取系统配置信息
     * @param  string $name 配置名
     * @param  bool $update 是否更新缓存
     * @author 橘子俊 <364666827@qq.com>
     * @return mixed
     */
    public static function getConfig($name = '', $update = false)
    {
        $result = cache('sys_config');
        if (!$result || $update == true) {
            $configs = self::column('value,type,group', 'name');
            $result = [];
            foreach ($configs as $config) {
                switch ($config['type']) {
                    case 'array':
                    case 'checkbox':
                        if ($config['name'] == 'config_group') {
                            $v = parse_attr($config['value']);
                            if (!empty($config['value'])) {
                                $result[$config['group']][$config['name']] = array_merge(config('aps_system.config_group'), $v);
                            } else {
                                $result[$config['group']][$config['name']] = config('aps_system.config_group');
                            }
                        } else {
                            $result[$config['group']][$config['name']] = parse_attr($config['value']);
                        }
                        break;
                    default:
                        $result[$config['group']][$config['name']] = $config['value'];
                        break;
                }
            }
            cache('sys_config', $result);
        }
        return $name != '' ? $result[$name] : $result;
    }

}
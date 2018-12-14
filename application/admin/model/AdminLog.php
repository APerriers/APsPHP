<?php
/**
 * Created by PhpStorm.
 * User: tangzhipeng
 * Date: 2018/12/14
 * Time: 下午2:10
 */

namespace app\admin\model;


use think\Model;

/**
 * 后台日志模型
 * @package app\admin\model
 */
class AdminLog extends Model
{
    // 定义时间戳字段名
    protected $createTime = 'ctime';
    protected $updateTime = 'mtime';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    public function user()
    {
        return $this->hasOne('AdminUser', 'id', 'uid');
    }
}

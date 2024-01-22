<?php

namespace app\common\model;

use think\Model;

/**
 * 会员余额日志模型
 */
class NoticeNotDisturb Extends Model
{

    // 表名
    protected $name = 'notice_not_disturb';
    // 定义时间戳字段名
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
    ];
}

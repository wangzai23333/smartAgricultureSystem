<?php

namespace app\common\model;

use think\Model;


class Record extends Model
{

    

    

    // 表名
    protected $name = 'record';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'ntype_text',
        'sendtime_text'
    ];
    

    
    public function getNtypeList()
    {
        return ['1' => __('Ntype 1'), '2' => __('Ntype 2')];
    }


    public function getNtypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['ntype']) ? $data['ntype'] : '');
        $list = $this->getNtypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSendtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['sendtime']) ? $data['sendtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setSendtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function task()
    {
        return $this->belongsTo('Task', 'tid', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function recordlog()
    {
        return $this->belongsTo('RecordLog', 'id', 'recordid', [], 'LEFT')->setEagerlyType(0);
    }


    public function notice()
    {
        return $this->belongsTo('Notice', 'nid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}

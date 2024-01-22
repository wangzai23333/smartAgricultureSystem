<?php

namespace app\common\model;

use think\Model;


class SensorAdjust extends Model
{

    

    

    // 表名
    protected $name = 'sensor_adjust';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'change_type_text'
    ];
    

    
    public function getChangeTypeList()
    {
        return ['value' => __('Value'), 'ratio' => __('Ratio')];
    }


    public function getChangeTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['change_type']) ? $data['change_type'] : '');
        $list = $this->getChangeTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function sensorlist()
    {
        return $this->belongsTo('SensorList', 'sensorid', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function unit()
    {
        return $this->belongsTo('Unit', 'label', 'label', [], 'LEFT')->setEagerlyType(0);
    }
}

<?php

namespace app\common\model;

use think\Model;


class UnitSensor extends Model
{

    

    

    // 表名
    protected $name = 'unit_sensor';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







    public function unit()
    {
        return $this->belongsTo('Unit',  'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function sensor()
    {
        return $this->belongsTo('Sensor', 'sensor_type', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}

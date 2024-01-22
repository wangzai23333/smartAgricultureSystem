<?php

namespace app\common\model;

use think\Model;


class SensorList extends Model
{

    

    

    // 表名
    protected $name = 'sensor_list';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'createtime_text'
        
    ];
    
    
    
    public function getCreatetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['createtime']) ? $data['createtime'] : '');
        return is_numeric($value) ? $this->msecdate($value) : $value;
    }
    
    







    public function sensor()
    {
        return $this->belongsTo('Sensor', 'kind', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}

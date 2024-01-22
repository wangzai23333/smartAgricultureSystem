<?php

namespace app\admin\model;

use think\Model;


class Notice extends Model
{

    

    

    // 表名
    protected $name = 'notice';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'executiontime_text'
    ];
    

    



    public function getExecutiontimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['executiontime']) ? $data['executiontime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setExecutiontimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}

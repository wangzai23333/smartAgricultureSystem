<?php

namespace app\common\model;

use think\Model;


class Member extends Model
{

    

    

    // 表名
    protected $name = 'member';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
  
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'prevtime_text',
        'createtime_text'
        
    ];
    

    

    public function getCreatetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['createtime']) ? $data['createtime'] : '');
        return is_numeric($value) ? $this->msecdate($value) : $value;
    }

    public function getPrevtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['prevtime']) ? $data['prevtime'] : '');
        return is_numeric($value) ? $this->msecdate($value) : $value;
    }
    

    protected function setPrevtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}

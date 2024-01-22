<?php

namespace app\common\model;

use think\Model;


class DehumidifiersLog extends Model
{

    

    

    // 表名
    protected $name = 'dehumidifiers_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'onoff_text'
    ];
    

    
    public function getOnoffList()
    {
        return ['on' => __('Onoff on'), 'off' => __('Onoff off')];
    }


    public function getOnoffTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff']) ? $data['onoff'] : '');
        $list = $this->getOnoffList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function dehumidifiers()
    {
        return $this->belongsTo('Dehumidifiers', 'did', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}

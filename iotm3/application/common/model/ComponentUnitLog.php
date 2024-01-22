<?php

namespace app\common\model;

use think\Model;


class ComponentUnitLog extends Model
{

    

    

    // 表名
    protected $name = 'component_unit_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'onoff1_text',
        'onoff2_text',
        'onoff3_text',
        'onoff4_text',
        'onoff5_text',
        'onoff6_text',
        'onoff7_text',
        'onoff8_text',
        'onoff9_text',
        'onoff10_text'
    ];
    

    
    public function getOnoff1List()
    {
        return ['1' => __('Onoff1 1'), '0' => __('Onoff1 0')];
    }

    public function getOnoff2List()
    {
        return ['0' => __('Onoff2 0'), '1' => __('Onoff2 1')];
    }

    public function getOnoff3List()
    {
        return ['0' => __('Onoff3 0'), '1' => __('Onoff3 1')];
    }

    public function getOnoff4List()
    {
        return ['0' => __('Onoff4 0'), '1' => __('Onoff4 1')];
    }

    public function getOnoff5List()
    {
        return ['0' => __('Onoff5 0'), '1' => __('Onoff5 1')];
    }

    public function getOnoff6List()
    {
        return ['0' => __('Onoff6 0'), '1' => __('Onoff6 1')];
    }

    public function getOnoff7List()
    {
        return ['0' => __('Onoff7 0'), '1' => __('Onoff7 1')];
    }

    public function getOnoff8List()
    {
        return ['0' => __('Onoff8 0'), '1' => __('Onoff8 1')];
    }

    public function getOnoff9List()
    {
        return ['0' => __('Onoff9 0'), '1' => __('Onoff9 1')];
    }

    public function getOnoff10List()
    {
        return ['0' => __('Onoff10 0'), '1' => __('Onoff10 1')];
    }


    public function getOnoff1TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff1']) ? $data['onoff1'] : '');
        $list = $this->getOnoff1List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff2TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff2']) ? $data['onoff2'] : '');
        $list = $this->getOnoff2List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff3TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff3']) ? $data['onoff3'] : '');
        $list = $this->getOnoff3List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff4TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff4']) ? $data['onoff4'] : '');
        $list = $this->getOnoff4List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff5TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff5']) ? $data['onoff5'] : '');
        $list = $this->getOnoff5List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff6TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff6']) ? $data['onoff6'] : '');
        $list = $this->getOnoff6List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff7TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff7']) ? $data['onoff7'] : '');
        $list = $this->getOnoff7List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff8TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff8']) ? $data['onoff8'] : '');
        $list = $this->getOnoff8List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff9TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff9']) ? $data['onoff9'] : '');
        $list = $this->getOnoff9List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff10TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff10']) ? $data['onoff10'] : '');
        $list = $this->getOnoff10List();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function componentunit()
    {
        return $this->belongsTo('ComponentUnit', 'did', 'did', [], 'LEFT')->setEagerlyType(0);
    }
}

<?php

namespace app\common\model;

use think\Model;


class SensorStatus extends Model
{

    

    

    // 表名
    protected $name = 'sensor_status';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        
    ];
    

    
    public function getOnoff1List()
    {
        return ['1' => __('Onoff1 1'), '0' => __('Onoff1 0')];
    }

    public function getOnoff2List()
    {
        return ['1' => __('Onoff2 1'), '0' => __('Onoff2 0')];
    }

    public function getOnoff3List()
    {
        return ['1' => __('Onoff3 1'), '0' => __('Onoff3 0')];
    }

    public function getOnoff4List()
    {
        return ['1' => __('Onoff4 1'), '0' => __('Onoff4 0')];
    }

    public function getOnoff5List()
    {
        return ['1' => __('Onoff5 1'), '0' => __('Onoff5 0')];
    }

    public function getOnoff6List()
    {
        return ['1' => __('Onoff6 1'), '0' => __('Onoff6 0')];
    }

    public function getOnoff7List()
    {
        return ['1' => __('Onoff7 1'), '0' => __('Onoff7 0')];
    }

    public function getOnoff8List()
    {
        return ['1' => __('Onoff8 1'), '0' => __('Onoff8 0')];
    }

    public function getOnoff9List()
    {
        return ['1' => __('Onoff9 1'), '0' => __('Onoff9 0')];
    }

    public function getOnoff10List()
    {
        return ['1' => __('Onoff10 1'), '0' => __('Onoff10 0')];
    }

    public function getOnoff11List()
    {
        return ['1' => __('Onoff11 1'), '0' => __('Onoff11 0')];
    }

    public function getOnoff12List()
    {
        return ['1' => __('Onoff12 1'), '0' => __('Onoff12 0')];
    }

    public function getOnoff13List()
    {
        return ['1' => __('Onoff13 1'), '0' => __('Onoff13 0')];
    }

    public function getOnoff14List()
    {
        return ['1' => __('Onoff14 1'), '0' => __('Onoff14 0')];
    }

    public function getOnoff15List()
    {
        return ['1' => __('Onoff15 1'), '0' => __('Onoff15 0')];
    }

    public function getOnoff16List()
    {
        return ['1' => __('Onoff16 1'), '0' => __('Onoff16 0')];
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


    public function getOnoff11TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff11']) ? $data['onoff11'] : '');
        $list = $this->getOnoff11List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff12TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff12']) ? $data['onoff12'] : '');
        $list = $this->getOnoff12List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff13TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff13']) ? $data['onoff13'] : '');
        $list = $this->getOnoff13List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff14TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff14']) ? $data['onoff14'] : '');
        $list = $this->getOnoff14List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff15TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff15']) ? $data['onoff15'] : '');
        $list = $this->getOnoff15List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOnoff16TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff16']) ? $data['onoff16'] : '');
        $list = $this->getOnoff16List();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function componentunit()
    {
        return $this->belongsTo('ComponentUnit', 'did', 'did', [], 'LEFT')->setEagerlyType(0);
    }
}

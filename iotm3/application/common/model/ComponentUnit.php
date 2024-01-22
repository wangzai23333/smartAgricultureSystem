<?php

namespace app\common\model;

use think\Db;
use think\Model;


class ComponentUnit extends Model
{

    

    

    // 表名
    protected $name = 'component_unit';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;
    // 追加属性
    protected $append = [
        'createtime_text',
        'switch1',
        'switch2',
        'switch3',
        'switch4',
        'status'
        
    ];
    public function getStatusAttr($value, $data)
    {
        
        $sql = "SELECT  /*+ QB_NAME(QB1) NO_RANGE_OPTIMIZATION(`iot_component_unit_log`@QB1 `isexpire`) */ updatetime FROM `iot_component_unit_log` WHERE isexpire ='0' and did = '{$data['did']}'    ORDER BY updatetime DESC LIMIT 1";
        $info = $this->query($sql);
        $thistime = time();
        if(!empty($info)){
            $val = intval($thistime)- $info[0]['updatetime']/1000;
          
        if($val>= 1800){
            return  'deleted';
        }else{
            return  'success';
        }
        }else{
            return  'deleted';
        }
        
        
    }
    public function getswitch1Attr($value, $data)
    {
        
        $info = Db::name('sensor_status')->where(array('did'=>$data['did']))->find();
        return  $info['onoff1'];
    
    }
    public function getswitch2Attr($value, $data)
    {
        
        $info = Db::name('sensor_status')->where(array('did'=>$data['did']))->find();
        return  $info['onoff2'];
    }
    public function getswitch3Attr($value, $data)
    {
        
        $info = Db::name('sensor_status')->where(array('did'=>$data['did']))->find();
        return  $info['onoff3'];
    }
    public function getswitch4Attr($value, $data)
    {
        
        $info = Db::name('sensor_status')->where(array('did'=>$data['did']))->find();
        return  $info['onoff4'];
    }
    public function getCreatetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['createtime']) ? $data['createtime'] : '');
        return is_numeric($value) ? $this->msecdate($value) : $value;
    }

    







}

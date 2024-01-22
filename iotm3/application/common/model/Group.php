<?php

namespace app\common\model;

use think\Db;
use think\Model;


class Group extends Model
{

    

    

    // 表名
    protected $name = 'group';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    
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
/**

*获取所属组别的传感器id
**/
    public function getSensorIdList($groups){
        $sensorids = array();
        $sensorlist = Db::name('sensor_group')->where('groupid', 'in', $groups)->order('id','desc') ->select();
        $sensorlist = collection($sensorlist)->toArray();
        foreach ($sensorlist as $v){
            $sensorids[] = $v['sensorid'];
        }
        return $sensorids;
    }






}

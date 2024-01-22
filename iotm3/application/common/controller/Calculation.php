<?php
namespace app\common\controller;

use think\Db;
use think\exception\HttpResponseException;


class Calculation
{
    
   
    
    
    
    /**
     * 数据校准
     *
     */
    public function adjust($data,$sensorid,$type){
        $sensorAdjustModel = new \app\common\model\SensorAdjust;
        if($type == 'array'){
            foreach ($data as $k=>$v){
                $info =  $sensorAdjustModel->get(array('label'=>$k,'sensorid'=>$sensorid));
                if(!empty($info) && $info['change_value'] != 0){
                    if($info['change_type'] == 'value'){
                        $data[$k] = $v + $info['change_value'];
                    }else{
                        $data[$k] = $v + $v*($info['change_value'] * 0.01);
                    }
                }
                
            }
        }else{
        $info =  $sensorAdjustModel->get(array('label'=>$data['label'],'sensorid'=>$sensorid));
        if(!empty($info) && $info['change_value'] != 0){
            if($info['change_type'] == 'value'){
                $data['val'] = $data['val'] + $info['change_value'];
            }else{
                $data['val'] = $data['val'] + $data['val']*($info['change_value'] * 0.01);
            }
        }
        }
        return $data;
    }
    
    /**
     * 获取聚合计算数据
     *
     */
    public function getCalculationlist($calculate,$dids,$wtext,$timeunit,$timeorder,$label ='temperature'){
        if(!empty($dids)){
            $didtext = '';
            foreach ($dids as $v){
                $didtext .= "'".$v."',";
            }
            
            $didtext = rtrim($didtext,',');
            
            $wtext =   " did IN ({$didtext}) ".$wtext;
        }else{
            $wtext = ltrim($wtext,' and');
        }
        
        
        
        $timeunitText = '';
        if($timeunit !=''){
            $timeunitText = 'INTERVAL('.$timeunit.')';
        }
        
        $calculate = $calculate=='count'? $calculate.'(*)':$calculate.'(val)';
        
        $sql = "select {$calculate} as num from model_{$label}  where {$wtext} {$timeunitText} ORDER BY ts {$timeorder}  ;";
       $tdengine = new Tdengine;
       $log =  $tdengine->queryForData($sql);
      
        return $log;
    }
    
    /**
     * 获取对应组实时计算信息
     *
     */
    public function getGroupsCal($data,$groups){
        $groupModel =  new \app\common\model\Group;
        $sensorids = $groupModel->getSensorIdList($groups);
        $sensortext = implode(',',$sensorids);
 

        $where = " and sensorid in({$sensortext}) and ts > NOW - 1m";
        $log = $this->getCalculationlist($data['calculation'], [], $where,'','',$data['unitlabel']);
        if(!empty($log)){
        $val = $log[0]['num'];
        }else{
            $val = false;
        }
        return $val;
    }
    /**
     * 获取对应值
     *
     */
    public function getRange($id){
        $rangeModel = new \app\common\model\Range;
        $rangeInfo = $rangeModel->get(array('id'=>$id));
        $val = 0;
        $tdengine = new Tdengine;
        $timeCon = new ChangeTime;
        switch ($rangeInfo['rtype']){
            case '0':
                $val = 'null';
                break;
            case '1':
                $val = $rangeInfo['fixedvalue'];
                break;
            case '2':
                $sql = "select last(ts) as ts,last(val) as val,last(istext) as istext,last(content_des) as content_des from sensor_{$rangeInfo['sensorid']};";
                $info = $tdengine->queryForData($sql);
                if(!empty($info)){
                    $thistime = $timeCon->getMsectime();
                    $datatime = $timeCon->getDataFormat($info[0]['ts']);
                    $less = ($thistime - $datatime)/1000;
                    if($less > 1800){
                        $val = false;
                    }else{
                        $val = $info[0]['val'];
                    }
                }else{
                    $val = false;
                }
             
                break;
            case '3':
                $groups = array();
                $groups[0] = $rangeInfo['groupid'];
                $val = $this->getGroupsCal($rangeInfo,$groups);
                break;
        }
        return $val;
    }
}

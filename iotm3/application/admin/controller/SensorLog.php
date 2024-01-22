<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\common\controller\ChangeTime;
use app\common\controller\Tdengine;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class SensorLog extends Backend
{
    
    /**
     * SensorLog模型对象
     * @var \app\common\model\SensorLog
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\SensorLog;

    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
     
        $did = $this->request->get("did");
        //$sql = "SELECT DISTINCT  s.`label`,l.title FROM  `iot_unit_sensor` s LEFT JOIN `iot_sensor_list` l ON l.`id` = s.`sensorid` WHERE l.`did` = '{$did}' ";
        $sql = "SELECT label,id,title FROM `iot_sensor_list` WHERE did = '{$did}'";
        $sensorList = $this->model ->query($sql);
        $this->view->assign("did", $did);
        $labels = array();
        $todayData = array();
        $tdengine = new Tdengine();
        $time = new ChangeTime();
        $today = strtotime(date("Y-m-d"),time());
        $today = $today * 1000;
        foreach ($sensorList as $k=>$v){
            $sensorids[$k] = $v['id'];
            if($v['label']!='person'){
                $sql = "select avg(val) as val from sensor_{$v['id']} where  ts < now and ts > {$today} INTERVAL(1h) ;";
            }else{
                $sql = "select count(*) as val from sensor_{$v['id']} where  ts < now and ts > {$today} and val = 1 INTERVAL(1h) ;";
            }
            $data = $tdengine->queryForData($sql);
            foreach ($data as $s=>$val){
                $valtime = $time->getDataFormat($val['ts']);
                $hour = intval($time->msecOwnDate($valtime,'H'));
                $todayData[$v['id']][$hour] = $val['val'];
            }
        }
       
        $todayText = array();
        $data = [];
        foreach ($sensorids as $k=>$v){
        for($i=0;$i<25;$i++){
            if(empty($todayData[$v][$i])){
                $data[$v][$i] = 0;
            }else{
                $data[$v][$i] = $todayData[$v][$i];
            }
        }
        $todayText[$v] = implode(',',   $data[$v]);
        } 
       
        $sensorText = implode(',', $sensorids);
        $this->view->assign("sensorList", $sensorList);
        $this->view->assign("todayText", $todayText);
        $this->view->assign("sensorText", $sensorText);
        return $this->view->fetch();
    }
    
    public function to_index(){
        $unit = new \app\common\model\ComponentUnit;
        $units = $unit->select();
        
        $units =  collection($units)->toArray();
        
        $unitlist = array();
        foreach ($units as $v){
            $unitlist[$v['did']] = $v['title'];
        }
        $this->view->assign("unitlist", $unitlist);
        return $this->view->fetch();
    }

}

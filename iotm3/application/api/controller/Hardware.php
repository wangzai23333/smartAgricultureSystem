<?php

namespace app\api\controller;

use Complex\Exception;
use app\common\controller\Api;
use app\common\controller\Gizwits;
use app\common\controller\ChangeTime;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\common\controller\Examine;
use app\common\controller\Calculation;
use app\common\controller\Emqx;
/**
 * 硬件等相关对外接口
 */
class Hardware extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    
    
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST,GET,OPTIONS');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        header("Content-Type: application/json; charset=UTF-8");
        header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
        
        $this->air = new \app\common\model\Air;
        $this->airLog = new \app\common\model\AirLog;
        $this->deh = new \app\common\model\Dehumidifiers;
        $this->dehLog = new \app\common\model\DehumidifiersLog;
    }
    
    /**
     * 获取空调最新数据
     *
     */
    public function getAirLatestInfo(){
       $token = $this->request->post('token');
        $idsText = $this->request->post('hids');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $where = '';
        if(!empty($idsText)){
            $hids = explode(",", $idsText);
            if(empty($hids)){
                $this->error('硬件编号有误');
            }
        }else{
            $sql = "SELECT hid FROM `iot_air` ";
            $airList = $this->air->query($sql);
            $hids = array();
            foreach ($airList as $v){
                $hids[] = $v['hid'];
            }
            
        }
        $list = array();
        foreach ($hids as $v){
            $sql = "SELECT a.did,a.num,l.hid,l.onoff,l.env_temp AS envTemp,l.set_temp AS setTemp,l.workmode AS workMode,l.fanvol AS fanVol FROM `iot_air_log` l LEFT JOIN `iot_air` a ON a.`id` = l.`aid`  WHERE l.hid = {$v} ORDER BY l.createtime DESC  LIMIT 1";
            $info = $this->airLog->query($sql);
            $statusInfo = $this->air->get(array('hid'=>$v));
            $info[0]['onoff'] = $statusInfo['status'];
            $list[] = $info[0];
            
        }
        
        $this->success('查询成功',$list);
        
    }
    
    
    /**
     * 获取抽湿机最新数据
     *
     */
    public function getDecHumLatestInfo(){
        $token = $this->request->post('token');
        $idsText = $this->request->post('hids');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        if(!empty($idsText)){
            $hids = explode(",", $idsText);
            if(empty($hids)){
                $this->error('硬件编号有误');
            }
        }else{
            $sql = "SELECT hid FROM `iot_dehumidifiers` ";
            $dehList = $this->deh->query($sql);
            $hids = array();
            foreach ($dehList as $v){
                $hids[] = $v['hid'];
            }
            
        }
        $list = array();
        foreach ($hids as $v){
            $sql = "SELECT d.`did`,d.`num`,l.hid,l.onoff,l.env_temp AS envTemp,l.env_humi AS envHumi,l.set_temp AS setTemp,l.set_humi AS setHumi FROM `iot_dehumidifiers_log` l LEFT JOIN `iot_dehumidifiers` d ON l.`dhid` = d.`id` WHERE l.hid = {$v} ORDER BY l.createtime DESC  LIMIT 1";
            $info = $this->dehLog->query($sql);
            $statusInfo = $this->deh->get(array('hid'=>$v));
            $info[0]['onoff'] = $statusInfo['status'];
            $list[] = $info[0];
            
        }
        
        $this->success('查询成功',$list);
        
    }
    
    /**
     * 空调开关控制
     *
     */
    public function airOperation(){
        $token = $this->request->post('token');
        $did = $this->request->post('did');
        $num = $this->request->post('num');
        $setTemp = $this->request->post('setTemp');
        $setWorkMode = $this->request->post('setWorkMode');
        $setFanVol = $this->request->post('setFanVol');
        $onoff = $this->request->post('onoff');
        $way = $this->request->post('way');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $airModel = new \app\common\model\Air;
        $time = new ChangeTime();
        
        $sensor = $examine->checkUnit($did, $member['id']);
        if(intval($num) <=0 || intval($num)>4){
            $this->error('编号有误');
        }
        if(!empty($way) && ($way != 'app' && $way != 'client')){
            $this->error('操作方式有误');
        }
        if(empty($way)){
            $way = 'app';
        }
        $info = $airModel->get(array('did'=>$did,'num'=>$num));
        if(empty($info)){
            $this->error('该设备不存在');
        }
        if($onoff!='on' && $onoff!='off'){
            $this->error('状态有误');
        }
        if($info['is_run'] == 'on'){
            $this->error('请勿多次请求');
        }
        
        
        if(!empty($setTemp)){
            if($setTemp<10 || $setTemp>=40){
                $this->error('请检查温度设置是否过低或者过高');
            }
        }else {
            $this->error('温度设定不能为空');
        }
        $setWorkMode = intval($setWorkMode);
        if($setWorkMode<0 || $setWorkMode>2){
            $this->error('空调机设定的工作模式有误');
        }
        
        $setFanVol = intval($setFanVol);
        if($setFanVol<0 || $setFanVol>5){
            $this->error('空调机设定的工作风量有误');
        }
        $airModel->update(array('is_run'=>'on'),array('id'=>$info['id']));
        $emqx = new Emqx();
        Db::startTrans();
      
            //向设备发送信息
            $getTopic = 'YHM/Rs485ToEth/ServerCmd/Ack/'.$info['dev_uid'];
            $sendTopic = 'YHM/Rs485ToEth/ServerCmd/'.$info['dev_uid'];
            $sendData =array();
            $sendData['Head'] = array(
                'Symbol'=>'YHM',
                'MsgId' =>$emqx->getRandromStr(),
                'CmdStr' =>'SetMachinePara'
            );
            $onoffSwitch = $onoff=='on'?'On':'Off';
            $sendData['Body'] = array(
                'DevUID'=>$info['dev_uid'],
                'CentralAirc' => array(0=>array(
                    'Id' => $info['hid'],
                    'SetTemp' => $setTemp,
                    'SetWorkMode' => $setWorkMode,
                    'SetFanVol' => $setFanVol,
                    'SetOnoff' =>$onoffSwitch
                ))
            );
            switch ($setWorkMode){
                case 0:
                    $workText = '抽风模式';
                    break;
                case 1:
                    $workText = '制热模式';
                        break;
                case 2:
                    $workText = '制冷模式';
                        break;
            }
            if($setFanVol == 0){
                $fanVolText = '自动';
            }else{
                $fanVolText = $setFanVol.'级';
            }
            
            $sendMeg = json_encode($sendData);
            $r = $emqx->subscribeForBack($getTopic, $sendTopic, $sendMeg);
            $res = json_decode($r,true);
            if($res['Body']['ResultCode'] == 0 || !empty($res['Body']['DevUID'])){
                if($info['status'] == $onoff){
                Db::name('status_log')->insert(array(
                    'did' => $did,
                    'type' => 2,
                    'number' => $num,
                    'status' => $onoff,
                    'way' => $way,
                    'operation'=>'温度设置为'.$setTemp.'℃ 工作模式设置为'.$workText.' 风力为'.$fanVolText,
                    'createtime' => $time->getMsectime()
                ));
                
                }else{
                    Db::name('status_log')->insert(array(
                        'did' => $did,
                        'number' => $num,
                        'status' => $onoff,
                        'way' => $way,
                        'operation'=>'温度设置为'.$setTemp.'℃ 工作模式设置为'.$workText.' 风力为'.$fanVolText,
                        'createtime' => $time->getMsectime()
                    ));
                    $airModel->update(array('status'=>$onoff,'updatetime'=>$time->getMsectime()),array('id'=>$info['id']));
                }
                $airModel->update(array('is_run'=>'off'),array('id'=>$info['id']));
                try {
                //手动操作之后，正要运行的联动任务全部取消
                $taskModel = new \app\common\model\Task;
                $taskModel->query(" UPDATE /*+ QB_NAME(QB1) NO_RANGE_OPTIMIZATION(`iot_task`@QB1 `status`, `isinvalid`) */`iot_task` SET    `isInvalid`='1' WHERE  `isInvalid` = '0' AND `did` = '{$did}' AND `switchnum` = {$num} AND `status` = '0' ");
                
                Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                $this->success('设置成功');
            }else{
                $airModel->update(array('is_run'=>'off'),array('id'=>$info['id']));
              //  Db::commit();
                $this->error('设置失败');
            }
            
            
       
    }
    
    
    /**
     * 抽湿机开关控制
     *
     */
    public function decHumOperation(){
        $token = $this->request->post('token');
        $did = $this->request->post('did');
        $num = $this->request->post('num');
        $setHumi = $this->request->post('setHumi');
        $setTemp = $this->request->post('setTemp');
        $onoff = $this->request->post('onoff');
        $way = $this->request->post('way');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $dehModel = new \app\common\model\Dehumidifiers;
        $dehLogModel = new \app\common\model\DehumidifiersLog;
        $time = new ChangeTime();
        
        $sensor = $examine->checkUnit($did, $member['id']);
        if(intval($num) <=0 || intval($num)>4){
            $this->error('编号有误');
        }
        if(!empty($way) && ($way != 'app' && $way != 'client')){
            $this->error('操作方式有误');
        }
        if(empty($way)){
            $way = 'app';
        }
        
        $info = $dehModel->get(array('did'=>$did,'num'=>$num));
        if(empty($info)){
            $this->error('该设备不存在');
        }
        if($onoff!='on' && $onoff!='off'){
            $this->error('状态有误');
        }
        if($info['is_run'] == 'on'){
            $this->error('请勿多次请求');
        }
        if(!empty($setHumi)){
            if($setHumi<10 || $setHumi>=100){
                $this->error('请检查湿度设置是否过低或者过高');
            }
        }else {
            $this->error('湿度设定不能为空');
        }
        if(!empty($setTemp)){
            if($setTemp<10 || $setTemp>=40){
                $this->error('请检查温度设置是否过低或者过高');
            }
        }else {
            $this->error('温度设定不能为空');
        }
        
        $emqx = new Emqx();
      
        $dehModel->update(array('is_run'=>'on'),array('id'=>$info['id']));
        Db::startTrans();
        try {
            //向设备发送信息
            $getTopic = 'YHM/Rs485ToEth/ServerCmd/Ack/'.$info['dev_uid'];
            $sendTopic = 'YHM/Rs485ToEth/ServerCmd/'.$info['dev_uid'];
            $sendData =array();
            $sendData['Head'] = array(
                'Symbol'=>'YHM',
                'MsgId' =>$emqx->getRandromStr(),
                'CmdStr' =>'SetMachinePara'
            );
            $onoffSwitch = $onoff=='on'?'On':'Off';
            $sendData['Body'] = array(
                'DevUID'=>$info['dev_uid'],
                'DehumiDevs' => array(0=>array(
                    'Id' => $info['hid'],
                    'SetTemp' => $setTemp,
                    'SetHumi' => $setHumi,
                    'SetOnoff' =>$onoffSwitch
                ))
            );
            $sendMeg = json_encode($sendData);
            $r = $emqx->subscribeForBack($getTopic, $sendTopic, $sendMeg);
            $res = json_decode($r,true);
            if($res['Body']['ResultCode'] == 0 || !empty($res['Body']['DevUID'])){
                if($info['status'] == $onoff){
                Db::name('status_log')->insert(array(
                    'type'=>2,
                    'did' => $did,
                    'number' => $num,
                    'status' => $onoff,
                    'way' => $way,
                    'operation'=>'温度设置为'.$setTemp.'℃ 湿度设置为'.$setHumi.'%',
                    'createtime' => $time->getMsectime()
                ));
                }else{
                    Db::name('status_log')->insert(array(
                        'did' => $did,
                        'number' => $num,
                        'status' => $onoff,
                        'way' => $way,
                        'operation'=>'温度设置为'.$setTemp.'℃ 湿度设置为'.$setHumi.'%',
                        'createtime' => $time->getMsectime()
                    ));
                $dehModel->update(array('status'=>$onoff,'updatetime'=>$time->getMsectime()),array('id'=>$info['id']));
                }
                
                $dehModel->update(array('is_run'=>'off'),array('id'=>$info['id']));
             
                //手动操作之后，正要运行的联动任务全部取消
                $taskModel = new \app\common\model\Task;
                $taskModel->query(" UPDATE /*+ QB_NAME(QB1) NO_RANGE_OPTIMIZATION(`iot_task`@QB1 `status`, `isinvalid`) */`iot_task` SET    `isInvalid`='1' WHERE  `isInvalid` = '0' AND `did` = '{$did}' AND `switchnum` = {$num} AND `status` = '0' ");
                
                Db::commit();
                $this->success('设置成功');
            }else{
                $dehModel->update(array('is_run'=>'off'),array('id'=>$info['id']));
                Db::commit();
                $this->error('设置失败');
            }
            
            
        } catch (ValidateException $e) {
            Db::rollback();
            $this->error($e->getMessage());
        } catch (PDOException $e) {
            Db::rollback();
            $this->error($e->getMessage());
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
    }
    
    public function getInfo(){
        $devId = $this->request->get('devId');
        if(empty($devId)){
            $this->error('网关序号不能为空');
        }
        $emqx = new Emqx();
        $getTopic = 'YHM/Rs485ToEth/ServerCmd/Ack/'.$devId;
        $sendTopic = 'YHM/Rs485ToEth/ServerCmd/'.$devId;
        $sendData =array();
        $sendData['Head'] = array(
            'Symbol'=>'YHM',
            'MsgId' =>$emqx->getRandromStr(),
            'CmdStr' =>'GetMachineInfo'
        );
        $sendData['Body'] = array(
            'DevUID'=>$devId
        );
        $sendMeg = json_encode($sendData);
        $r = $emqx->subscribeForBack($getTopic, $sendTopic, $sendMeg);
        $r = json_decode($r,true);
       
        $emqx->toInputMeg($r);
        $this->success('获取成功');
    }
    
}

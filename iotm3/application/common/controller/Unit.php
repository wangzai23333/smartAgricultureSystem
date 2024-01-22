<?php
namespace app\common\controller;

use app\common\library\Check;
use app\common\library\SignatureHelper;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use Complex\Exception;
use fast\Arr;


class Unit
{
    
    /**
     * 控制开关
     */
    public function onoff($did,$number,$status,$runTime,$way='other',$taskValue = 0)
    {
        $client = new WebSocketClient;
        $time = new ChangeTime();
        $linkageModel = new \app\common\model\Linkage;
        $sql = " SELECT mid FROM `iot_member_unit` u LEFT JOIN  `iot_member` m  ON m.id = u.`mid` WHERE u.did = '{$did}' AND m.`member_type` = 'unit' ";
        $memberList = $linkageModel->query($sql);
        $uid = array();
        $addSql = '';
        if(empty($memberList[0])){
            return false;
        }else{
            //添加任务
            $thisTime = $time->getMsectime();
            $runTime = $runTime;
            //获取失效任务
            $sql = "SELECT id FROM `iot_run_task` WHERE did = '{$did}' AND number = {$number} AND cancel = '0' AND runTime > {$thisTime} ";
            $taskList = $linkageModel->query($sql);
           //失效处理
            $tids = array();
            $updateText = '';
            foreach ($taskList as $i=>$v){
                $tids[$i] = $v['id'];
                $updateText.=$v['id'].',';
            }
            $updateText = rtrim($updateText,',');
            $tcount = sizeof($tids);
            if($tcount>0){
            try {
                $sql = "UPDATE `iot_run_task` SET cancel = '1' WHERE id IN($updateText)";
                $r = $linkageModel->query($sql);
            }catch (ValidateException $e) {
                Db::rollback();
            } catch (PDOException $e) {
                Db::rollback();
            } catch (Exception $e) {
                Db::rollback();
            }
            }
            //添加新任务
            Db::name('run_task')->insert(array(
                'did' => $did,
                'number' => $number,
                'onoff' => intval($status),
                'runTime' => $runTime,
                'way' => $way,
                'tvid' => $taskValue
            ));
            $taskId =  Db::name('run_task')->getLastInsID();
        foreach ($memberList as $i=>$v){
            $uid[$i] = $v['mid'];
            $addSql .= "({$taskId},{$v['mid']}),";
        }
        $addSql = rtrim($addSql,',');
        $count = sizeof($uid);
        if($count<=0){
            return false;
        }else{
        $sql = "INSERT INTO `iot_run_task_member`(taskid,userid) VALUE $addSql";
        $r = $linkageModel->query($sql);

        $sendInfo = array();
        //发送任务
        $emqx = new Emqx();
        //开关操作前，取消已有任务
        if($tcount>0){
            //向设备发送信息
            $sendTopic = 'UNIT/toReceive/'.$did;
            $sendData =array();
            $sendData['head'] = array(
                'symbol'=>'IOTM',
                'msgId' =>$emqx->getRandromStr(),
                'cmd' =>'toCancel'
            );
            $sendData['body'] = array(
                'taskId' => $tids,
                'linkageTaskId' => []
            );
            $sendMeg = json_encode($sendData);
            $emqx->subscribeForSend($sendTopic, $sendMeg);
        }
        //开关操作
        $sendTopic = 'UNIT/toReceive/'.$did;
        $sendData =array();
        $sendData['head'] = array(
            'symbol'=>'IOTM',
            'msgId' =>$emqx->getRandromStr(),
            'cmd' =>'toControl'
        );
        $sendData['body'] = array(
            'onoff' => intval($status),
            'number' => $number,
            'runTime' => $runTime,
            'taskId' => $taskId
            
        );
        $sendMeg = json_encode($sendData);
        $emqx->subscribeForSend($sendTopic, $sendMeg);
        return true;
        }
        
        }
        
    }
    
    
    
}

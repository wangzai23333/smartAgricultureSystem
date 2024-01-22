<?php

namespace app\api\controller;

use Complex\Exception;
use app\common\controller\Api;
use app\common\controller\Gizwits;
use app\common\controller\ChangeTime;
use app\common\controller\WebSocketClient;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\common\controller\Examine;
use app\common\controller\Calculation;
use app\common\controller\Emqx;
/**
 * 联动相关对外接口
 */
class Linkage extends Api
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
    }
    
    /**
     * 添加联动
     */
    public function addLinkage(){
        $token = $this->request->post('token');
        $title = $this->request->post('title');
        $did = $this->request->post('did');
        $unitLabel = $this->request->post('unitLabel');
        $min = $this->request->post('min');
        $max = $this->request->post('max');
        $switchnum = $this->request->post('switchNum');
        $onoff = $this->request->post('onoff');
        $forbidden = $this->request->post('forbidden');
        $keeptime = $this->request->post('keepTime');
        $delaytime = $this->request->post('delayTime');
        $calculation = $this->request->post('calculation');
        $operationWay = $this->request->post('operationWay');
        $sensorTitle = $this->request->post('sensorTitle');
        $sensorPort = $this->request->post('sensorPort');
        //检查
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $sensor = $examine->checkUnit($did, $member['id']);
        if(empty($switchnum) || $switchnum>16 || $switchnum<1){
            $this->error('开关序号有误');
        }
        if($onoff!='on' && $onoff!='off'){
            $this->error('开关状态有误');
        }
        if(empty($title)){
            $this->error('标题不能为空');
        }
        if(!empty($operationWay) && $operationWay!='local' && $operationWay!='task'){
            $this->error('运行方式设置有误');
        }
        if(empty($operationWay)){
            $operationWay = 'polling';
        }
        $calculation = 'avg';
       
        if($keeptime<0){
            $this->error('保持时间有误');
        }
        if($delaytime<0){
            $this->error('延时时间有误');
        }
        if($min<0){
            $this->error('最小值有误');
        }
        if($max<0){
            $this->error('最大值有误');
        }
       
        if($forbidden!=0 && $forbidden!=1){
            $this->error('授权禁止有误');
        }
        
        $sensorListModel = new \app\common\model\SensorList;
        if(empty($sensorTitle)){
            $this->error('传感器标题不能为空');
        }
        if(!empty($sensorPort)){
            $sensorInfo = $sensorListModel->get(array('did'=>$did,'port'=>$sensorPort,'title' => $sensorTitle,'label'=>$unitLabel));
        }else{
            $sensorInfo = $sensorListModel->get(array('did'=>$did,'title' => $sensorTitle,'label'=>$unitLabel));
        }
          
        if(empty($sensorInfo)){
            $this->error('该组建单元并没绑定该传感器');
        }
        if($operationWay == 'local'){
            if($min<=0 || $max<=0  ){
                $this->error('本地模式不支持最大值最小值为无限值');
            }
            if($delaytime >0){
                $this->error('本地模式延迟时长为0');
            }
        }
        
        $time = new ChangeTime();
            $data = array(
                'rtype' => 2,
                'did' => $did,
                'sensorid' => $sensorInfo['id'],
                'calculation' => $calculation,
                'unitlabel' => $unitLabel,
                'createtime' => $time->getMsectime()
            );
            Db::name('range')->insert($data);
            $referenceid = Db::name('range')->getLastInsID();
            if($min>0){
            $data = array(
                'rtype' => 1,
                'fixedvalue' => $min,
                'unitlabel' => $unitLabel,
                'createtime' => $time->getMsectime()
            );
            }else{
                $data = array(
                    'rtype' => 0,
                    'createtime' => $time->getMsectime()
                );
            }
            
            Db::name('range')->insert($data);
            $minid =  Db::name('range')->getLastInsID();
           
                $data = array(
                    'rtype' => 1,
                    'fixedvalue' => $max,
                    'unitlabel' => $unitLabel,
                    'createtime' => $time->getMsectime()
                );
            Db::name('range')->insert($data);
            $maxid =  Db::name('range')->getLastInsID();
          
            $data = array(
                'title' => $title,
                'referenceid' => $referenceid,
                'did'=>$did,
                'minid' => $minid,
                'maxid' => $maxid,
                'switchnum' => $switchnum,
                'onoff' => $onoff,
                'delaytime' => floatval($delaytime),
                'createtime' => $time->getMsectime(),
                'keeptime' => floatval($keeptime),
                'forbidden' => intval($forbidden),
                'operationWay' =>$operationWay,
                'mid'=>$member['id']
                
            );
            Db::name('linkage')->insert($data);
            $addId =  Db::name('linkage')->getLastInsID();
            if($addId>0){                
                //如是任务模式，需发送检查任务
                if($data['operationWay'] == 'task' && $data['forbidden'] == 0){
                    $client = new WebSocketClient;
                    $client->connect('127.0.0.1', '8282', '/');
                    $sendData = array();
                    $sendData['cmd'] = 'addTask';
                    $sendData['data'] = array(
                        'title' => 'cherkLinkage'.$addId,
                        'url' => $_SERVER['SERVER_NAME'].'/api/task/checkLinkage',
                        'option'=>array(
                            'id'=>$addId
                        ),
                        'time'=>$data['delaytime'] * 60,
                        'persistent'=>1,
                        'checkUrl' => $_SERVER['SERVER_NAME'].'/api/task/checkLinkageTask?id='.$addId
                    );
                    $sendText = json_encode($sendData);
                    $rs = $client->sendData($sendText);
                    $client->disconnect();
                }
                //如是本地模式，需发送检查任务
                if($data['operationWay'] == 'local' && $data['forbidden'] == 0){
                    $emqx = new Emqx();
                    //向设备发送信息
                    $sendTopic = 'UNIT/toReceive/'.$did;
                    $sendData =array();
                    $sendData['head'] = array(
                        'symbol'=>'IOTM',
                        'msgId' =>$emqx->getRandromStr(),
                        'cmd' =>'toLinkage'
                    );
                    $sensorModel = new \app\common\model\SensorList;
                    $sensor = $sensorModel->get(array('id'=>$sensorInfo['id']));
                    $ronoff = $onoff =='on' ? 1:0;
                    $sendData['body'] = array(
                        'taskId' => 'linkage_'.$addId,
                        'minVal' => $min,
                        'maxVal' => $max,
                        'label' => $unitLabel,
                        'port' => $sensorInfo['port'],
                        'sensorTitle' => $sensorInfo['title'],
                        'onoff' => $ronoff,
                        'switchNum' => $switchnum,
                        'keeptime' => $keeptime,
                        'delaytime' => $delaytime
                    );
                    $sendMeg = json_encode($sendData);
                    $emqx->subscribeForSend($sendTopic, $sendMeg);
                }
                
                $this->success('添加成功',array('addId'=>$addId));
            }else{
                $this->error('添加失败');
            }   
        
    }
       
    /**
     * 修改联动
     */
    public function updateLinkage(){
        $token = $this->request->post('token');
        $id = $this->request->post('id');
        $title = $this->request->post('title');
        $did = $this->request->post('did');
        $unitLabel = $this->request->post('unitLabel');
        $min = $this->request->post('min');
        $max = $this->request->post('max');
        $switchnum = $this->request->post('switchNum');
        $onoff = $this->request->post('onoff');
        $forbidden = $this->request->post('forbidden');
        $keeptime = $this->request->post('keepTime');
        $delaytime = $this->request->post('delayTime');
        $calculation = $this->request->post('calculation');
        $operationWay = $this->request->post('operationWay');
        $sensorTitle = $this->request->post('sensorTitle');
        $sensorPort = $this->request->post('sensorPort');
        //检查
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $sensor = $examine->checkUnit($did, $member['id']);
        $linkageModel = new \app\common\model\Linkage;
        $info = $linkageModel->get(array('id'=>$id));
        if(empty($info)){
            $this->error('该联动不存在');
        }
        $data = array();
        if(empty($switchnum) || $switchnum>16 || $switchnum<1){
            $this->error('开关序号有误');
        }
        if($onoff!='on' && $onoff!='off'){
            $this->error('开关状态有误');
        }
        if(!empty($operationWay) && $operationWay!='local' && $operationWay!='task'){
            $this->error('运行方式设置有误');
        }
        if(!empty($title)){
            $data['title'] = $title;
        }
        $calculation = 'avg';
        
        if(empty($operationWay)){
            $operationWay = 'polling';
        }
        if($keeptime<0){
            $this->error('保持时间有误');
        }
        if($delaytime<0){
            $this->error('延时时间有误');
        }
        $data['keeptime'] = $keeptime;
        $data['delaytime'] = intval($delaytime);
        // if($min<0){
        //     $this->error('最小值有误');
        // }
        if($max<0){
            $this->error('最大值有误');
        }
        
        if($forbidden!=0 && $forbidden!=1){
            $this->error('授权禁止有误');
        }
       
       
        $sensorListModel = new \app\common\model\SensorList;
        if(empty($sensorTitle)){
            $this->error('传感器标题不能为空');
        }
       if(!empty($sensorPort)){
            $sensorInfo = $sensorListModel->get(array('did'=>$did,'port'=>$sensorPort,'title' => $sensorTitle,'label'=>$unitLabel));
        }else{
        $sensorInfo = $sensorListModel->get(array('did'=>$did,'title' => $sensorTitle,'label'=>$unitLabel));
        }
            if(empty($sensorInfo)){
                $this->error('该组建单元并没绑定该传感器');
            }
            if($operationWay == 'local'){
                if($min==0 || $max==0  ){
                    $this->error('本地模式不支持最大值最小值为无限值');
                }
                if($delaytime >0){
                    $this->error('本地模式延迟时长为0');
                }
            }
        $rangeModel = new \app\common\model\Range;
        $time = new ChangeTime();
       
            $rdata = array(
                'rtype' => 2,
                'did' => $did,
                'sensorid' => $sensorInfo['id'],
                'calculation' => $calculation,
                'unitlabel' => $unitLabel,
            );
            $rangeModel->update($rdata,array('id'=>$info['referenceid']));
            if($min>0){
                $rdata = array(
                    'rtype' => 1,
                    'fixedvalue' => $min,
                    'unitlabel' => $unitLabel,
                );
            }else{
                $rdata = array(
                    'rtype' => 0,
                );
            }
            
            $rangeModel->update($rdata,array('id'=>$info['minid']));
            
                $rdata = array(
                    'rtype' => 1,
                    'fixedvalue' => $max,
                    'unitlabel' => $unitLabel,
                    'createtime' => $time->getMsectime()
                );
            $rangeModel->update($rdata,array('id'=>$info['maxid']));
            
            if(!empty($operationWay)){
                $data['operationWay'] = $operationWay;
            }
            $data['forbidden'] = intval($forbidden);
            $data['switchnum'] = $switchnum;
            $data['onoff'] = $onoff;
            Db::startTrans();
            try {
            $linkageModel->update($data,array('id'=>$id));
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
      
        if($info['operationWay'] == 'task' && $info['forbidden'] == 0){
            $client = new WebSocketClient;
            $client->connect('127.0.0.1', '8282', '/');
            $sendData = array();
            $sendData['cmd'] = 'delTask';
            $sendData['data'] = array(
                'title' => 'cherkLinkage'.$info['id'],
            );
            $sendText = json_encode($sendData);
            $rs = $client->sendData($sendText);
            
            $sendData = array();
            $sendData['cmd'] = 'delTask';
            $sendData['data'] =array(
                'title' =>  'task_'.$did.$switchnum
                
            );
            $sendText = json_encode($sendData);
            $rs = $client->sendData($sendText);
            $client->disconnect();
        }
        $emqx = new Emqx();
        if($info['operationWay'] == 'local' && $info['forbidden'] == 0){
            //向设备发送信息
            $sendTopic = 'UNIT/toReceive/'.$did;
            $sendData =array();
            $sendData['head'] = array(
                'symbol'=>'IOTM',
                'msgId' =>$emqx->getRandromStr(),
                'cmd' =>'toCancel'
            );
            $linkageId = array();
            $linkageId[0] = 'linkage_'.$info['id'];
            $thistime = $time->getMsectime();
            $sql = "SELECT id FROM `iot_run_task` WHERE lid = {$info['id']} AND cancel = '0' AND runTime>{$thistime}";
            $idList = $rangeModel->query($sql);
            $tids = array();
            $updateText = '';
            foreach ($idList as $k=>$v){
                $tids[$k] = $v['id'];
                $updateText.=$v['id'].',';
            }
            $updateText = rtrim($updateText,',');
            if($updateText!=''){
            $sql = "UPDATE `iot_run_task` SET cancel = '1' WHERE id IN($updateText)";
            $r = $rangeModel->query($sql);
            }
            $sendData['body'] = array(
                'taskId' => $tids,
                'linkageTaskId' => $linkageId
            );
            $sendMeg = json_encode($sendData);
            $emqx->subscribeForSend($sendTopic, $sendMeg);
        }
        //如是任务模式，需发送检查任务
        if($data['operationWay'] == 'task'  &&  $data['forbidden']== 0){
            $client = new WebSocketClient;
            $client->connect('127.0.0.1', '8282', '/');
            $sendData = array();
            $sendData['cmd'] = 'addTask';
            $sendData['data'] = array(
                'title' => 'cherkLinkage'.$info['id'],
                'url' => $_SERVER['SERVER_NAME'].'/api/task/checkLinkage',
                'option'=>array(
                    'id'=>$info['id']
                ),
                'time'=>$data['delaytime'] * 60,
                'persistent'=>1,
                'checkUrl' => $_SERVER['SERVER_NAME'].'/api/task/checkLinkageTask?id='.$info['id']
            );
            $sendText = json_encode($sendData);
            $rs = $client->sendData($sendText);
            $client->disconnect();
        }
      
        
        if($operationWay == 'local'  &&  $data['forbidden']== 0){
            //向设备发送信息
            $sendTopic = 'UNIT/toReceive/'.$did;
            $sendData =array();
            $sendData['head'] = array(
                'symbol'=>'IOTM',
                'msgId' =>$emqx->getRandromStr(),
                'cmd' =>'toLinkage'
            );
            $sensorModel = new \app\common\model\SensorList;
            $sensor = $sensorModel->get(array('id'=>$sensorInfo['id']));
            $ronoff = $onoff =='on' ? 1:0;
            $sendData['body'] = array(
                'taskId' => 'linkage_'.$info['id'],
                'minVal' => $min,
                'maxVal' => $max,
                'label' => $unitLabel,
                'port' => $sensorInfo['port'],
                'sensorTitle' => $sensorInfo['title'],
                'onoff' => $ronoff,
                'switchNum' => $switchnum,
                'keeptime' => $keeptime,
                'delaytime' => $delaytime
            );
            $sendMeg = json_encode($sendData);
            $emqx->subscribeForSend($sendTopic, $sendMeg);
            
        }
        
        $this->success('修改成功');
        
    }
    
    /****
     * 
     * 删除联动
     * 
     * ***/
    public function delLinkage(){
        $id = $this->request->post('id');
        $token = $this->request->post('token');
        //检查
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $linkageModel = new \app\common\model\Linkage;
        $rangeModel = new \app\common\model\Range;
        $info = $linkageModel->get(array('id'=>$id));
        if(empty($info)){
            $this->error('该联动不存在');
        }
       
        if($info['operationWay'] == 'task' && $info['forbidden'] == 0){
            $client = new WebSocketClient;
            $client->connect('127.0.0.1', '8282', '/');
            $sendData = array();
            $sendData['cmd'] = 'delTask';
            $sendData['data'] = array(
                'title' => 'cherkLinkage'.$info['id'],
            );
            $sendText = json_encode($sendData);
            $rs = $client->sendData($sendText);
            $client->disconnect();
        }
        if($info['operationWay']  == 'local' && $info['forbidden'] == 0){
            $emqx = new Emqx();
            $range = $rangeModel->get(array('id'=>$info['referenceid']));
            //向设备发送信息
            $sendTopic = 'UNIT/toReceive/'.$range['did'];
            $sendData =array();
            $sendData['head'] = array(
                'symbol'=>'IOTM',
                'msgId' =>$emqx->getRandromStr(),
                'cmd' =>'toCancel'
            );
            $linkageId = array();
            $linkageId[0] = 'linkage_'.$info['id'];
            $time = new ChangeTime();
            $thistime = $time->getMsectime();
            $sql = "SELECT id FROM `iot_run_task` WHERE lid = {$info['id']} AND cancel = '0' AND runTime>{$thistime}";
            $idList = $rangeModel->query($sql);
            $updateText = '';
            $tids = array();
            foreach ($idList as $k=>$v){
                $tids[$k] = $v['id'];
                $updateText.=$v['id'].',';
            }
            $updateText = rtrim($updateText,',');
            if($updateText!=''){
            $sql = "UPDATE `iot_run_task` SET cancel = '1' WHERE id IN($updateText)";
            $r = $rangeModel->query($sql);
            }
            $sendData['body'] = array(
                'taskId' => $tids,
                'linkageTaskId' => $linkageId
            );
            $sendMeg = json_encode($sendData);
            $emqx->subscribeForSend($sendTopic, $sendMeg);
        }
        $r = $rangeModel->where(array('id'=>$info['referenceid']))->delete();
        $r = $rangeModel->where(array('id'=>$info['minid']))->delete();
        $r = $rangeModel->where(array('id'=>$info['maxid']))->delete();
        $r = $linkageModel->where(array('id'=>$id))->delete();
        if(!empty($r)){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
        
    }
       
    /****
     * 
     * 查询联动
     * 
     * ***/
    public function getLinkageInfo(){
        $id = $this->request->post('id');
        $token = $this->request->post('token');
        //检查
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $linkageModel = new \app\common\model\Linkage;
        $rangeModel = new \app\common\model\Range;
        $info = $linkageModel->get(array('id'=>$id));
        if(empty($info)){
            $this->error('该联动不存在');
        }
        
        $refereceInfo = $rangeModel->get(array('id'=>$info['referenceid']));
        $minInfo = $rangeModel->get(array('id'=>$info['minid']));
        $maxInfo = $rangeModel->get(array('id'=>$info['maxid']));
        $max = $maxInfo['rtype'] == 0?0:$maxInfo['fixedvalue'];
        $min = $minInfo['rtype'] == 0?0:$minInfo['fixedvalue'];
        $sensorListModel = new \app\common\model\SensorList;
       
        $sensorInfo = $sensorListModel->get(array('id'=>$refereceInfo['sensorid']));
        $reData = array(
            'title' => $info['title'],
            'did' => $refereceInfo['did'],
            'unitLabel' =>$refereceInfo['unitlabel'],
            'sensorTitle' =>$sensorInfo['title'],
            'sensorPort' =>$sensorInfo['port'],
            'min' => $min,
            'max' => $max,
            'onoff' => $info['onoff'],
            'switchNum' => $info['switchnum'],
            'delayTime' => $info['delaytime'],
            'forbidden' => $info['forbidden'],
            'keepTime' => $info['keeptime'],
            'calculation' => $refereceInfo['calculation'],
            'operationWay' => $info['operationWay']
        );
        
        $this->success('获取成功',$reData);
    }
   
    public function getLocalLinkage(){
        $token = $this->request->post('token');
        $did = $this->request->post('did');
        $examine = new Examine;
        $member = $examine->checkMember($token,'unit');
        $linkageModel = new \app\common\model\Linkage;
        $examine->checkUnit($did, $member['id']);
        $sql = "SELECT l.*,r.`sensorid`,r.`unitlabel` FROM `iot_linkage` l LEFT JOIN `iot_range` r ON r.id = l.`referenceid` WHERE r.`did` = '{$did}' AND l.`operationWay` = 'local' and l.forbidden = '0'";
        $list = $linkageModel->query($sql);
        $sensorModel = new \app\common\model\SensorList();
        $rangeModel = new \app\common\model\Range();
        if(!empty($list)){
            $data = array();
           foreach ($list as $k=>$v){
             
               $info = $sensorModel->get(array('id'=>$v['sensorid']));
               $data[$k]['port'] = $info['port'];
               $data[$k]['sensorTitle'] = $info['title'];
               $data[$k]['label'] = $v['unitlabel'];
               $info = $rangeModel->get(array('id'=>$v['minid']));
               $data[$k]['minVal'] = floatval($info['fixedvalue']);
               $info = $rangeModel->get(array('id'=>$v['maxid']));
               $data[$k]['maxVal'] =  floatval($info['fixedvalue']);
               $data[$k]['taskId'] = 'linkage_'.$v['id'];
               $onoff = $v['onoff']=='on' ? 1 : 0 ;
               $data[$k]['onoff'] = $onoff;
               $data[$k]['switchNum'] = $v['switchnum'];
               $data[$k]['keeptime'] = $v['keeptime'];
               $data[$k]['delaytime'] = $v['delaytime'];
           }
            
        }else{
            $data = array();
        }
        $this->success('获取成功',$data);
    }
    
    public function getOnRunLinkage(){
        $token = $this->request->post('token');
        $did = $this->request->post('did');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $linkageModel = new \app\common\model\Linkage;
        $examine->checkUnit($did, $member['id']);
        $sql = "SELECT l.*,r.`sensorid`,r.`unitlabel` FROM `iot_linkage` l LEFT JOIN `iot_range` r ON r.id = l.`referenceid` WHERE r.`did` = '{$did}'  and l.forbidden = '0'";
        $list = $linkageModel->query($sql);
        $sensorModel = new \app\common\model\SensorList();
        $rangeModel = new \app\common\model\Range();
        if(!empty($list)){
            $data = array();
           foreach ($list as $k=>$v){
             
               $info = $sensorModel->get(array('id'=>$v['sensorid']));
               $data[$k]['port'] = $info['port'];
               $data[$k]['sensorTitle'] = $info['title'];
               $data[$k]['label'] = $v['unitlabel'];
               $info = $rangeModel->get(array('id'=>$v['minid']));
               $data[$k]['minVal'] = floatval($info['fixedvalue']);
               $info = $rangeModel->get(array('id'=>$v['maxid']));
               $data[$k]['maxVal'] =  floatval($info['fixedvalue']);
               $data[$k]['taskId'] = 'linkage_'.$v['id'];
               $onoff = $v['onoff']=='on' ? 1 : 0 ;
               $data[$k]['onoff'] = $onoff;
               $data[$k]['switchNum'] = $v['switchnum'];
               $data[$k]['keeptime'] = $v['keeptime'];
               $data[$k]['delaytime'] = $v['delaytime'];
           }
            
        }else{
            $data = array();
        }
        $this->success('获取成功',$data);
    }
    
    
     public function getLinkageByRange(){
        $token = $this->request->post('token');
        //检查
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $min = $this->request->post('min',0);
        $max = $this->request->post('max',0);
        $unitlabel = $this->request->post('unitlabel');
        $did = $this->request->post('did');
         //开关序号
        $number = $this->request->post('number',0);
        
        $where = array();
        if(empty($did)){
            $this->error('组建单元id不能为空');
        }
        $didArray = explode(",", $did);
        $rangeModel = new \app\common\model\Range;
        if(!empty($unitlabel)){
            $unitlabelArray = explode(",", $unitlabel);
            $total = $rangeModel->where('unitlabel', 'in', $unitlabelArray)->count();
            $total = intval($total);
            if($total == 0){
                $this->error('请检查标签是否有误');
            }
            $where['Range.unitlabel'] = array('in', $unitlabelArray);
        }
        
        
        
        $minids = array();
        if($min!=0){
            if(!empty($unitlabelArray)){
                $minList = $rangeModel->field('id')->where(array('rtype' => 1,'fixedvalue'=>array('egt',$min)))->where('unitlabel', 'in', $unitlabelArray)->select();
            }else{
                $minList = $rangeModel->field('id')->where(array('rtype' => 1,'fixedvalue'=>array('egt',$min)))->select();
            }
            $minList = collection($minList)->toArray($minList);
            //$minList = array_merge($minList,$allList);
            foreach ($minList as $k=>$v){
                $minids[$k] = $v['id'];
            }
            $where['minid'] = array('in', $minids);
        }
        
        
        $maxids = array();
        if($max!=0){
            if(!empty($unitlabelArray)){
                $maxList = $rangeModel->field('id')->where(array('rtype' => 1,'fixedvalue'=>array('elt',$max)))->where('unitlabel', 'in', $unitlabelArray)->select();
            }else{
                $maxList = $rangeModel->field('id')->where(array('rtype' => 1,'fixedvalue'=>array('elt',$max)))->select();
            }
            $maxList = collection($maxList)->toArray($maxList);
            foreach ($maxList as $k=>$v){
                $maxids[$k] = $v['id'];
            }
            $where['maxid'] = array('in', $maxids);
        }
        
         if($number>0){
            $numbers = explode(",", $number);
            $where['switchnum'] = array('in',$numbers);
        }
        
        $linkageModel = new \app\common\model\Linkage;
        
        $list = $linkageModel->with('Range')
        ->where(array('Range.rtype'=>'2','Range.did'=>array('in',$didArray)))
        ->where($where)
        ->select();
        
        
        $list = collection($list)->toArray($list);
        $relist = array();
       $SensorListModel = new \app\common\model\SensorList;
        foreach ($list as $i=>$v){
            $sensor = $SensorListModel->get(array('id'=>$v['range']['sensorid']));
            if(!empty($sensor)){
                $relist[$i]['sensorTitle'] = $sensor['title'];
                $relist[$i]['sensorPort'] = $sensor['port'];
            }
             $relist[$i]['id'] = $v['id'];
            $relist[$i]['title'] = $v['title'];
             $relist[$i]['forbidden'] = intval($v['forbidden']);
            $relist[$i]['did'] = $v['did'];
            $relist[$i]['switchnum'] = $v['switchnum'];
            $relist[$i]['onoff'] = $v['onoff'];
            $relist[$i]['unitlabel'] = $v['range']['unitlabel'];
        }
        $this->success('获取成功',$relist);
    }
    
    public function updateLinkageStatus(){
        $id = $this->request->post('id');
        $token = $this->request->post('token');
        $status = $this->request->post('status');
        //检查
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $linkageModel = new \app\common\model\Linkage;
        $rangeModel = new \app\common\model\Range;
        $info = $linkageModel->get(array('id'=>$id));
        if(empty($info)){
            $this->error('该联动不存在');
        }
        $did = $info['did'];
        $switchnum = $info['switchnum'];
        $forbidden = $status == 'on' ? 0 : 1 ; 
        $client = new WebSocketClient;
        if($info['operationWay'] == 'task' && $info['forbidden'] == 0){
            $client->connect('127.0.0.1', '8282', '/');
            $sendData = array();
            $sendData['cmd'] = 'delTask';
            $sendData['data'] = array(
                'title' => 'cherkLinkage'.$info['id'],
            );
            $sendText = json_encode($sendData);
            $rs = $client->sendData($sendText);
            
            $sendData = array();
            $sendData['cmd'] = 'delTask';
            $sendData['data'] =array(
                'title' =>  'task_'.$did.$switchnum
                
            );
            $sendText = json_encode($sendData);
            $rs = $client->sendData($sendText);
            $client->disconnect();
        }
        $emqx = new Emqx();
        if($info['operationWay'] == 'local' && $info['forbidden'] == 0){
            //向设备发送信息
            $sendTopic = 'UNIT/toReceive/'.$did;
            $sendData =array();
            $sendData['head'] = array(
                'symbol'=>'IOTM',
                'msgId' =>$emqx->getRandromStr(),
                'cmd' =>'toCancel'
            );
            $linkageId = array();
                $time = new ChangeTime();
            $linkageId[0] = 'linkage_'.$info['id'];
            $thistime = $time->getMsectime();
            $sql = "SELECT id FROM `iot_run_task` WHERE lid = {$info['id']} AND cancel = '0' AND runTime>{$thistime}";
            $idList = $rangeModel->query($sql);
            $tids = array();
            $updateText = '';
            foreach ($idList as $k=>$v){
                $tids[$k] = $v['id'];
                $updateText.=$v['id'].',';
            }
            $updateText = rtrim($updateText,',');
            if($updateText!=''){
                $sql = "UPDATE `iot_run_task` SET cancel = '1' WHERE id IN($updateText)";
                $r = $rangeModel->query($sql);
            }
            $sendData['body'] = array(
                'taskId' => $tids,
                'linkageTaskId' => $linkageId
            );
            $sendMeg = json_encode($sendData);
            $emqx->subscribeForSend($sendTopic, $sendMeg);
        }
        //如是任务模式，需发送检查任务
        if($info['operationWay'] == 'task'  &&  $forbidden == 0){
            $client = new WebSocketClient;
            $client->connect('127.0.0.1', '8282', '/');
            $sendData = array();
            $sendData['cmd'] = 'addTask';
            $sendData['data'] = array(
                'title' => 'cherkLinkage'.$info['id'],
                'url' => $_SERVER['SERVER_NAME'].'/api/task/checkLinkage',
                'option'=>array(
                    'id'=>$info['id']
                ),
                'time'=>$info['delaytime'] * 60,
                'persistent'=>1,
                'checkUrl' => $_SERVER['SERVER_NAME'].'/api/task/checkLinkageTask?id='.$info['id']
            );
            $sendText = json_encode($sendData);
            $rs = $client->sendData($sendText);
            $client->disconnect();
        }
        
        
        if($info['operationWay'] == 'local'  &&  $forbidden == 0){
            //向设备发送信息
            $sendTopic = 'UNIT/toReceive/'.$did;
            $sendData =array();
            $sendData['head'] = array(
                'symbol'=>'IOTM',
                'msgId' =>$emqx->getRandromStr(),
                'cmd' =>'toLinkage'
            );
            $rangeModel = new \app\common\model\Range;
            $sensorInfo = $rangeModel ->get(array('id' => $info['referenceid']));
            $max = $rangeModel ->get(array('id' => $info['maxid']));
            $min = $rangeModel ->get(array('id' => $info['minid']));
            $sensorModel = new \app\common\model\SensorList;
            $sensor = $sensorModel->get(array('id'=>$sensorInfo['sensorid']));
            $ronoff = $info['onoff'] =='on' ? 1:0;
            $sendData['body'] = array(
                'taskId' => 'linkage_'.$info['id'],
                'minVal' => $min['fixedvalue'],
                'maxVal' => $max['fixedvalue'],
                'label' => $sensorInfo['unitlabel'],
                'port' => $sensor['port'],
                'sensorTitle' => $sensor['title'],
                'onoff' => $ronoff,
                'switchNum' => $switchnum,
                'keeptime' => $info['keeptime'],
                'delaytime' => $info['delaytime']
            );
            $sendMeg = json_encode($sendData);
            $emqx->subscribeForSend($sendTopic, $sendMeg);
            
        }
        $linkageModel->update(array('forbidden'=>$forbidden),array('id'=>$info['id']));
        $this->success('修改成功');
    }
}

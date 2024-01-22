<?php
namespace app\common\controller;

use app\common\library\Check;
use app\common\library\SignatureHelper;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use Complex\Exception;
use fast\Arr;


class Gizwits
{
    /**
     * 登录授权
     */
    public function login()
    {
        #艾掌控
        $url = 'http://api.gizwits.com/app/login';
        $user = array(
            'username' => '',
            'password' => '',
            'lang' => 'en'
        );
        $userString = json_encode($user);
        $data = \fast\Http::sendByRaw($url, $userString);
        $sendData = json_decode($data,true);
        
        if(!empty($sendData['token']))
            $result =  Db::name('authorize')
            ->where(['label' => 'gizwits'])
            ->update(array(
                'token'=>$sendData['token'],
                'uid'=>$sendData['uid'],
                'expiretime'=>$sendData['expire_at'],
            ));
            
            return $sendData['token'];
    }
    
  
    public function checkcalculation($data,$method,$num){
        $calculation = new Calculation();
        $time = new ChangeTime();
        $SensorLog = new \app\common\model\SensorLog;
        $SensorModel = new \app\common\model\SensorList;
        $sensorInfo = $SensorModel->get(array('id'=>$data['sensorid']));
            print_r(11111);
        $examine = new Examine;
        switch ($method){
            case 'noiseSensor':
                $r =  $calculation->noiseSensor($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                   $newData =  $calculation->adjust(array('label'=>'noise','val'=>$r), $data['sensorid'], 'one');
                   $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'noise',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'soilHumidity1':
                $r =  $calculation->soilHumidity1($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'soilHumidity','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'soilHumidity',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'soilHumidity2':
                $r =  $calculation->soilHumidity2($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'soilHumidity','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'soilHumidity',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'soilTemperature':
                $r =  $calculation->soilTemperature($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'soilTemperature','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'soilTemperature',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'voltage':
                $value = is_array($data['val'])?$data['val'][0]:$data['val'];
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'voltage','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $r =  $calculation->voltage($num, $value);
                
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'voltage',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                
                $SensorLog->insert($meg);
                break;
            case 'weatherStation':
                $r =  $calculation->weatherStation($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust($r, $data['sensorid'], 'array');
                    $r = $newData;
                }
                foreach ($r as $k=>$v){
                    if($k == 'wind'){
                        $meg = array(
                            'val' => $v['winddata'],
                            'sensorid' => $data['sensorid'],
                            'unitlabel' => $k,
                            'did' => $data['did'],
                            'createtime' => $time->getMsectime(),
                            'updatetime' => $data['updatetime'],
                            'nid'=>$data['nid'],
                            'text'=>$v['wind'],
                            'istext'=>1
                        );
                    }else{
                        $meg = array(
                            'val' => $v,
                            'sensorid' => $data['sensorid'],
                            'unitlabel' => $k,
                            'did' => $data['did'],
                            'createtime' => $time->getMsectime(),
                            'updatetime' => $data['updatetime'],
                            'nid'=>$data['nid']
                        );
                    }
                    $SensorLog->insert($meg);
                }
                
                break;
            case 'ozoneSensor':
                $r =  $calculation->ozoneSensor($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'ozone','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'ozone',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'positiveVoltageSensor':
                $r =  $calculation->positiveVoltageSensor($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'voltage','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'voltage',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                $examine->checkPeosonLog('voltage', $meg);
                break;
            case 'co2Sensor':
                $r =  $calculation->co2Sensor($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'co2','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'co2',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'soundSensor':
                $r =  $calculation->soundSensor($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'sound','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'sound',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'tempHumSensor':
                $r =  $calculation->tempHumSensor($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust($r, $data['sensorid'], 'array');
                    $r = $newData;
                }
                foreach ($r as $k=>$v){
                    
                    $meg = array(
                        'val' => $v,
                        'sensorid' => $data['sensorid'],
                        'unitlabel' => $k,
                        'did' => $data['did'],
                        'createtime' => $time->getMsectime(),
                        'updatetime' => $data['updatetime'],
                        'nid'=>$data['nid']
                    );
                    
                    $SensorLog->insert($meg);
                }
                break;
            case 'wirelessTempHumSensor':
                $r =  $calculation->wirelessTempHumSensor($num, $data['val']);
                    print_r($r);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust($r, $data['sensorid'], 'array');
                    $r = $newData;
                }
            
                foreach ($r as $k=>$v){
                    
                    $meg = array(
                        'val' => $v,
                        'sensorid' => $data['sensorid'],
                        'unitlabel' => $k,
                        'did' => $data['did'],
                        'createtime' => $time->getMsectime(),
                        'updatetime' => $data['updatetime'],
                        'nid'=>$data['nid']
                    );
                    
                    $SensorLog->insert($meg);
                }
                break;
            case 'pmSensor':
                $r =  $calculation->pmSensor($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust($r, $data['sensorid'], 'array');
                    $r = $newData;
                }
                foreach ($r as $k=>$v){
                    
                    $meg = array(
                        'val' => $v,
                        'sensorid' => $data['sensorid'],
                        'unitlabel' => $k,
                        'did' => $data['did'],
                        'createtime' => $time->getMsectime(),
                        'updatetime' => $data['updatetime'],
                        'nid'=>$data['nid']
                    );
                    
                    $SensorLog->insert($meg);
                }
                break;
            case 'waterLeakageSensor':
                $r =  $calculation->waterLeakageSensor($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'waterleakage','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'waterleakage',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'onoffSensor':
                $r =  $calculation->onoffSensor($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'onoff','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'onoff',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'mq2Sensor':
                $r =  $calculation->mq2Sensor($num, $data['val']);
                
                if($data['did'] == 'gFKtfQk1yUhPDzxc5GFjy5' && $r>10){
                    $r = $r - 8;
                }
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'mq2','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'mq2',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'lightSensor':
                
                $r =  $calculation->lightSensor($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'light','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'light',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'peosonSensor':
                $r =  $calculation->peosonSensor($num, $data['val']);
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'person',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                $examine->checkPeosonLog('person', $meg);
                break;
            case 'thermosensitiveWindSensor':
                $r =  $calculation->thermosensitiveWindSensor($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'windspeed','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'windspeed',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'electronicVoltmeter':
                $r =  $calculation->electronicVoltmeter($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust($r, $data['sensorid'], 'array');
                    $r = $newData;
                }
                foreach ($r as $k=>$v){
                    
                    $meg = array(
                        'val' => $v,
                        'sensorid' => $data['sensorid'],
                        'unitlabel' => $k,
                        'did' => $data['did'],
                        'createtime' => $time->getMsectime(),
                        'updatetime' => $data['updatetime'],
                        'nid'=>$data['nid']
                    );
                    
                    $SensorLog->insert($meg);
                }
                break;
            case 'light6W':
                $r =  $calculation->light6W($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'light','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'light',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'light18W':
                $r =  $calculation->light18W($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'light','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'light',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
            case 'ultravioletSensor':
                $r =  $calculation->ultravioletSensor($num, $data['val']);
                if($sensorInfo['isAdjust'] == 1){
                    $newData =  $calculation->adjust(array('label'=>'ultraviolet','val'=>$r), $data['sensorid'], 'one');
                    $r = $newData['val'];
                }
                $meg = array(
                    'val' => $r,
                    'sensorid' => $data['sensorid'],
                    'unitlabel' => 'ultraviolet',
                    'did' => $data['did'],
                    'createtime' => $time->getMsectime(),
                    'updatetime' => $data['updatetime'],
                    'nid'=>$data['nid']
                );
                $SensorLog->insert($meg);
                break;
        
        }
        
        
    }
    
    public function setSwitch($did,$data){
        $time = new ChangeTime();
//         Db::startTrans();
//         try {
            for($i=0;$i<4;$i++){
                $status = $data[$i];
                
                $number = $i+1;
                $meg = array(
                    'did' => $did,
                    'number' => $number,
                    'status' => $status,
                    'updatetime'=>$time->getMsectime()
                );
                
                $info = Db::name('sensor_status')->where(
                    array('did'=>$did,'number'=>$number)
                    )->find();
                    if(!empty($info)){
                        //检测非手动开启的状态
                        if($did != '1DIrTA5xH6CjVQ7jbNJSMw' && $did != 'hJVxMG4fS6Za4LPyvR01m4'){
                            
                                   //5秒内修改不进行other处理
                            $thisTime = $time->getMsectime();
                             $lessTime = $thisTime - $info['updatetime'];
                            
                            if($info['status'] != intval($status) && $lessTime > 5000){
                                
                                $statusText = $status == 0 ?'off':'on';
                                    try {
                                          $info = Db::name('sensor_status')->where(
                    array('did'=>$did,'number'=>$number)
                    )->find();
                     $lessTime = $thisTime - $info['updatetime'];
                    if($lessTime > 5000){
                                Db::name('status_log')->insert(array(
                                    'did' => $did,
                                    'number' => $number,
                                    'status' => $statusText,
                                    'way' => 'other',
                                    'createtime' => $time->getMsectime()
                                ));
                    }
                                    } catch (ValidateException $e) {
                        Db::rollback();
                     } catch (PDOException $e) {
                        Db::rollback();
                       } catch (Exception $e) {
                        Db::rollback();
                       }
                                //非手动开启，所有相关联动运行任务失效
                                $taskModel = new \app\common\model\Task;
                               
                               $taskList =  $taskModel->query("SELECT  /*+ QB_NAME(QB1) NO_RANGE_OPTIMIZATION(`iot_task`@QB1 `isinvalid`, `switchnum`) */  id FROM     `iot_task` USE INDEX FOR ORDER BY(`switchnum`)  WHERE `did` = '{$did}' and  `switchnum` = {$number} and  `isInvalid` = '0'   AND `status` = '0' ORDER BY `id` DESC ");
                                $taskId = '';
                                if(!empty($taskList)){
                                    foreach ($taskList as $val){
                                        $taskId .= "'".$val['id']."',";
                                        
                                    }
                                    $taskId = rtrim($taskId,',');
                                    
                                    $taskModel->query(" UPDATE `iot_task` SET    `isInvalid`='1' WHERE id in({$taskId}) ");
                                    
                                }
                       
                                
                            }
                        }
                       
                   
                            try {
                        Db::name('sensor_status')->where(array('did'=>$did,'number'=>$number))->update(
                            array('status'=>$status,'updatetime'=>$time->getMsectime()));
                        Db::commit();
                        
                    } catch (ValidateException $e) {
                        Db::rollback();
                    } catch (PDOException $e) {
                        Db::rollback();
                    } catch (Exception $e) {
                        Db::rollback();
                    }
                    }else{
                        Db::name('sensor_status')->insert($meg);
                    }
                    
                    
            }

        
    }
    
   
    /**
     * 获取token
     */
    public function getToken(){
        $data = Db::name('authorize')->where('label', 'gizwits')->find();
        if(empty($data['token']) || $data['expiretime'] < time()){
            $token = $this->login();
        }else{
            $token = $data['token'];
        }
        return $token;
    }
    
    
    
    /**
     * 控制开关
     */
    public function onoff($did,$number,$status,$way='other',$taskId = 0)
    {
        
        $url = "http://api.gizwits.com/app/control/{$did}";
        
        
        $onoff = $status=='on'?true:false;
        $attr = array();
        
        $fordata = array(
            'onoff'.$number => $onoff
        );
        $attr = array(
            'attrs' =>$fordata
        );
        $dataString = json_encode($attr);
        $token = $this->getToken();
        
        $data = \fast\Http::sendByRaw($url, $dataString,$token);
        $re = json_decode($data,true);
        $time = new ChangeTime();
        //$status = $status=='on'?'on':'off';
        //  if(empty($re)){
        
        Db::name('status_log')->insert(array(
            'did' => $did,
            'number' => $number,
            'status' => $status,
            'createtime' => $time->getMsectime(),
            'way' => $way,
            'taskId' => $taskId
        ));
        
        // }
        return $re;
        //print_r($data);exit();
        
    }
    
    public function sendNotice($v,$msg,$runData=array()){
        $smsbao = new \addons\smsbao\library\Smsbao;
        $time = new ChangeTime();
        $rangeModel = new \app\common\model\Range;
        $referenceInfo = $rangeModel->get(array('id'=>$v['referenceid']));
        $noticeModel = new \app\common\model\Notice;
        $v = $noticeModel->get(array('id'=>$v['id']));
        //免打扰设定
        $banApp = false;
        $banClient = false;
        $banOnoff = false;
        $banMessage = false;
        $banPhone = false;
        $canSend = false;
        $disturbModel = new \app\common\model\NoticeNotDisturb;
        if($referenceInfo['rtype'] == 2){
            $disturbInfo = $disturbModel->get(array('did'=>$referenceInfo['did'],'disturbtype'=>'unit'));
            if(!empty($disturbInfo)){
                if($disturbInfo['everyday'] == 1){
                    $startTime = mktime(0,$disturbInfo['startminute'],$disturbInfo['starthour'],date('m'),date('d'),date('Y'));
                    $endTime = mktime(0,$disturbInfo['endminute'],$disturbInfo['endhour'],date('m'),date('d'),date('Y'));
                }else{
                    $startTime = $disturbInfo['starttime']/1000;
                    $endTime = $disturbInfo['endtime']/1000;
                }
                $today = time();
                if($startTime>=$today && $endTime<=$today){
                    $banApp = $disturbInfo['banapp'] == 1 ? true : $banApp;
                    $banClient = $disturbInfo['banclient'] == 1 ? true : $banClient;
                    $banOnoff = $disturbInfo['banonoff'] == 1 ? true : $banOnoff;
                    $banMessage = $disturbInfo['banmessage'] == 1 ? true : $banMessage;
                    $banPhone = $disturbInfo['banphone'] == 1 ? true : $banPhone;
                }
            }
        }
        $disturbInfo = array();
        $disturbInfo = $disturbModel->get(array('nid'=>$v['id'],'disturbtype'=>'notice'));
        if(!empty($disturbInfo)){
            if($disturbInfo['everyday'] == 1){
                $startTime = mktime(0,$disturbInfo['startminute'],$disturbInfo['starthour'],date('m'),date('d'),date('Y'));
                $endTime = mktime(0,$disturbInfo['endminute'],$disturbInfo['endhour'],date('m'),date('d'),date('Y'));
            }else{
                $startTime = $disturbInfo['starttime']/1000;
                $endTime = $disturbInfo['endtime']/1000;
            }
            $today = time();
            if($startTime>=$today && $endTime<=$today){
                $banApp = $disturbInfo['banapp'] == 1 ? true : $banApp;
                $banClient = $disturbInfo['banclient'] == 1 ? true : $banClient;
                $banOnoff = $disturbInfo['banonoff'] == 1 ? true : $banOnoff;
                $banMessage = $disturbInfo['banmessage'] == 1 ? true : $banMessage;
                $banPhone = $disturbInfo['banphone'] == 1 ? true : $banPhone;
            }
            
        }
        $thistime = $time->getMsectime();
        $delaytime = $v['sendtime'] + $v['delaytime'] * 60 * 1000;
        if($thistime>$delaytime){
            $canSend = true;
        }
        
        
        
        
        //发送短信通知
        if($v['phone'] != '' &&  (!$banMessage) && $canSend){
            $phone = unserialize($v['phone']);
            foreach ($phone as $val){
                $result = $smsbao->mobile($val)->msg($msg)->send();
            }
            $data = array(
                'ntype' => 2,
                'nid'=>$v['id'],
                'sendtime' =>$time->getMsectime(),
                'createtime' =>$time->getMsectime()
            );
        }else {
            $data = array(
                'ntype' => 2,
                'nid'=>$v['id'],
                'createtime' =>$time->getMsectime()
            );
        }
     
        
        //电话通知
        if($v['isCall'] == 1 && (!$banPhone) && $canSend && (!empty($v['callNumber']))){
            if($v['isNight'] == 'on'){
                $hour = date('H');
                if($hour<22 && $hour>=8){
                    $this->callByVoice($v['callNumber'], $v['voiceCode'], 100, 3,array('alarmname'=>$v['alarmName']));
                }
            }else{
                $this->callByVoice($v['callNumber'], $v['voiceCode'], 100, 3,array('alarmname'=>$v['alarmName']));
            }
        }
        
        //内部通知
        if($v['people']!='' && $canSend){
            $people = unserialize($v['people']);
            if(!empty($people)){
                foreach ($people as $val){
                    if($val != 0){
                        $sensorList = new \app\common\model\SensorList;
                        $thistime = $time->getMsectime();
                        $sensorList->query(" UPDATE /*+ QB_NAME(QB1) NO_RANGE_OPTIMIZATION(`iot_record_log`@QB1 `mid`, `isread`) */ `iot_record_log` SET `isexpire`='1',`expiretime`={$thistime} WHERE  `mid` = {$val} AND `nid` = {$v['id']} AND `isread` = '0' AND `isexpire` = '0' ");
                        $thistime = $time->getMsectime();
                        $recordid = md5($thistime.$val.$v['id']);
                        $data = array(
                            'recordid' => $recordid,
                            'mid' => $val,
                            'nid' => $v['id'],
                            'sendtime' => $thistime,
                            'createtime' =>$thistime
                        );
                        
                        Db::name('record_log')->insert($data);
                        $addId =  Db::name('record_log')->getLastInsID();
                        //添加触发值
                        if(!empty($runData)){
                            $runData['tid'] = $addId;
                            $runData['createtime'] = $time->getMsectime();
                            Db::name('notice_value')->insert($runData);
                        }
                    }
                }
                
            }
        }
        $thistime = $time->getMsectime();
        if($canSend){
            Db::name('notice')->where(array('id'=>$v['id']))->update(array('executiontime'=>$thistime,'sendtime'=>$thistime ));
            //同类型的通知不再提醒
            if(!empty($v['noticetype']) &&  $v['weigh']>0){
                Db::name('notice')->where(array('noticetype'=>$v['noticetype'],'weigh'=>0,'banDid'=>$v['banDid']))->update(array('sendtime'=>$thistime ));
            }
        }else{
            Db::name('notice')->where(array('id'=>$v['id']))->update(array('executiontime'=>$thistime ));
        }
        
        //关闭所有开关
        if($v['isOnoff'] == 1 && (!$banOnoff) && $canSend){
         
              $unit = new Unit();
              $linkageModel = new \app\common\model\Linkage;
                for($s= 1; $s<17;$s++){
                    $r = $unit->onoff($v['banDid'], $s, 0,$thistime,'warn',0);
                }
        }
        
        
        //按组建单元推送消息
        //    $rangeModel = new \app\common\model\Range;
        $info = $referenceInfo;
        if(!empty($info)){
            $client = new WebSocketClient;
            switch ($info['rtype']){
                case 2:
                    if($v['isApp'] == 1 && (!$banApp) && $canSend){
                        $sendInfo = array();
                        $sendInfo['cmd'] = 'sendNotice';
                        $sendInfo['data'] = array(
                            'noticesign' => $v['noticesign'],
                            'recordid' => $recordid,
                            'terminal' => 'app'
                        );
                        $sendInfo['data']['did'] = $info['did'];
                        $client->connect('127.0.0.1', '9292', '/');
                        $sendText = json_encode($sendInfo);
                        $rs = $client->sendData($sendText);
                    }
                    if($v['isClient'] == 1 && (!$banClient) && $canSend){
                        $sendInfo = array();
                        $sendInfo['cmd'] = 'sendNotice';
                        $sendInfo['data'] = array(
                            'noticesign' => $v['noticesign'],
                            'recordid' => $recordid,
                            'terminal' => 'client'
                        );
                        $sendInfo['data']['did'] = $info['did'];
                        $client->connect('127.0.0.1', '9292', '/');
                        $sendText = json_encode($sendInfo);
                        $rs = $client->sendData($sendText);
                    }
                    break;
                case 3:
                    $groups = array();
                    $groups[0] = $info['groupid'];
                    $groupModel =  new \app\common\model\Group;
                    $sensorids = $groupModel->getSensorIdList($groups);
                    $sensorList = Db::name('sensor_list')
                    ->where('id', 'in', $sensorids)->select();
                    $didList = array();
                    foreach ($sensorList as $sval){
                        $didList[] = $sval['did'];
                    }
                    $didList = array_unique($didList);
                    foreach ($didList as $dval){
                        if($v['isApp'] == 1 && (!$banApp) && $canSend){
                            $sendInfo = array();
                            $sendInfo['cmd'] = 'sendNotice';
                            $sendInfo['data'] = array(
                                'noticesign' => $v['noticesign'],
                                'recordid' => $recordid,
                                'terminal' => 'app'
                            );
                            $sendInfo['data']['did'] = $dval;
                            $client->connect('127.0.0.1', '9292', '/');
                            $sendText = json_encode($sendInfo);
                            $rs = $client->sendData($sendText);
                        }
                        if($v['isClient'] == 1 && (!$banClient) && $canSend ){
                            $sendInfo = array();
                            $sendInfo['cmd'] = 'sendNotice';
                            $sendInfo['data'] = array(
                                'noticesign' => $v['noticesign'],
                                'recordid' => $recordid,
                                'terminal' => 'client'
                            );
                            $sendInfo['data']['did'] = $dval;
                            $client->connect('127.0.0.1', '9292', '/');
                            $sendText = json_encode($sendInfo);
                            $rs = $client->sendData($sendText);
                        }
                        
                    }
                    break;
                case 4:
                    $recordid = md5($thistime.$v['id']);
                    if($v['isApp'] == 1 && (!$banApp) && $canSend){
                        $sendInfo = array();
                        $sendInfo['cmd'] = 'sendNotice';
                        $sendInfo['data'] = array(
                            'noticesign' => $v['noticesign'],
                            'recordid' => $recordid,
                            'terminal' => 'app'
                        );
                        $sendInfo['data']['did'] = $info['did'];
                        $client->connect('127.0.0.1', '9292', '/');
                        $sendText = json_encode($sendInfo);
                        $rs = $client->sendData($sendText);
                    }
                    if($v['isClient'] == 1 && (!$banClient) && $canSend){
                        $sendInfo = array();
                        $sendInfo['cmd'] = 'sendNotice';
                        $sendInfo['data'] = array(
                            'noticesign' => $v['noticesign'],
                            'recordid' => $recordid,
                            'terminal' => 'client'
                        );
                        $sendInfo['data']['did'] = $info['did'];
                        $client->connect('127.0.0.1', '9292', '/');
                        $sendText = json_encode($sendInfo);
                        $rs = $client->sendData($sendText);
                    }
                    break;
            }
            $client->disconnect();
        }
        
        
    }
    
   
    
    /**
     * 拨打语音电话
     */
    public function callByVoice($calledNumber,$voiceCode,$Volume,$PlayTimes,$callParam){
        $params = array ();
        
        // *** 需用户填写部分 ***
        // fixme 必填：是否启用https
        $security = false;
        
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "";
        $accessKeySecret = "";
        
        // fixme 必填: 被叫显号
        //$params["CalledShowNumber"] = "4001112222";
        
        // fixme 必填: 被叫显号
        $params["CalledNumber"] = $calledNumber;
        
        // fixme 必填: Tts模板Code
        $params["TtsCode"] = $voiceCode;
        
        // fixme 选填: Tts模板中的变量替换JSON,假如Tts模板中存在变量，则此处必填
        $params["TtsParam"] = $callParam;
        
        // fixme 选填: 音量
        $params["Volume"] = $Volume;
        
        // fixme 选填: 播放次数
        $params["PlayTimes"] = $PlayTimes;
        
        // fixme 选填: 外呼流水号
        // $params["OutId"] = "yourOutId";
        
        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        
        if(!empty($params["TtsParam"]) && is_array($params["TtsParam"])) {
            $params["TtsParam"] = json_encode($params["TtsParam"], JSON_UNESCAPED_UNICODE);
        }
        
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();
        
        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dyvmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-shenzhen",
                "Action" => "SingleCallByTts",
                "Version" => "2017-05-25",
            )),
            $security
            );
        
        return $content;
        
    }
    
    public function sendTask($v,$type='polling',$runData=array()){
        
        $client = new WebSocketClient;
        $time = new ChangeTime();
        $thistime = $time->getMsectime();
        $unitModel = new \app\common\model\ComponentUnit;
        //获取该组建单元对应设备用户
        $sql = " SELECT mid FROM `iot_member_unit` u LEFT JOIN  `iot_member` m  ON m.id = u.`mid` WHERE u.did = '{$v['did']}' AND m.`member_type` = 'unit' ";
        $memberList = $unitModel->query($sql);
        $uid = array();
        foreach ($memberList as $i=>$val){
            $uid[$i] = $val['mid'];
          
        }
        $count = sizeof($uid);
        if($count<=0){
            return false;
        }else{
            //添加触发值
            if(!empty($runData)){
                $runData['createtime'] = $time->getMsectime();
                Db::name('task_value')->insert($runData);
                $taskValue =  Db::name('task')->getLastInsID();
            }else{
                $taskValue = 0;
            }
            $thisTime = $time->getMsectime()
            $sendInfo = array();
            //联动任务判断
            if($v['keeptime']>0 && $v['onoff'] == 'on'){
                $thisTime = $time->getMsectime();
                $runTime = $thisTime;
                //任务1
               
                Db::name('run_task')->insert(array(
                    'did' => $v['did'],
                    'number' => $v['switchnum'],
                    'onoff' => 1,
                    'runTime' => $runTime,
                    'way' => 'linkage',
                    'tvid' => $taskValue,
                    'lid' => $v['id']
                ));
                $taskId1 =  Db::name('run_task')->getLastInsID();
                $addSql1 = ''; 
                foreach ($uid as $i=>$val){
                    $addSql1 .= "({$taskId1},{$val}),";
                }
                $addSql1 = rtrim($addSql1,',');
                $sql = "INSERT INTO `iot_run_task_member`(taskid,userid) VALUE $addSql1";
                $r = $unitModel->query($sql);
                //任务2
                $afterTime = $runTime + $v['keeptime'] * 60 * 1000;
                Db::name('run_task')->insert(array(
                    'did' => $v['did'],
                    'number' => $v['switchnum'],
                    'onoff' => 0,
                    'runTime' => $afterTime,
                    'way' => 'linkage',
                    'tvid' => $taskValue
                ));
                $taskId2 =  Db::name('run_task')->getLastInsID();
                $addSql2 = '';
                foreach ($uid as $i=>$val){
                    $addSql2 .= "({$taskId2},{$val}),";
                }
                $addSql2 = rtrim($addSql2,',');
                $sql = "INSERT INTO `iot_run_task_member`(taskid,userid) VALUE $addSql2";
                $r = $unitModel->query($sql);
                
                $unit = new Unit();
                $unit->onoff($v['did'], $v['switchnum'], 1, $runTime,'linkage');
                $unit->onoff($v['did'], $v['switchnum'], 0, $afterTime,'linkage');
            }else{
                $status = $v['onoff'] == 'on' ? 1 : 0;
                $thisTime = $time->getMsectime();
                $runTime = $thisTime;
                //任务1
                Db::name('run_task')->insert(array(
                    'did' => $v['did'],
                    'number' => $v['switchnum'],
                    'onoff' => intval($status),
                    'runTime' => $runTime,
                    'way' => 'linkage',
                    'tvid' => $taskValue
                ));
                $taskId1 =  Db::name('run_task')->getLastInsID();
                $addSql1 = '';
                foreach ($uid as $i=>$val){
                    $addSql1 .= "({$taskId1},{$val}),";
                }
                $addSql1 = rtrim($addSql1,',');
                $sql = "INSERT INTO `iot_run_task_member`(taskid,userid) VALUE $addSql1";
                $r = $unitModel->query($sql);
             
                //开关操作
                $unit = new Unit();
                $unit->onoff($v['did'], $v['switchnum'], $status, $runTime,'linkage');
            }
            return  true;
            
        }
    }
    
}

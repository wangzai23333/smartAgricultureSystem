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
use Monolog\Processor\WebProcessor;
use app\common\library\Check;
use app\admin\controller\example\Tablelink;
use app\common\controller\Tdengine;
/**
 * 设备等相关对外接口
 */
class Unit extends Api
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
        $this->check();
    }
    /**
     * ip、appid检查
     */
    public function check(){
        $ip = $this->getIp();
        if($ip!='127.0.0.1' && $ip!='47.106.108.215'){
          // $this->error('无权访问');
        }
//         $header = $this->request->getHeaders();
//         if(!empty($header['Appid'])){
//         $appid = $header['Appid'];
//         if($appid!='9NT8qs&RJNs8bn1L8N$%ZL0ThzQES3'){
//             $this->error('无权访问');
//         }
//         }else{
//             $this->error('无权访问');
//         }
    }
    
    /**
     * 用户检测
     */
    public function checkMember(){
        $token = $this->request->post('token');
        $examine = new Examine;
        $member = $examine->checkMember($token,'unit');
        $this->success('有效用户',array('uid'=>$member['id']));
    }
   
    /**
     * 获取信息
     */
    public function setMessage(){
        $r = file_get_contents('php://input');
        $data = json_decode($r,true);
        if(empty($data) || empty($data['did']) || empty($data['sensorData']) || empty($data['onoffData']) || empty($data['updateTime'])){
            print_r($data);
            $this->error('数据不合法');
        }else{
            $inputData = $data['sensorData'];//传感器数据
            $did = $data['did'];//组建单元id
            $onoffData = $data['onoffData'];//开关数据
            $updateTime = $data['updateTime'];//更新时间
            $unitModel = new \app\common\model\ComponentUnit;
            $info = $unitModel->get(array('did'=>$did));
            if(empty($info)){
                $this->error('数据不合法');
            }else{
//                 $unitLogModel = new \app\common\model\ComponentUnitLog;
//                 $oldInfo = $unitLogModel->get(array('updatetime'=>$updateTime));
//                 if(!empty($oldInfo)){
//                     $this->error('数据已存在');
//                 }

//                 //录入原始数据
//                 $sendData = serialize($data);
              
//                 $r = $unitLogModel->insert(array(
//                     'did' => $did,
//                     'send_data' => $sendData,
//                     'updatetime' => $updateTime
//                 ));
//                 $nid = $unitLogModel->getQuery()->getLastInsID();
                
                
               //检查传感器是否存在
                $sensorListModel = new \app\common\model\SensorList;
                $unitSensorModel = new \app\common\model\UnitSensor;
                $tdengine = new Tdengine();
                $time = new ChangeTime();
                $addSql = '';
                $list = array();
                foreach ($inputData as $k=>$v){
                    $sensorInfo = $sensorListModel->get(array('label'=>$v['label'],'did'=>$did,'title' => $v['sensor_title'],'port'=>$v['port']));
                    if(empty($sensorInfo)){
                        $thisTime = $time->getMsectime();
                        $r = $sensorListModel->insert(array(
                            'did' => $did,
                            'port'=>$v['port'],
                            'title' => $v['sensor_title'],
                            'label'=>$v['label'],
                            'createtime' => $thisTime
                        ));
                        $sensorId = $sensorListModel->getQuery()->getLastInsID();
                        $unitSensorModel->insert(array(
                            'sensorid' => $sensorId,
                            'label' => $v['label']
                        ));
                        $v['val'] = floatval($v['val']);
                    }else{
                        $sensorId = $sensorInfo['id'];
                        $unitSensorInfo = $unitSensorModel->get(array('sensorid'=>$sensorId,'label'=>$v['label']));
                        if(empty($unitSensorInfo)){
                          $this->error('该属性已有传感器绑定');
                        }
                        if($sensorInfo['isAdjust'] == 1){
                            $calculation = new Calculation();
                            $newData =  $calculation->adjust(array('label'=>$v['label'],'val'=>floatval($v['val'])), $sensorId, 'one');
                            $v['val'] = $newData['val'];
                        }
                    }
                    
                    //录入tdengine
                    $istext = $v['istext']==1 ? 'true' : 'false';
                     Db::startTrans();
                    try {
                        $val = floatval($v['val']);
                        $sql = "INSERT INTO sensor_{$sensorId} USING model_{$v['label']} TAGS ('{$did}',{$sensorId},'{$v['port']}') VALUES ({$updateTime}, {$val}, {$istext}, '{$v['content']}','{$v['content_des']}');";
                        $r = $tdengine->queryBySql($sql);
                        
                        Db::commit();
                    }catch (ValidateException $e) {
                        Db::rollback();
                        $this->error('请检查数据是否有误');
                    } catch (PDOException $e) {
                        Db::rollback();
                        $this->error('请检查数据是否有误');
                    } catch (Exception $e) {
                        Db::rollback();
                        $this->error('请检查数据是否有误');
                    }
                    
                    
                    //$addSql.="({$v['val']},'{$v['istext']}','{$v['content']}','{$v['contentDes']}',{$sensorId},'{$v['label']}',{$nid},'{$did}',{$updateTime}),";
                    //录入记录，方便后续处理
                    $list[$k]['val'] = floatval($v['val']);
                    $list[$k]['istext'] = $v['istext'];
                    $list[$k]['port'] = $v['port'];
                    $list[$k]['content'] = $v['content'];
                    $list[$k]['content_des'] = $v['content_des'];
                    $list[$k]['sensorid'] = $sensorId;
                    $list[$k]['unitlabel'] = $v['label'];
                   // $list[$k]['nid'] = $nid;
                    $list[$k]['did'] = $did;
                    $list[$k]['sensor_title'] = $v['sensor_title'];
                    $list[$k]['updateTime'] = $updateTime;
                }
               //录入传感器数据
               // $addSql = rtrim($addSql,',');
               // $sql = "INSERT INTO `iot_sensor_log`(val,is_text,content,content_des,sensorid,unitlabel,logid,did,createtime) VALUE $addSql";
                //print_r($sql);exit();
               
//                 try {
//                    $r = $unitLogModel->query($sql);
//                 }catch (ValidateException $e) {
//                     $this->error('请检查数据是否有误');
//                 } catch (PDOException $e) {
//                     $this->error('请检查数据是否有误');
//                 } catch (Exception $e) {
//                     $this->error('请检查数据是否有误');
//                 }
                
                $inputOnOff = array();
                //开关状态录入
                $sensorStatusModel = new \app\common\model\SensorStatus;
                $sensorStatusInfo = $sensorStatusModel->get(array('did'=>$did));
                foreach ($onoffData as $v){
                    $inputOnOff[intval($v['number'])] = intval($v['state']);
                }
                if(empty($sensorStatusInfo)){
                for ($i=1;$i<=16;$i++){
                    if(empty($inputOnOff[$i])){
                        $inputOnOff[$i] = 0;
                    }
                }
                }else{
                    for ($i=1;$i<=16;$i++){
                        if(empty($inputOnOff[$i])){
                            $inputOnOff[$i] = 0;
                        }
                        if($sensorStatusInfo['onoff'.$i] != $inputOnOff[$i]){
                            $statusText = $inputOnOff[$i]==1?'on':'off';
                            Db::name('status_log')->insert(array(
                                'did' => $did,
                                'number' => $i,
                                'status' => $statusText,
                                'createtime' => $updateTime
                            ));
                        }

                        
                    }
                }
                 Db::startTrans();
                try {
                    if(empty($sensorStatusInfo)){
                        $sensorStatusModel->insert(array(
                            'did' => $did,
                            'onoff1' => $inputOnOff[1],
                            'onoff2' => $inputOnOff[2],
                            'onoff3' => $inputOnOff[3],
                            'onoff4' => $inputOnOff[4],
                            'onoff5' => $inputOnOff[5],
                            'onoff6' => $inputOnOff[6],
                            'onoff7' => $inputOnOff[7],
                            'onoff8' => $inputOnOff[8],
                            'onoff9' => $inputOnOff[9],
                            'onoff10' => $inputOnOff[10],
                            'onoff11' => $inputOnOff[11],
                            'onoff12' => $inputOnOff[12],
                            'onoff13' => $inputOnOff[13],
                            'onoff14' => $inputOnOff[14],
                            'onoff15' => $inputOnOff[15],
                            'onoff16' => $inputOnOff[16],
                            'updatetime' => $updateTime
                        ));
                    }else{
                    
                        $sensorStatusModel->update(array(
                            'onoff1' => $inputOnOff[1],
                            'onoff2' => $inputOnOff[2],
                            'onoff3' => $inputOnOff[3],
                            'onoff4' => $inputOnOff[4],
                            'onoff5' => $inputOnOff[5],
                            'onoff6' => $inputOnOff[6],
                            'onoff7' => $inputOnOff[7],
                            'onoff8' => $inputOnOff[8],
                            'onoff9' => $inputOnOff[9],
                            'onoff10' => $inputOnOff[10],
                            'onoff11' => $inputOnOff[11],
                            'onoff12' => $inputOnOff[12],
                            'onoff13' => $inputOnOff[13],
                            'onoff14' => $inputOnOff[14],
                            'onoff15' => $inputOnOff[15],
                            'onoff16' => $inputOnOff[16],
                            'updatetime' => $updateTime
                        ),array('did'=>$did));
                    }
                    Db::commit();
                }catch (ValidateException $e) {
                    Db::rollback();
                    $this->error('请检查开关数据是否有误');
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error('请检查开关数据是否有误');
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error('请检查开关数据是否有误');
                }
                
                //推送数据
                $sendData = array();
                $sendData['sensorData'] = $list;
                $sendData['onoffData'] = array();
                foreach ($inputOnOff as $k=>$v){
                    $sendData['onoffData'][$k-1]['num'] = $k;
                    $sendData['onoffData'][$k-1]['onOff'] = $inputOnOff[$k];
                }
                $sendInfo = array();
                $sendInfo['cmd'] = 'sendUnitMeg';
                $sendInfo['data'] = $sendData;
                $sendInfo['data']['did'] = $did;
                $client = new WebSocketClient;
                $client->connect('127.0.0.1', '9292', '/');
                $sendText = json_encode($sendInfo);
                $rs = $client->sendData($sendText);
                $client->disconnect();
                $examine = new Examine;
//                 $examine->cherkReading($did, 'notice', $list);
//                 $examine->cherkReading($did, 'linkage', $list);
                
                
        
                //更新上线状态
                $info = $unitModel->get(array('did'=>$did));
                if($info['online'] == 0){
                    $unitModel->update(array('online'=>1),array('did'=>$did));
                }
                
                
                $this->success('录入成功');
            }
            
        }
     
        
        
    }
    
    /**
     * 设置组建单元上下线状态
     */
    public function setOnline(){
        $unitModel = new \app\common\model\ComponentUnit;
        $time = new ChangeTime();
        $did = $this->request->post('did');
        $online = $this->request->post('online');
        $updatetime = $this->request->post('updatetime');
        $info = $unitModel->get(array('did'=>$did));
       
        if(empty($info)){
            $this->error('该组建单元不存在');
        }else{
            $online = intval($online);
             Db::startTrans();
            try {
                $thistime = $time->getMsectime();
                $r = $unitModel->update(array('online'=>$online,'updatetime'=>$updatetime),array('did'=>$did));
                
                Db::commit();
            } catch (ValidateException $e) {
                Db::rollback();
            } catch (PDOException $e) {
                Db::rollback();
            } catch (Exception $e) {
                Db::rollback();
            }
            
            $sendInfo = array();
            $sendInfo['cmd'] = 'sendOnlineMeg';
            $sendInfo['data'] = array();
            $sendInfo['data']['did'] = $did;
            $sendInfo['data']['online'] = $online;
            $client = new WebSocketClient;
            $client->connect('127.0.0.1', '9292', '/');
            $sendText = json_encode($sendInfo);
            $rs = $client->sendData($sendText);
            $client->disconnect();
            $this->success('设置成功');
        }
    }
    
   
    
                    

    
    
    
    
    
}

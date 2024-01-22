<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
declare(ticks=1);



use Workerman\Timer;
use Workerman\Connection\AsyncTcpConnection;
use Common\Http;
use \GatewayWorker\Lib\Gateway;


/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    static public $worker = null;
   
   // private $businessWorker;
    public static function onWorkerStart($businessWorker)
    {
      
       
    }
    
   
    
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
        // 向当前client_id发送数据 
        Gateway::sendToClient($client_id, "connect");
        
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $message)
   {
       $data = json_decode($message,true);
        if(empty($data['cmd'])){
            $sendData = array(
                'cmd'=>'errorRes',
                'msg' => 'Illegal information'
            );
            $r = json_encode($sendData);
            Gateway::sendToClient($client_id,$r);
            Gateway::closeClient($client_id);
        }else if(empty($data['data']) && $data['cmd']!='ping'){
            $sendData = array(
                'cmd'=>'errorRes',
                'msg' => 'Illegal information'
            );
            $r = json_encode($sendData);
            Gateway::sendToClient($client_id,$r);
            Gateway::closeClient($client_id);
        }else{
            $host = 'http://iotm3.yafrm.com/';
         
        switch ($data['cmd']){
            case 'ping':
                //心跳
                $sendData = array(
                'cmd'=>'pung'
                );
                $r = json_encode($sendData);
                Gateway::sendToClient($client_id,$r);
                break;
            case 'toSubscribe':
                //信息订阅
                if(empty($data['data']['terminal']) || empty($data['data']['token']) || empty($data['data']['did'])){
                    $sendData = array(
                        'cmd'=>'errorRes',
                        'msg' => 'Illegal information'
                    );
                    $r = json_encode($sendData);
                    Gateway::sendToClient($client_id,$r);
                    Gateway::closeClient($client_id);
                }elseif ($data['data']['terminal'] !='app' && $data['data']['terminal']!='client'){
                    $sendData = array(
                        'cmd'=>'errorRes',
                        'msg' => 'Illegal terminal'
                    );
                    $r = json_encode($sendData);
                    Gateway::sendToClient($client_id,$r);
                    Gateway::closeClient($client_id);
                }else{
                
                //检查用户是否有效
                $url = $host."api/SubscribeTask/checkMeg";
                $sData = array(
                    'did' => $data['data']['did'],
                    'token' => $data['data']['token']
                );
                $tData = Http::post($url,$sData);
                $tData = json_decode($tData,true);
                print_r($tData);
                if($tData['code'] == 1){
                    $online = $tData['data']['online'];
                    $sendData = array(
                        'cmd'=>'successRes',
                        'msg' => 'success',
                        'data'=>array(
                            'online' => $online
                        )
                    );
                    $r = json_encode($sendData);
                    Gateway::joinGroup($client_id, $data['data']['did'].'_'.$data['data']['terminal']);
                    Gateway::sendToClient($client_id,$r);
                }else{
                    $sendData = array(
                        'cmd'=>'errorRes',
                        'msg' => $tData['msg']
                    );
                    $r = json_encode($sendData);
                    
                    Gateway::sendToClient($client_id,$r);
                }
                }
                break;
           case 'sendNotice':
               //通知转发
               if(empty($data['data']['terminal']) ||empty($data['data']['noticesign']) || empty($data['data']['recordid'])|| empty($data['data']['did'])){
                   $sendData = array(
                       'cmd'=>'errorRes',
                       'msg' => 'Illegal information'
                   );
                   $r = json_encode($sendData);
                   Gateway::sendToClient($client_id,$r);
                   Gateway::closeClient($client_id);
               }elseif ($data['data']['terminal'] !='app' && $data['data']['terminal']!='client'){
                   $sendData = array(
                       'cmd'=>'errorRes',
                       'msg' => 'Illegal terminal'
                   );
                   $r = json_encode($sendData);
                   Gateway::sendToClient($client_id,$r);
                   Gateway::closeClient($client_id);
               }else{
                   $sendData = array(
                       'cmd'=>'sendNotice',
                       'msg' => 'success',
                       'data' => array(
                           'noticesign' => $data['data']['noticesign'],
                           'recordid' => $data['data']['recordid']
                       )
                   );
                   $r = json_encode($sendData);
                   
                  
                   Gateway::sendToGroup($data['data']['did'].'_'.$data['data']['terminal'],$r);
                   $sendData = array(
                       'cmd'=>'successRes',
                       'msg' => 'success'
                   );
                   $r = json_encode($sendData);
                   Gateway::sendToClient($client_id,$r);
               }
               break;
           case 'sendUnitMeg':
                  //传感器信息转发
               if(empty($data['data']['did'])){
                   $sendData = array(
                       'cmd'=>'errorRes',
                       'msg' => 'Illegal information'
                   );
                   $r = json_encode($sendData);
                   Gateway::sendToClient($client_id,$r);
                   Gateway::closeClient($client_id);
               }else{
                   $sendData = array(
                       'cmd'=>'successRes',
                       'msg' => 'success',
                       'data' => $data['data']
                   );
                   $r = json_encode($sendData);
                   Gateway::sendToGroup($data['data']['did'].'_app',$r);
                   Gateway::sendToGroup($data['data']['did'].'_client',$r);
                   $sendData = array(
                       'cmd'=>'successRes',
                       'msg' => 'success'
                   );
                   $r = json_encode($sendData);
                   Gateway::sendToClient($client_id,$r);
               }
               break;
           case 'sendOnlineMeg':
               //上下线转发
               if(empty($data['data']['did'])){
                   $sendData = array(
                       'cmd'=>'errorRes',
                       'msg' => 'Illegal information'
                   );
                   $r = json_encode($sendData);
                   Gateway::sendToClient($client_id,$r);
                   Gateway::closeClient($client_id);
               }else{
                   $sendData = array(
                       'cmd'=>'statusRes',
                       'msg' => 'success',
                       'data' =>array(
                           'did' => $data['data']['did'],
                           'online' => $data['data']['online']
                       )
                   );
                   $r = json_encode($sendData);
                   Gateway::sendToGroup($data['data']['did'].'_app',$r);
                   Gateway::sendToGroup($data['data']['did'].'_client',$r);
                   $sendData = array(
                       'cmd'=>'successRes',
                       'msg' => 'success',
                   );
                   $r = json_encode($sendData);
                   Gateway::sendToClient($client_id,$r);
               }
               break;
          case 'toLogin':
              //登录
              if(empty($data['data']['appid']) || empty($data['data']['token'])){
                  $sendData = array(
                      'cmd'=>'errorRes',
                      'msg' => 'Illegal information'
                  );
                  $r = json_encode($sendData);
                  Gateway::sendToClient($client_id,$r);
                  Gateway::closeClient($client_id);
              }elseif ($data['data']['appid'] !='1GAn4wl2Lljhu9I2^Ya5czTL&5tIvr3M'){
                  $sendData = array(
                      'cmd'=>'errorRes',
                      'msg' => 'Illegal appid'
                  );
                  $r = json_encode($sendData);
                  Gateway::sendToClient($client_id,$r);
                  Gateway::closeClient($client_id);
                  
              }else{
                  
                  //检查用户是否有效
                  $url = $host."api/unit/checkMember";
                  $sData = array(
                      'token' => $data['data']['token']
                  );
                  $tData = Http::post($url,array('token' => $data['data']['token']));
                  $tData = json_decode($tData,true);
                
                  if($tData['code'] == 1){
                      $uid = $tData['data']['uid'];
                      Gateway::bindUid($client_id, $uid);
                      $sendData = array(
                          'cmd'=>'loginRes',
                          'data' => array(
                              'result'=>true,
                              'ResultCode'=>0,
                              'msg'=>"登录成功"
                          )
                      );
                      $r = json_encode($sendData);
                      Gateway::sendToClient($client_id,$r);
                  }else{
                      $sendData = array(
                          'cmd'=>'loginRes',
                          'data' => array(
                              'result'=>false,
                              'ResultCode'=>1,
                              'msg'=>$tData['msg']
                          )
                      );
                      $r = json_encode($sendData);
                      
                      Gateway::sendToClient($client_id,$r);
                      Gateway::closeClient($client_id);
                  }
              }
              break;
          case 'sendMsg':
              //转发虚拟组建单元传感器信息
             $uid =  Gateway::getUidByClientId($client_id);
             if($uid == null){
                 $sendData = array(
                     'cmd'=>'errorRes',
                     'msg' => 'Illegal member'
                 );
                 $r = json_encode($sendData);
                 Gateway::sendToClient($client_id,$r);
                 Gateway::closeClient($client_id);
             }else{
                 if(empty($data['data']['did']) || empty($data['data']['sensorData']) || empty($data['data']['onoffData'])){
                     $sendData = array(
                         'cmd'=>'errorRes',
                         'msg' => 'Illegal information'
                     );
                     $r = json_encode($sendData);
                     Gateway::sendToClient($client_id,$r);
                     Gateway::closeClient($client_id);
                 }else{
                     $data = json_encode($data['data']);
                     $url = $host."api/unit/setMessage";
                     $tData = Http::sendByRaw($url, $data);
                     $tData = json_decode($tData,true);
                     if($tData['code'] == 1){
                         $sendData = array(
                             'cmd'=>'sendMsgRes',
                             'data' => array(
                                 'result'=>true,
                                 'ResultCode'=>0,
                                 'msg'=>"录入成功"
                             )
                         );
                         $r = json_encode($sendData);
                         Gateway::sendToClient($client_id,$r);
                     }else{
                         $sendData = array(
                             'cmd'=>'sendMsgRes',
                             'data' => array(
                                 'result'=>false,
                                 'ResultCode'=>1,
                                 'msg'=>$tData['msg']
                             )
                         );
                         $r = json_encode($sendData);
                         
                         Gateway::sendToClient($client_id,$r);
                         Gateway::closeClient($client_id);
                     }
                 }
                 
             }
              break;
          case 'onLineMsg':
              //转发虚拟组建单元上下线信息
              $uid =  Gateway::getUidByClientId($client_id);
              if($uid == null){
                  $sendData = array(
                      'cmd'=>'errorRes',
                      'msg' => 'Illegal member'
                  );
                  $r = json_encode($sendData);
                  Gateway::sendToClient($client_id,$r);
                  Gateway::closeClient($client_id);
              }else{
                  if(empty($data['data']['did'])  || empty($data['data']['updatetime'])){
                      $sendData = array(
                          'cmd'=>'errorRes',
                          'msg' => 'Illegal information'
                      );
                      $r = json_encode($sendData);
                      Gateway::sendToClient($client_id,$r);
                      Gateway::closeClient($client_id);
                  }else{
                      $sendData = array();
                      $sendData['did'] = $data['data']['did'];
                      $sendData['online'] = $data['data']['online'];
                      $sendData['updatetime'] = $data['data']['updatetime'];
                      $url = $host."api/unit/setOnline";
                      $re =  Http::post($url,$sendData);
                      $re = json_decode($re,true);
                      if($re['code'] == 1){
                          $sendData = array(
                              'cmd'=>'onLineRes',
                              'data' => array(
                                  'result'=>true,
                                  'ResultCode'=>0,
                                  'msg'=>"修改成功"
                              )
                          );
                          $r = json_encode($sendData);
                          Gateway::sendToClient($client_id,$r);
                      }else{
                          $sendData = array(
                              'cmd'=>'onLineRes',
                              'data' => array(
                                  'result'=>false,
                                  'ResultCode'=>1,
                                  'msg'=>$re['msg']
                              )
                          );
                          $r = json_encode($sendData);
                          
                          Gateway::sendToClient($client_id,$r);
                          Gateway::closeClient($client_id);
                      }
                      
                  }
                  
              }
              break;
          case 'toControl':
              //转发虚拟组建单元操作开关任务信息
              $sendData = array();
              if(empty($data['data'])){
                  $sendData = array(
                      'cmd'=>'errorRes',
                      'msg' => 'Illegal msg'
                  );
                  $r = json_encode($sendData);
                  Gateway::sendToClient($client_id,$r);
                  Gateway::closeClient($client_id);
              }else{
                  
                  foreach ($data['data'] as $k=>$v){
                  foreach ($v['uid']  as $uid){
                      $sendData[$uid][$k]['did'] = $v['did'];
                      $sendData[$uid][$k]['onoff'] = $v['onoff'];
                      $sendData[$uid][$k]['number'] = $v['number'];
                      $sendData[$uid][$k]['runTime'] = $v['runTime'];
                      $sendData[$uid][$k]['way'] = $v['way'];
                      $sendData[$uid][$k]['taskId'] = intval($v['taskId']);
                  }
              }
              foreach ($sendData as $k=>$v){
                  $r = array();
                  $r['cmd'] = 'toControl';
                  $r['data'] = $v;
                  $sendText = json_encode($r);
                  Gateway::sendToUid($k,$sendText);
              }
              }
              break;
              
          case 'cancelControl':
              //转发虚拟组建单元关闭操作开关任务信息
              $sendData = array();
              if(empty($data['data'])){
                  $sendData = array(
                      'cmd'=>'errorRes',
                      'msg' => 'Illegal msg'
                  );
                  $r = json_encode($sendData);
                  Gateway::sendToClient($client_id,$r);
                  Gateway::closeClient($client_id);
              }else{
                  
                  foreach ($data['data'] as $k=>$v){
                      foreach ($v['uid']  as $uid){
                          $sendData[$uid][$k]['taskId'] = $v['taskId'];
                      }
                  }
                  foreach ($sendData as $k=>$v){
                      $r = array();
                      $r['cmd'] = 'cancelControl';
                      $r['data'] = $v;
                      $sendText = json_encode($r);
                      Gateway::sendToUid($k,$sendText);
                  }
              }
              break;
          case 'onoffMsg':
              //转发虚拟组建单元开关触发信息
              $uid =  Gateway::getUidByClientId($client_id);
              if($uid == null){
                  $sendData = array(
                      'cmd'=>'errorRes',
                      'msg' => 'Illegal member'
                  );
                  $r = json_encode($sendData);
                  Gateway::sendToClient($client_id,$r);
                  Gateway::closeClient($client_id);
              }else{
                  if(empty($data['data']['did']) || empty($data['data']['number']) || empty($data['data']['runTime']) || empty($data['data']['way'])){
                      $sendData = array(
                          'cmd'=>'errorRes',
                          'msg' => 'Illegal information'
                      );
                      $r = json_encode($sendData);
                      Gateway::sendToClient($client_id,$r);
                      Gateway::closeClient($client_id);
                  }else{
                      
                      $sendData = array();
                      $sendData['did'] = $data['data']['did'];
                      $sendData['onoff'] = $data['data']['onoff'];
                      $sendData['number'] = $data['data']['number'];
                      $sendData['runTime'] = $data['data']['runTime'];
                      $sendData['way'] = $data['data']['way'];
                      $sendData['tid'] = $data['data']['tid'];
                      $url = $host."api/unit/setOnoff";
                      $ress =  Http::post($url,$sendData);
//                       $re = explode("result",$ress);
//                       $result = '{"result'.$re[1];
                      $res = json_decode($ress,true);
                    
                      if($res['code'] == 1){
                          $sendData = array(
                              'cmd'=>'onOffRes',
                              'data' => array(
                                  'result'=>true,
                                  'ResultCode'=>0,
                                  'msg'=>"修改成功"
                              )
                          );
                          $r = json_encode($sendData);
                          Gateway::sendToClient($client_id,$r);
                      }else{
                          $sendData = array(
                              'cmd'=>'onOffRes',
                              'data' => array(
                                  'result'=>false,
                                  'ResultCode'=>1,
                                  'msg'=>$res['msg']
                              )
                          );
                          $r = json_encode($sendData);
                          
                          Gateway::sendToClient($client_id,$r);
                          Gateway::closeClient($client_id);
                      }
                      
                  }
                  
              }
              break;
         
           default:
               
               if($data['cmd'] != 'cancelRes' && $data['cmd'] != 'controlRes'){
               $sendData = array(
               'cmd'=>'errorRes',
               'msg' => 'Illegal information'
                   );
               $r = json_encode($sendData);
               Gateway::sendToClient($client_id,$r);
               Gateway::closeClient($client_id);
               break;
               }
        }
        }
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id)
   {
//        // 向所有人发送 
//        GateWay::sendToAll("$client_id logout\r\n");
   }
}

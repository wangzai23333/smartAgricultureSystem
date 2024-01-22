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

use Workerman\Http;
use Workerman\Timer;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Connection\TcpConnection;
use \Workerman\Worker;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

// 自动加载类
require_once __DIR__ . '/../../vendor/autoload.php';

// bussinessWorker 进程
$worker = new Worker();
// worker名称
$worker->name = 'mqttWorker';
// bussinessWorker进程数量
$worker->count = 1;

$worker->onWorkerStart = 'startLink';
/**
 * 连接、登录
 *
 * @return void
 */
function startLink()
{
    
    $mqtt = new \Workerman\Mqtt\Client('mqtt://120.78.140.176:1883');
    $mqtt->onConnect = function($mqtt) {
        
        $room = 'UNIT/toSend/+';
        echo "connect mqtt success!\r\n";
        
        $mqtt->subscribe($room, null, function(){
            echo "join room success! type something to talk!\r\n";
        });
            
            $mqtt->onMessage = function($room, $message,$mqtt){
                //echo "room[$room]:", $message, "\r\n";
               
                $did = ltrim($room,'UNIT/toSend/');
                $data = json_decode($message,true);
                $sendData = array();
                $sendData['head'] = array(
                  'symbol' => 'IOTM',
                  'msgId' => $mqtt->getRandromStr()
                );
                if(empty($data) || empty($data['head'])){
                      $send = 'UNIT/toReceive/'.$did;
                    $sendData['head']['cmd'] = 'sendMsgRes';
                    $sendData['body'] = array(
                        'result' => false,
                        'ResultCode' => 0,
                        'msg'=> '数据为空'
                    );
                    $sendText = json_encode($sendData);
                    $mqtt->publish($send, $sendText);
                    
                }else{
                    if(empty($data['head']['symbol']) || $data['head']['symbol']!='UNIT'){
                          $send = 'UNIT/toReceive/'.$did;
                        $sendData['head']['cmd'] = 'sendMsgRes';
                        $sendData['body'] = array(
                            'result' => false,
                            'ResultCode' => 0,
                            'msg'=> '数据不合法'
                        );
                        $sendText = json_encode($sendData);
                        $mqtt->publish($send, $sendText);
                      
                    }else{
                        $send = 'UNIT/toReceive/'.$did;
                        switch ($data['head']['cmd']){
                            case 'ping':
                                //心跳
                                $sendData['head']['cmd'] = 'pung';
                                $sendText = json_encode($sendData);
                                $mqtt->publish($send, $sendText);
                                
                                break;
                            case 'sendMsg':
                                $sendData['head']['cmd'] = 'sendMsgRes';
                                if(empty($data['body']['did']) || empty($data['body']['sensorData']) || empty($data['body']['onoffData'])){
                                    $sendData['body'] = array(
                                        'result' => false,
                                        'ResultCode' => 0,
                                        'msg'=> '数据不合法'
                                    );
                                    $sendText = json_encode($sendData);
                                    $mqtt->publish($send, $sendText); echo "4444";
                                }else{
                                    $data = json_encode($data['body']);
                                    $host = 'http://iotm3.yafrm.com/';
                                    $url = $host."api/unit/setMessage";
                                    $tData = Http::sendByRaw($url, $data);
                                    $tData = json_decode($tData,true);
                                    if($tData['code'] == 1){
                                        $sendData['body'] = array(
                                            'result' => true,
                                            'ResultCode' => 1,
                                            'msg'=> '录入成功'
                                        );
                                        $sendText = json_encode($sendData);
                                        $mqtt->publish($send, $sendText);
                                    }else{
                                        
                                        $sendData['body'] = array(
                                            'result' => false,
                                            'ResultCode' => 0,
                                            'msg'=>$tData['msg']
                                        );
                                        $sendText = json_encode($sendData);
                                        $mqtt->publish($send, $sendText);
                                }
                                }
                                break;
                            case 'onLineMsg':
                                $sendData['head']['cmd'] = 'onOffRes';
                                if(empty($data['body']['did'])  || empty($data['body']['updatetime'])){
                                    $sendData['body'] = array(
                                        'result' => false,
                                        'ResultCode' => 0,
                                        'msg'=> '数据不合法'
                                    );
                                    $sendText = json_encode($sendData);
                                    $mqtt->publish($send, $sendText);
                                }else{
                                    $sendToData = array();
                                    $sendToData['did'] = $data['body']['did'];
                                    $sendToData['online'] = $data['body']['online'];
                                    $sendToData['updatetime'] = $data['body']['updatetime'];
                                    $host = 'http://iotm3.yafrm.com/';
                                    $url = $host."api/unit/setOnline";
                                    $re =  Http::post($url,$sendToData);
                                    $re = json_decode($re,true);
                                    if($re['code'] == 1){
                                        $sendData['body'] = array(
                                            'result' => true,
                                            'ResultCode' => 1,
                                            'msg'=> '修改成功'
                                        );
                                        $sendText = json_encode($sendData);
                                        $mqtt->publish($send, $sendText);
                                    }else{
                                  
                                        $sendData['body'] = array(
                                            'result'=>false,
                                            'ResultCode'=>0,
                                            'msg'=>$re['msg']
                                        );
                                        $sendText = json_encode($sendData);
                                        $mqtt->publish($send, $sendText);
                                    }
                                    
                                }
                                break;
                                default:
                                    $sendData['body'] = array(
                                    'result'=>false,
                                    'ResultCode'=>0,
                                    'msg'=> '数据不合法'
                                    );
                                    $sendText = json_encode($sendData);
                                    $mqtt->publish($room, $sendText);
                                break;
                        }
                    }
                }
               
                
            };
         
            
           
    };
    $mqtt->connect();
    
}




//如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

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
use Workerman\Redis\Client;
use \GatewayWorker\Lib\Gateway;
use Event\Linkage;
use Event\TimerEvent;
use Event\Notice;

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
        
        session_start();
       
        global $redis;
        $redis = new Client('redis://127.0.0.1:6379');
        $redis->select(2);
        // Linkage::addLinkageCheckTask($redis);
        // Linkage::addLinkageTask($redis);
        // Notice::addNoticeCheckTask($redis);
         Timer::delAll();
          TimerEvent::recoveryTask($redis);
   
        self::$worker = $businessWorker;
       
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
        Gateway::sendToClient($client_id, "Hello $client_id\r\n");
        // 向所有人发送
        Gateway::sendToAll("$client_id login\r\n");
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $message)
   {
        // 向所有人发送 
//        Gateway::sendToAll($message);
//        Gateway::sendToClient($client_id,$message);
        //echo $message;
        global $redis;
        $data = json_decode($message,true);
        if(empty($data['cmd'])){
            Gateway::sendToClient($client_id,'Illegal information');
        }else if(empty($data['data'])){
            Gateway::sendToClient($client_id,'Illegal information');
        }else{
            
        switch ($data['cmd']){
            case 'addTask':
                $option = $data['data']['option'];
               
                if(empty($data['data']['url']) || empty($data['data']['title'])){
                    Gateway::sendToClient($client_id,'Illegal information');
                }else{
                   
                   
                        $r = TimerEvent::addTask($data['data']['title'], $data['data']['url'],$option,intval($data['data']['time']),$data['data']['persistent'],$data['data']['checkUrl']);
                        $redis->sAdd($data['data']['title'], $r);
                    
                    Gateway::sendToClient($client_id,'success');
               
              
                }
                break;
           case 'delTask':
              // $timer_ids = $redis->smembers($data['data']['title']);
               $redis->sort($data['data']['title'], [], function ($result) {
                   $timer_ids = $result; 
                   if(!empty($timer_ids)){
                       foreach($timer_ids as $id){
                            $a = Timer::del($id);
                         }
                   }
               }); 
            
              $redis->del($data['data']['title']);
                
               Gateway::sendToClient($client_id,'success');
               break;
           default:
               Gateway::sendToClient($client_id,'Illegal information');
               break;
        }
        }
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id)
   {
       // 向所有人发送 
       GateWay::sendToAll("$client_id logout\r\n");
   }
}

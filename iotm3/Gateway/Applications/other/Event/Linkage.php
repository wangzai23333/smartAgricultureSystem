<?php

namespace Event;

use Common\Http;
use Workerman\Timer;
use Workerman\Connection\AsyncTcpConnection;
/**
 * 联动相关
 */
class Linkage
{
   
    
    /**
     * 添加已有需检测联动对应任务
     *
     * @return void
     */
    public static function addLinkageCheckTask($redis)
    {
    
        $host = 'http://iotm3.yafrm.com/';
        $url = $host."api/task/getLinkageCheckTask";
        $taskData = Http::post($url);
        $taskData = json_decode($taskData,true);
        if($taskData['code'] == 1){
        $taskList = $taskData['data'];
        foreach ($taskList as $v){
            $id = $v['id'];
            $delayTime = $v['delaytime'] == 0 ? 5 : $v['delaytime'] * 60;
            $sendUrl = $host."api/task/checkLinkage?id={$id}";
            $checkUrl = $host."api/task/checkLinkageTask?id={$id}";
            $r = TimerEvent::addTask('cherkLinkage'.$id, $sendUrl,array('id'=>$id), $delayTime,true,$checkUrl);
            $redis->sAdd('cherkLinkage'.$id, $r);
            
        }
        }

    }

    /**
     * 添加已有需检测联动对应任务
     *
     * @return void
     */
    public static function addLinkageTask($redis)
    {
        $host = 'http://iotm3.yafrm.com/';
        $url = $host."api/task/getLinkageTask";
        $taskData = Http::post($url);
        $taskData = json_decode($taskData,true);
        if($taskData['code'] == 1){
            $taskList = $taskData['data'];
            foreach ($taskList as $v){
                $id = $v['id'];
                $thisTime = time();
                $time = intval($v['executetime'] /1000 - $thisTime);
                if($time>0){
                $time = $time<=0?5:$time;
                $sendUrl = $host."api/task/runLinkageTask?id={$id}";
                $checkUrl = $host."api/task/checkLinkageRunTask?id={$id}";
                $r = TimerEvent::addTask('task_'.$v['did'].$v['switchnum'], $sendUrl,array('id'=>$id), $time,false,$checkUrl);
                $redis->sAdd('task_'.$v['did'].$v['switchnum'], $r);
                }
                
        }
    }
    
}
    
    
  
}
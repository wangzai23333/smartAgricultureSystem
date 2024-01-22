<?php

namespace Event;

use Common\Http;
use Workerman\Timer;
use Workerman\Connection\AsyncTcpConnection;
/**
 * 联动相关
 */
class Notice
{
    
    /**
     * 添加已有需检测通知对应任务
     *
     * @return void
     */
    public static function addNoticeCheckTask($redis)
    {
        $host = 'http://iotm3.yafrm.com/';
        $url = $host."api/task/getNoticeCheckTask";
        $taskData = Http::post($url);
        $taskData = json_decode($taskData,true);
        if($taskData['code'] == 1){
        $taskList = $taskData['data'];
        foreach ($taskList as $v){
            $id = $v['id'];
            $delayTime = $v['keeptime'] == 0 ? 30 : $v['keeptime'] * 60;
            $sendUrl = $host."api/task/checkNotice?id={$id}";
            $checkUrl = $host."api/task/checkNoticeTask?id={$id}";
            $r = TimerEvent::addTask('cherkNotice'.$id, $sendUrl,array('id'=>$id), $delayTime,true,$checkUrl);
            $redis->sAdd('cherkNotice'.$id, $r);
//             $_SESSION['cherkLinkage'.$id] =  Timer::add($delayTime, function()use($id,$host){
//                 $url = $host."api/task/checkLinkage?id={$id}";
//                 $result = Http::post($url,array('id'=>$id));
//             });
            
        }
        }

    }

   
    
    
  
}
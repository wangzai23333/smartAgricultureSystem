<?php

namespace Event;

use Common\Http;
use Workerman\Timer;

/**
 * 定时任务相关
 */

class TimerEvent
{
   

    /**
     * 添加任务
     *$persistent:true为重复 false为单次
     * @return void
     */
    public static function addTask($title,$url,$option=Array(),$time,$persistent = true,$checkUrl='')
    {
            $time = $time == 0 ? 5 : $time;
            $persistent = $persistent != 1?false:true;
                $timerId =  Timer::add($time, function()use(&$timerId,$url,$option,$title,$checkUrl){
//                     if($checkUrl != ''){
//                     $resData = Http::get($checkUrl);
//                     $resData = json_decode($resData,true);
//                     $status = $resData['result'];
//                     if($status){
//                         $result = Http::sendRequest($url,$option,'post');
//                     }else{
//                         unset($_SESSION[$title]);
//                         Timer::del($timerId);
//                     }
//                     }else{
                    $result = Http::sendRequest($url,$option,'post');
               //     }
            },array(),$persistent);
                   // $_SESSION[$title] = $timerId;
                    return $timerId;
           
    
    }
    
  
    /**
     * 任务整理
     * @return void
     */
    public static function recoveryTask($redis){
       Timer::delAll();
        $host = 'http://iotm3.yafrm.com/';
        //通知检测任务
        $url = $host."api/task/getNoticeCheckTask";
        $taskData = Http::post($url);
        $taskData = json_decode($taskData,true);
        if(!empty($taskData)){
        if($taskData['code'] == 1){
            $taskList = $taskData['data'];
            foreach ($taskList as $v){
                $id = $v['id'];
              //  TimerEvent::delTask($redis, 'cherkNotice'.$id);
                $delayTime = $v['keeptime'] == 0 ? 30 : $v['keeptime'] * 60;
                $sendUrl = $host."api/task/checkNotice?id={$id}";
                $checkUrl = $host."api/task/checkNoticeTask?id={$id}";
                $r = TimerEvent::addTask('cherkNotice'.$id, $sendUrl,array('id'=>$id), $delayTime,true,$checkUrl);
                $redis->sAdd('cherkNotice'.$id, $r);
        }
    }
    
        }
    //联动检测任务
    $url = $host."api/task/getLinkageCheckTask";
    $taskData = Http::post($url);
    $taskData = json_decode($taskData,true);
    if(!empty($taskData)){
    if($taskData['code'] == 1){
        $taskList = $taskData['data'];
        foreach ($taskList as $v){
            $id = $v['id'];
           // TimerEvent::delTask($redis, 'cherkLinkage'.$id);
            $delayTime = $v['delaytime'] == 0 ? 5 : $v['delaytime'] * 60;
            $sendUrl = $host."api/task/checkLinkage?id={$id}";
            $checkUrl = $host."api/task/checkLinkageTask?id={$id}";
            $r = TimerEvent::addTask('cherkLinkage'.$id, $sendUrl,array('id'=>$id), $delayTime,true,$checkUrl);
            $redis->sAdd('cherkLinkage'.$id, $r);
            
        }
    }
    }
   
    
    $timerId =  Timer::add('43200', function()use($redis){
        
        TimerEvent::recoveryTask($redis);
        
    },[],true);
    }
  
    /**
     * 删除任务
     */
    public static function delTask($redis,$title)
    {
        $redis->sort($title, [], function ($result) {
            $timer_ids = $result;
            if(!empty($timer_ids)){
                foreach($timer_ids as $id){
                    $a = Timer::del($id);
                }
            }
        });
            $redis->del($title);
    }
    
  
}
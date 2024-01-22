<?php
namespace app\common\controller;

use app\common\library\Phpmqtt;
use think\Db;
use think\exception\HttpResponseException;



class Emqx
{
    
    
    public function __construct()
    {
        $this->server = '127.0.0.1';     // change if necessary
        $this->port = 1883;                     // change if necessary
        $this->username = '';                   // set your username
        $this->password = '';                   // set your password
        $this->client_id = 'mqttx_12345'; // make sure this is unique for connecting to sever - you could use uniqid()
        ignore_user_abort(); // 后台运行
        set_time_limit(18000); // 取消脚本运行时间的超时上限
   
    }
    /***
     * 信息推送
     * 
     * **/
    public function send($meg,$topic)
    {
        
        $mqtt = new Phpmqtt($this->server, $this->port, $this->client_id);
        
        if ($mqtt->connect(true, NULL, $this->username, $this->password)) {
            $mqtt->publish($topic, $meg, 0, false);
            $mqtt->close();
        } else {
            $result = [
                'result' => false,
                'code' => 0,
                'msg'  => '超时',
                'data' =>null
            ];
            $r =  json($result);
            throw new HttpResponseException($r);
            exit();
        }
    }
    
    /***
     * 订阅持续监听信息
     * ***/
    public function subscribe($topic){
        ignore_user_abort(); // 后台运行
        set_time_limit(0); // 取消脚本运行时间的超时上限
        $mqtt = new Phpmqtt($this->server, $this->port, $this->client_id);
        if(!$mqtt->connect(true, NULL, $this->username, $this->password)) {
            $result = [
                'result' => false,
                'code' => 0,
                'msg'  => '连接失败',
                'data' =>null
            ];
            $r =  json($result);
            throw new HttpResponseException($r);
            exit();
        }
        $mqtt->debug = false;
        $topics[$topic] = array('qos' => 0, 'function' => 'procMsg');
        $mqtt->subscribe($topics, 0);
        $info = Db::name('iot_dev')->where(
            array('type'=>'chacang')
            )->find();
        while($mqtt->proc() && $info['get_status'] == 'on') {
            $info = Db::name('iot_dev')->where(
                array('type'=>'chacang')
                )->find();
              
        }
        $mqtt->close();
    }
  
    /***
     * 订阅等到信息就停止
     * 
     * ***/
    public function  wait($topic){
        ignore_user_abort(); // 后台运行
        set_time_limit(0); // 取消脚本运行时间的超时上限
        $mqtt = new Phpmqtt($this->server, $this->port, $this->client_id);
        if(!$mqtt->connect(true, NULL, $this->username, $this->password)) {
            $result = [
                'result' => false,
                'code' => 0,
                'msg'  => '连接失败',
                'data' =>null
            ];
            $r =  json($result);
            throw new HttpResponseException($r);
            exit();
        }
        $re =  $mqtt->subscribeAndWaitForMessage($topic, 0);
        $mqtt->close();
        return $re;
        
    }
    
    /***
     * 订阅并获取返回
     *
     * ***/
    public function  subscribeForBack($getTopic,$sendTopic,$sendMeg){
        set_time_limit(18000);
        $time = new ChangeTime();
        $mqtt = new Phpmqtt($this->server, $this->port, 'yaheen_'.$time->getMsectime());
        if(!$mqtt->connect(true, NULL, $this->username, $this->password)) {
            $result = [
                'result' => false,
                'code' => 0,
                'msg'  => '连接失败',
                'data' =>null
            ];
            $r =  json($result);
            throw new HttpResponseException($r);
            exit();
        }

        $r = $mqtt->subscribeForMessage($getTopic, 0,$sendTopic,$sendMeg);  
        $mqtt->close();
        return $r;
    }
    
    /***
     * 订阅并发送
     *
     * ***/
    public function  subscribeForSend($sendTopic,$sendMeg){
        set_time_limit(18000);
        $time = new ChangeTime();
        $mqtt = new Phpmqtt($this->server, $this->port, 'yaheen_'.$time->getMsectime());
        if(!$mqtt->connect(true, NULL, $this->username, $this->password)) {
            $result = [
                'result' => false,
                'code' => 0,
                'msg'  => '连接失败',
                'data' =>null
            ];
            $r =  json($result);
            throw new HttpResponseException($r);
            exit();
        }
        $r = $mqtt->publish($sendTopic,$sendMeg);  
        $mqtt->close();
        return $r;
    }
    
    public function toInputMeg($data){
        if(!empty($data['Body']['DehumiDevs'])){
        $decHumList = $data['Body']['DehumiDevs'];
        $airList = $data['Body']['CentralAirc'];
        $airModel = new \app\common\model\Air;
        $airLogModel = new \app\common\model\AirLog;
        $dehModel = new \app\common\model\Dehumidifiers;
        $dehLogModel = new \app\common\model\DehumidifiersLog;
        $time = new ChangeTime();
        
        //抽湿机数据获取
        if(!empty($decHumList)>0){
            $log = $dehLogModel->get(array('MsgId'=>$data['head']['MsgId']));
            if(empty($log)){
        foreach ($decHumList as $v){
            $info = $dehModel->get(array('hid'=>$v['Id'],'dev_uid'=>$data['Body']['DevUID']));
           
            if(!empty($info) ){
                $onoff = $v['OnOffState'] == 'On' ? 'on' : 'off';
                $addData = array(
                    'msgid'=>$data['head']['MsgId'],
                    'hid' => $v['Id'],
                    'dhid' => $info['id'],
                    'onoff'=>$onoff,
                    'env_temp' =>$v['EnvTemp'],
                    'env_humi' =>$v['EnvHumi'],
                    'set_temp' =>$v['SetTemp'],
                    'set_humi' =>$v['SetHumi'],
                    'createtime' =>$time->getMsectime()
                );
                $r = $dehLogModel->insert($addData);
                //5秒内修改不进行other处理
                $thisTime = $time->getMsectime();
                $lessTime = $thisTime - $info['updatetime'];
                if(!empty($r) && $onoff != $info['status'] && $lessTime>5000){
                    $dehModel->update(array('status'=>$onoff,'updatetime'=>$time->getMsectime()),array('id'=>$info['id']));
                    Db::name('status_log')->insert(array(
                        'did' => $info['did'],
                        'number' => $info['num'],
                        'status' => $onoff,
                        'way' => 'other',
                        'createtime' => $time->getMsectime()
                    ));
                }
            }
        }
        }
        }
        //空调数据获取
        if(!empty($airList)>0){
            $log = $airLogModel->get(array('MsgId'=>$data['head']['MsgId']));
            if(empty($log)){
            foreach ($airList as $v){
                $info = $airModel->get(array('hid'=>$v['Id'],'dev_uid'=>$data['Body']['DevUID']));
                if(!empty($info)){
                    $onoff = $v['OnOffState'] == 'On' ? 'on' : 'off';
                    $addData = array(
                        'msgid'=>$data['head']['MsgId'],
                        'hid' => $v['Id'],
                        'aid' => $info['id'],
                        'onoff'=>$onoff,
                        'env_temp' =>$v['EnvTemp'],
                        'set_temp' =>$v['SetTemp'],
                        'workmode' =>$v['WorkMode'],
                        'fanvol' =>$v['FanVol'],
                        'createtime' =>$time->getMsectime()
                    );
                    $r = $airLogModel->insert($addData);
                    //5秒内修改不进行other处理
                    $thisTime = $time->getMsectime();
                    $lessTime = $thisTime - $info['updatetime'];
                    if(!empty($r) && $onoff != $info['status'] && $lessTime>5000){
                        $airModel->update(array('status'=>$onoff,'updatetime'=>$time->getMsectime()),array('id'=>$info['id']));
                        Db::name('status_log')->insert(array(
                            'did' => $info['did'],
                            'number' => $info['num'],
                            'status' => $onoff,
                            'way' => 'other',
                            'createtime' => $time->getMsectime()
                        ));
                    }
                }
            }
        }
        }
        
        }
    }
    
    /****
     * 
     * 
     * 获取msgid
     * 
     *****/
    public function getRandromStr(){
        $str = "";
        $key = "1234567890qwertyuiopasdfghjklzxcvbnmQAZWSXEDCRFVTGBYHNUJMIKOLP";
        $max = strlen($key)-1;
        for($i=0;$i<16;$i++){
            $str.=$key[mt_rand(0,$max)];
        }
        return $str;
    }
    
}

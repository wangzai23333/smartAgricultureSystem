<?php

namespace app\api\controller;
use app\common\controller\Api;
use app\common\library\Phpmqtt;

/**
 * 首页接口
 */
class Emqx extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    public function __construct()
    {
        $this->server = '';     // change if necessary
        $this->port = 1883;                     // change if necessary
        $this->username = '';                   // set your username
        $this->password = '';                   // set your password
        $this->client_id = 'mqttx_12345'; // make sure this is unique for connecting to sever - you could use uniqid()
        
    }
    public function index()
    {
        $server = '';     // change if necessary
        $port = 1883;                     // change if necessary
        $username = '';                   // set your username
        $password = '';                   // set your password
        $client_id = 'mqttx_2121212'; // make sure this is unique for connecting to sever - you could use uniqid()
        
        $mqtt = new Phpmqtt($server, $port, $client_id);
        
        if ($mqtt->connect(true, NULL, $username, $password)) {
            $mqtt->publish('test', 'Hello World! at ' . date('r'), 0, false);
            $mqtt->close();
        } else {
            echo "Time out!\n";
        }
    }
    public function getMeg(){
        $server = '';     // change if necessary
        $port = 1883;                     // change if necessary
        $username = '';                   // set your username
        $password = '';                   // set your password
        $client_id = 'mqttx_123456'; // make sure this is unique for connecting to sever - you could use uniqid()
        ignore_user_abort(); // 后台运行
        set_time_limit(0); // 取消脚本运行时间的超时上限
        $mqtt = new Phpmqtt($server, $port, $client_id);
        if(!$mqtt->connect(true, NULL, $username, $password)) {
            exit(1);
        }
        
        $mqtt->debug = true;
        
        $topics['test'] = array('qos' => 0, 'function' => 'procMsg');
        $mqtt->subscribe($topics, 0);
        
        while($re = true) {
            $re = $mqtt->proc();
            
        }
        echo $re;
        $mqtt->close();
    }
    function procMsg($topic, $msg){
        echo 'Msg Recieved: ' . date('r') . "\n";
        echo "Topic: {$topic}\n\n";
        echo "\t$msg\n\n";
        print_r("12\n\n\n\n");
    }
    
    public function  wait(){
        $server = '';     // change if necessary
        $port = 1883;                     // change if necessary
        $username = '';                   // set your username
        $password = '';                   // set your password
        $client_id = 'mqttx_21212132'; // make sure this is unique for connecting to sever - you could use uniqid()
        ignore_user_abort(); // 后台运行
        set_time_limit(0); // 取消脚本运行时间的超时上限
        $mqtt = new Phpmqtt($server, $port, $client_id);
        if(!$mqtt->connect(true, NULL, $username, $password)) {
            exit(1);
        }
        //$emqx = new Emqx;
       // $re = $this->send('1111', 'test');
        echo $mqtt->subscribeForMessage('test', 0);
        
    
          
         
    
       
        $mqtt->close();
        
       
    }
    /***
     * 信息推送
     *
     * **/
    public function send($meg,$public)
    {
        
        $mqtt = new Phpmqtt($this->server, $this->port, $this->client_id);
        
        if ($mqtt->connect(true, NULL, $this->username, $this->password)) {
            $mqtt->publish($public, $meg, 0, false);
            $mqtt->close();
        } else {
            echo "Time out!\n";
        }
    }
    
}

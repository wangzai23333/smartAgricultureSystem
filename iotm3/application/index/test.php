<?php


use app\common\library\Phpmqtt;
use think\Db;

    ignore_user_abort(); // 后台运行
    set_time_limit(0); // 取消脚本运行时间的超时上限
    $server = '8.134.124.145';     // change if necessary
    $port = 1883;                     // change if necessary
    $username = '';                   // set your username
    $password = '';                   // set your password
    $client_id = 'Test_01'; // make sure this is unique for connecting to sever - you could use uniqid()
    
    $mqtt = new Phpmqtt($server, $port, $client_id);
    if(!$mqtt->connect(true, NULL, $username, $password)) {
  
        exit(1);
    }
    $mqtt->debug = false;
    $topics['test'] = array('qos' => 0, 'function' => 'procMsg');
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




?>
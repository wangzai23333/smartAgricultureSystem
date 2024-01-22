<?php
namespace app\common\controller;





class Tdengine
{
    private $client;
    
    public function __construct()
    {
        //tdengine配置
        \Yurun\TDEngine\TDEngineManager::setClientConfig('yiotm3', new \Yurun\TDEngine\ClientConfig([
            'host'            => '',
            'hostName'        => '',
            'port'            => 6041,
            'user'            => '',
            'password'        => '',
            'db'              => '',
            'ssl'             => false,
            'timestampFormat' => \Yurun\TDEngine\Constants\TimeStampFormat::LOCAL_STRING,
            'keepAlive'       => true,
        ]));
        // 设置默认数据库
        \Yurun\TDEngine\TDEngineManager::setDefaultClientName('yiotm3');
        // 获取客户端对象（\Yurun\TDEngine\Client）
        $this->client = \Yurun\TDEngine\TDEngineManager::getClient();
   
    }
   
    public function queryBySql($sql){
        $re =  $this->client->sql($sql);
        $list = $re->getResponse(); 
        return $list;
    }
    
    
    public function queryForData($sql){
        $re =  $this->client->sql($sql);
        $list = $re->getData();
        return $list;
    }
    
    public function getNewByLabel($label,$where=''){
        $sql = "select last(ts) as ts,last(val) as val,last(istext) as istext,last(content_des) as content_des from model_{$label} {$where} group by did,port,sensorid ;";
        $list = $this->queryForData($sql);
        return $list;
    }
}

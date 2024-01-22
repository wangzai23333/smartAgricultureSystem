<?php

namespace app\api\controller;



use Complex\Exception;
use app\common\controller\Api;
use app\common\controller\Gizwits;
use app\common\controller\ChangeTime;
use app\common\controller\WebSocketClient;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\common\controller\Examine;
use app\common\controller\Calculation;
use app\common\controller\Emqx;
use Monolog\Processor\WebProcessor;
use app\common\library\Check;
use app\common\controller\Tdengine;
/**
 * 设备等相关对外接口
 */
class Test extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    
    
    public function _initialize()
    {
        parent::_initialize();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST,GET,OPTIONS');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        header("Content-Type: application/json; charset=UTF-8");
        header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
    }
   
    

    public function test(){
        $r = file_get_contents('php://input');
     
        $myfile = fopen("C:/eclipse-workspace/iot/ssss.txt", "w") or die("Unable to open file!");
        $txt = "Bill Gates\n";
        fwrite($myfile, $r);
    
        fclose($myfile);
        
        if(!empty($r)){
        $d = json_decode($r,true);
        $data = array();
        $data['touser'] = $d['touser'];
        $data['template_id'] = $d['template_id'];
        $data['page'] = $d['page'];
        $datas = $d['data'];
        $data['data'] = $datas;
        $data['miniprogram_state'] = $d['miniprogram_state'];
        $data['lang'] = $d['lang'];
        $this->success('获取成功',$data);
        }elseif ($this->request->post('touser')!=''){
            $data = array();
            $data['touser'] =$this->request->post('touser');
            $data['template_id'] = $this->request->post('template_id');
            $data['page'] = $this->request->post('page');
            $datas = $this->request->post('data');
            $ds =  json_decode($datas,true);
            $data['data'] = $ds;
            $data['miniprogram_state'] = $this->request->post('miniprogram_state');
            $data['lang'] = $this->request->post('lang');
            $this->success('获取成功',$data);
        }else{
            $this->error('获取失败');
        }
    }
   
    public function t(){
        $sendData =array();
        $sendData['Head'] = array(
            'Symbol'=>'YHM',
            'MsgId' =>'111',
            'CmdStr' =>'SetMachinePara'
        );
        $sendData['Body'] = array(
            'DevUID'=>'0000011CD0A0E8CED4696011',
            'DehumiDevs' => array(0=>array(
                'Id' => 1,
                'SetOnoff' =>'On'
            ))
        );
        $sendMeg = json_encode($sendData);
        print_r($sendMeg);
    }
   
    
    public function td(){
        $tdengine  = new Tdengine();
        $re = $tdengine->queryForSql("SHOW STABLES;");
        print_r($re);
    }
    
}

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
/**
 * 设备等相关对外接口
 */
class Subscribetask extends Api
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
        $this->check();
    }
    /**
     * ip检查
     */
    public function check(){
        $ip = $this->getIp();
     
        
    }
    
     /**
     * 订阅信息检查
     */
    public function checkMeg(){
        $did = $this->request->post('did');
        $token = $this->request->post('token');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        if(!empty($member)){
            $unit = $examine->checkUnit($did, $member['id']);
            if(!empty($unit)){
                $unitModel = new \app\common\model\ComponentUnit;
                $info = $unitModel->get(array('did'=>$did));
                $isonline = $info['online'];
                if($info['online'] == 1){
                    $res = $examine->checkOnLine($did);
                    if(!$res){
                        $isonline = 0;
                    }
                }
                $this->success('有效用户',array('online'=>$isonline));
            }else{
                $this->error('无效用户');
            }
        }else{
            $this->error('无效用户');
        }
        
    }
   
   
    
}

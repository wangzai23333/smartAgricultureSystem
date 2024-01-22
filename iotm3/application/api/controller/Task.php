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
class Task extends Api
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
        if($ip!='127.0.0.1' && $ip!='47.106.108.215'){
            // $this->error('无权访问');
        }
        
    }
    

     /**
     * 数据定时清理
     */
    public function cleanData(){
        //清理接口请求记录
         $apiModel = new \app\common\model\Linkage;
//         $apiInfo = $apiModel->get(['code'=>500]);
//         if(!empty($info)){
//             $r = $apiModel->where(array('code'=>200))->delete();
//         }else{
//             $sql = 'TRUNCATE TABLE `iot_apilog`';
//             $r = $apiModel->query($sql);
//         }
        //清理任务请求记录
        $taskInfo = Db::name('task_log')->where(['status'=>'fail'])->find();
        if(!empty($info)){
            $r = Db::name('task_log')->where(array('status'=>'success'))->delete();
        }else{
            $sql = 'TRUNCATE TABLE `iot_task_log`';
            $r = $apiModel->query($sql);
        }
        
        //清理任务记录
        $r = $apiModel->query("DELETE FROM `iot_run_task` WHERE  runTime/1000 < UNIX_TIMESTAMP(DATE_SUB(CURDATE(),INTERVAL 60 DAY)) ORDER BY id ASC LIMIT 100000");
        $r = $apiModel->query("DELETE FROM `iot_task_value` WHERE  createtime/1000 < UNIX_TIMESTAMP(DATE_SUB(CURDATE(),INTERVAL 60 DAY)) ORDER BY id ASC LIMIT 100000");
      
        //清理通知记录
        $r = $apiModel->query("DELETE FROM `iot_record_log` WHERE  createtime/1000 < UNIX_TIMESTAMP(DATE_SUB(CURDATE(),INTERVAL 60 DAY)) ORDER BY id ASC LIMIT 100000");
     
        
        $this->success('清理成功');
        
        
    }
   
    /**
     * 获取检测联动任务
     */
    public function getLinkageCheckTask(){
        $linkageModel = new \app\common\model\Linkage;
        $linkageList = $linkageModel->where(array('forbidden'=>0,'operationWay'=>'task'))->order('id','desc')->select();
        $linkageList = collection($linkageList)->toArray();
        if(empty($linkageList)){
            $this->error('暂无需检测的联动');
        }else{
        $this->success('获取成功',$linkageList);
        }
    }
    
    /**
     * 获取检测通知任务
     */
    public function getNoticeCheckTask(){
        $noticeModel = new \app\common\model\Notice;
        $noticeList = $noticeModel->where(array('forbidden'=>0))->order('id','desc')->select();
        $noticeList = collection($noticeList)->toArray();
        if(empty($noticeList)){
            $this->error('暂无需检测的通知');
        }else{
            $this->success('获取成功',$noticeList);
        }
    }
    

    
    /**
     * 检测通知
     */
    public function checkNotice(){
        $id = $this->request->post('id');
        $NoticeModel = new \app\common\model\Notice;
        $v = $NoticeModel->get(array('id'=>$id));
        if(empty($v)){
            $this->error('notice not exist');
        }
        if($v['forbidden'] == 1 ){
            $this->error('notice is invalid');
        }
        $gizwits = new Gizwits;
        $examine = new Examine;
        $time = new ChangeTime();
        $Calculation = new Calculation;
        $rangeModel = new \app\common\model\Range;
        
        //检查最大值最小值相关联数值，是否为最新
                    $thistime = $time->getMsectime();
                    Db::name('notice')->where(array('id'=>$v['id']))->update(array('runtime'=>$thistime));
                    $referenceInfo = $rangeModel->get(array('id'=>$v['referenceid']));
                    $referenceVal =  $Calculation->getRange($v['referenceid']);
                    
                    if($referenceInfo['rtype'] != 4){
                        if($referenceVal == false){
                            $this->error('Data failure');
                        }
                        $minVal =  $Calculation->getRange($v['minid']);
                        if($minVal == false){
                            $this->error('Data failure');
                        }
                        $maxVal =  $Calculation->getRange($v['maxid']);
                        if($maxVal == false){
                            $this->error('Data failure');
                        }
                        $msg = $v['content'];
                        $msg = strtr($msg, array("{reference}" => $referenceVal));
                        $msg = strtr($msg, array("{max}" => $maxVal));
                        $msg = strtr($msg, array("{min}" => $minVal));
                        $msg = "【物联网】".$msg;
                        
                        //读取触发值
                        $rangeModel = new \app\common\model\Range;
                        $rangeInfo = $rangeModel->get(array('id'=>$v['referenceid']));
                        $runData = array(
                            'val' => $referenceVal,
                            'unitlabel' => $rangeInfo['unitlabel'],
                            'calculation' => $rangeInfo['calculation']
                        );
                        
                        if($minVal!='null' && $maxVal!='null'){
                            if($referenceVal>=$minVal && $referenceVal<$maxVal){
                                $gizwits->sendNotice($v, $msg,$runData);
                            }
                            
                        }elseif ($minVal=='null'){
                            if($maxVal == 0){
                                if($referenceVal<=$maxVal){
                                    $gizwits->sendNotice($v, $msg,$runData);
                                }
                            }else{
                                if($referenceVal<$maxVal){
                                    $gizwits->sendNotice($v, $msg,$runData);
                                }
                            }
                            
                            
                        }elseif ($maxVal=='null'){
                            if($referenceVal>=$minVal){
                                $gizwits->sendNotice($v, $msg,$runData);
                            }
                        }
                    }else{
                        
                        $unit = new \app\common\model\ComponentUnit;
                     $did = $referenceInfo['did'];
                     $res = $examine->checkOnLine($did);
                        $runData = array(
                            'val' => 0,
                            'valueType' => 'offline',
                        );
                        $info = $unit->get(array('did'=>$did));
                        $msg = "【物联网】{$info['title']}已离线，请及时检查";
                        if(!$res){
                            $gizwits->sendNotice($v, $msg,$runData);
                        }elseif ($info['online'] == 0){
                            $gizwits->sendNotice($v, $msg,$runData);
                        }
                    }
               
        $this->success('success');
    }
    
    /**
     * 检测联动
     */
    public function checkLinkage(){
        $id = $this->request->post('id');
        $linkageModel = new \app\common\model\Linkage;
        $v = $linkageModel->get(array('id'=>$id));
        if(empty($v)){
            $this->error('linkage not exist');
        }
        if($v['forbidden'] == 1 || $v['operationWay'] != 'task'){
            $this->error('linkage is invalid');
        }
         //禁止时间
        $thisDate = date("Y-m-d ", time());
        $startban = strtotime($thisDate.$v['startban']);
        $endban = strtotime($thisDate.$v['endban']);
        $thisTime = time();
        if($thisTime>=$startban && $thisTime<=$endban){
            $this->error('linkage is ban');
        }
        $gizwits = new Gizwits;
        $examine = new Examine;
        $time = new ChangeTime();
        $Calculation = new Calculation;
        $rangeModel = new \app\common\model\Range;
 
        //读取触发值
                    $referenceInfo = $rangeModel->get(array('id'=>$v['referenceid']));
                    $rangeInfo = $referenceInfo;
                    $thistime = $time->getMsectime();
                    Db::name('linkage')->where(array('id'=>$v['id']))->update(array('runtime'=>$thistime));
                   
                    if($referenceInfo['rtype'] != 4){
                        
                        $referenceVal =  $Calculation->getRange($v['referenceid']);
                        if($referenceVal == false){
                            $this->error('Data failure');
                        }
                        $minVal =  $Calculation->getRange($v['minid']);
                        if($minVal == false){
                            $this->error('Data failure');
                        }
                        $maxVal =  $Calculation->getRange($v['maxid']);
                        if($maxVal == false){
                            $this->error('Data failure');
                        }
                       // $referenceInfo = $rangeModel->get(array('id'=>$v['referenceid']));
                        $referenceVal =  $Calculation->getRange($v['referenceid']);
                        if($referenceVal == false){
                            $this->error('Data failure');
                        }
                        $runData = array(
                            'val' => $referenceVal,
                            'unitlabel' => $rangeInfo['unitlabel'],
                            'calculation' => $rangeInfo['calculation']
                        );
                        
                        
                        
                        if($minVal!='null' && $maxVal!='null'){
                            
                            if($referenceVal>=$minVal && $referenceVal<$maxVal){
                                
                                $r =  $gizwits->sendTask($v,'task',$runData);
                                if(!$r){
                                    $this->error('请检查该组建单元是否已绑定设备用户');
                                }
                            }
                            
                        }elseif ($minVal=='null'){
                            if($maxVal == 0){
                                if($referenceVal<=$maxVal){
                                    $r =  $gizwits->sendTask($v,'task',$runData);
                                    if(!$r){
                                        $this->error('请检查该组建单元是否已绑定设备用户');
                                    }
                                }
                                
                            }else{
                                if($referenceVal<$maxVal){
                                    $r = $gizwits->sendTask($v,'task',$runData);
                                    if(!$r){
                                        $this->error('请检查该组建单元是否已绑定设备用户');
                                    }
                                }
                            }
                            
                            
                        }elseif ($maxVal=='null'){
                            
                            if($referenceVal>=$minVal){
                                $r =  $gizwits->sendTask($v,'task',$runData);
                                if(!$r){
                                    $this->error('请检查该组建单元是否已绑定设备用户');
                                }
                            }
                        }
                    }else{
                        $runData = array(
                            'val' => 0,
                            'valueType' => 'offline',
                        );
                        $did = $referenceInfo['did'];
                        $res = $examine->checkOnLine($did);
                        if(!$res){
                            $r = $gizwits->sendTask($v,'task',$runData);
                            if(!$r){
                                $this->error('请检查该组建单元是否已绑定设备用户');
                            }
                        }elseif ($info['online'] == 0){
                            $r = $gizwits->sendTask($v,'task',$runData);
                            if(!$r){
                                $this->error('请检查该组建单元是否已绑定设备用户');
                            }
                        }
                    }
               
        
        $this->success('success');
        
    }
    
    
    

    
    /**
     * 检测联动检测任务情况
     */
    public function checkLinkageTask(){
        $id = $this->request->get('id');
        $linkageModel = new \app\common\model\Linkage;
        $info = $linkageModel->get(array('id'=>$id));
        $info['forbidden']  = intval($info['forbidden']);
        if(!empty($info)  && $info['forbidden'] == '0'){
            $this->success('success');
        }else{
            $this->error('fail');
        }
        
        
    }
    
    /**
     * 检测通知任务情况
     */
    public function checkNoticeTask(){
        $id = $this->request->get('id');
        $noticeModel = new \app\common\model\Notice;
        $info = $noticeModel->get(array('id'=>$id));
        $info['forbidden']  = intval($info['forbidden']);
        if(!empty($info)  && $info['forbidden'] == '0'){
            $this->success('success');
        }else{
            $this->error('fail');
        }
        
        
    }
    
    /**
     * 检测组建单元任务情况
     */
    public function checkUnitTask(){
        $id = $this->request->get('id');
        $unitModel = new \app\common\model\ComponentUnit;
        $info = $unitModel->get(array('id'=>$id));
        if(!empty($info)){
            $this->success('success');
        }else{
            $this->error('fail');
        }
        
        
    }
    
    

    

    
   
    
    
    
    
}

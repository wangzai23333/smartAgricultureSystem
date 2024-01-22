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
use app\common\library\Check;
use app\common\controller\Unit;
use app\common\controller\Tdengine;
/**
 * 设备等相关对外接口
 */
class Sensor extends Api
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
        
        $this->member = new \app\common\model\Member;
        $this->sensorList = new \app\common\model\SensorList;
        $this->Group = new \app\common\model\Group;
    }
    /**
     * token获取
     *
     */
    public function login()
    {
        
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        if(empty($username)){
            $this->error('请输入用户名');
        }
        if(empty($password)){
            $this->error('请输入密码');
        }
        $member = $this->member->get(array('username'=>$username));
        if(empty($member)){
            $this->error('用户不存在');
        }
        $password = base64_decode($password);
        $password = md5(md5($password) . $member['salt']);
        
        if($member['password'] != $password){
            $this->error('请检查密码是否有误');
        }
        $time = new ChangeTime();
        $thistime = $time->getMsectime();
        //$token = $this->request->token($username.$thistime);
        if($thistime <= $member['expiretime']-1000){
            $token = $member['token'];
            $expiretime = $member['expiretime'];
        }else{
            $token = $this->request->token($username.$thistime);
            $expiretime = $thistime + (7 * 24 * 60 * 60 * 1000);
        }
        
        
        // $expiretime = $thistime + (7 * 24 * 60 * 60 * 1000);
        
        $result =  $this->member
        ->where(['username' => $username])
        ->update(array(
            'token'=>$token,
            'expiretime'=>$expiretime,
            'prevtime' =>$thistime
        ));
        $data = array(
            'result' => true,
            'code' => 1,
            'msg' => '获取成功',
            'token'=>$token,
            'expiresTime'=>$expiretime
        );
        return json_encode($data);
        
    }

    
    /**
     * 获取最新数据
     *
     */
    public function getLatestList(){
        $token = $this->request->post('token');
        $groupid = $this->request->post('groupid');
        $did = $this->request->post('did');
        $label = $this->request->post('label');
        $sensorTitle = $this->request->post('sensorTitle');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        
        $sensorids = array();
        $wtext = '';
        if(!empty($groupid)){
            $groupModel =  new \app\common\model\Group;
            $groups = explode(",", $groupid);
            if(empty($groups)){
                $this->error('分组编号有误');
            }
            $sensorids = $groupModel->getSensorIdList($groups);
            if(empty($sensorids)){
                $this->error('分组编号有误');
            }
            $sensortext = implode(',',$sensorids);
            $wtext =   " and sensorid IN ({$sensortext}) ";
        }
        if(!empty($did)){
            $unit = $examine->checkUnit($did, $member['id']);
            $dids = explode(",", $did);
        }else{
            $memberUnit = new \app\common\model\MemberUnit;
            $dids = $memberUnit->getMyUnit($member['id']);
            if($dids == false){
                $this->error('无授权组建单元');
            }
        }
        //根据传感器标题检索
        if(!empty($sensorTitle)){
            $titles = explode(",", $sensorTitle);
            $sensorids = array();
            $sensorlist = Db::name('sensor_list')->where('title', 'in', $titles)->order('id','desc') ->select();
            $sensorlist = collection($sensorlist)->toArray();
            foreach ($sensorlist as $v){
                $sensorids[] = $v['id'];
            }
            if(!empty($sensorids)){
                $sensortext = implode(',',$sensorids);
                $wtext .=   " and sensorid IN ({$sensortext}) ";
            }else{
                $this->error('请检查传感器标题是否有误');
            }
        }
        $SensorLog = new \app\common\model\SensorLog;
        $didtext = '';
        foreach ($dids as $v){
            $didtext .= "'".$v."',";
        }
       
        $didtext = rtrim($didtext,',');
        
        if($label != ''){
            $labels = explode(",", $label);
        }else{
            $sql = "SELECT DISTINCT  s.`label` FROM  `iot_unit_sensor` s LEFT JOIN `iot_sensor_list` l ON l.`id` = s.`sensorid` WHERE l.`did` IN ({$didtext}) {$wtext}  ";
            $labelsLog = $SensorLog ->query($sql);
        }
        $where = "where did in ({$didtext}) {$wtext}";
     $tdengine = new Tdengine();
     $list = array();
        if(!empty($labels)){
            foreach ($labels as $v){
                $list[$v] = $tdengine->getNewByLabel($v,$where);
               // $list[$v]  = $r['data'];
            }
            
        }else{
            foreach ($labelsLog as $v){
                $list[$v['label']] = $tdengine->getNewByLabel($v['label'],$where);
            }
        }
        
        $this->success('查询成功',$list);
        
    }
    
    
    /**
     * 获取历史数据
     *
     */
    public function getHistoryList(){
        $token = $this->request->post('token');
        $groupid = $this->request->post('groupid');
        $did = $this->request->post('did');
        $limit = $this->request->post('limit');
        $timeorder = $this->request->post('timeorder');
        $starttime = $this->request->post('starttime');
        $endtime = $this->request->post('endtime');
        $label = $this->request->post('label');
        $referenceValue = $this->request->post('referenceValue');
        $referenceCal = $this->request->post('referenceCal');
        $sensorTitle = $this->request->post('sensorTitle');
        $sensorPort = $this->request->post('sensorPort');
        $wtext = '';
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $sensorids = array();
        if($timeorder!='desc' && $timeorder!='asc' && $timeorder!=''){
            $this->error('排序有误');
        }
       
        if($limit!=''){
            $limits = explode(",", $limit);
            if(!is_numeric($limits[0]) || $limits[0]<0){
                $this->error('条数限制有误');
            }
            if(!is_numeric($limits[1])|| $limits[1]<0){
                $this->error('条数限制有误');
            }
        }
       
        if(!empty($groupid)){
            $groupModel =  new \app\common\model\Group;
            $groups = explode(",", $groupid);
            if(empty($groups)){
                $this->error('分组编号有误');
            }
            $sensorids = $groupModel->getSensorIdList($groups);
            if(empty($sensorids)){
                $this->error('分组编号有误');
            }
            $sensortext = implode(',',$sensorids);
            if(sizeof($sensorids)>1){
                $wtext .=   " and sensorid IN ({$sensortext}) ";
            }else{
                $wtext .=   " and sensorid = {$sensortext} ";
            }
           
            
        }
        
       
        if(!empty($did)){
            $unit = $examine->checkUnit($did, $member['id']);
            $dids = explode(",", $did);
        }else{
            $memberUnit = new \app\common\model\MemberUnit;
            $myunit = $memberUnit
            ->where(array('mid'=>$member['id']))
            ->order('id','desc')
            ->select();
            $myunit = collection($myunit)->toArray();
            $dids = array();
            if(!empty($myunit)){
                foreach ($myunit as $v){
                    $dids[] = $v['did'];
                }
                
            }else{
                $this->error('无授权组建单元');
            }
        }
        
        
        $SensorLog = new \app\common\model\SensorLog;
        $didtext = '';
        foreach ($dids as $v){
            $didtext .= "'".$v."',";
        }
        $didtext = rtrim($didtext,',');
      
         //根据传感器标题检索
        if(!empty($sensorTitle)){
            $titles = explode(",", $sensorTitle);
            $sensorids = array();
            if(!empty($sensorPort)){
                $ports = explode(",", $sensorPort);
                $sensorlist = Db::name('sensor_list')->where('title', 'in', $titles)->where('port', 'in', $ports)->order('id','desc') ->select();
            }else {
                $sensorlist = Db::name('sensor_list')->where('title', 'in', $titles)->order('id','desc') ->select();
                
            }
            $sensorlist = collection($sensorlist)->toArray();
            foreach ($sensorlist as $v){
                $sensorids[] = $v['id'];
            }
            if(!empty($sensorids)){
                $sensortext = implode(',',$sensorids);
                $wtext .=   " and sensorid IN ({$sensortext}) ";
            }else{
                $this->error('请检查传感器标题是否有误');
            }
        }else {
            if(!empty($sensorPort)){
                $this->error('端口检索必填传感器标题');
            }
        }
     
        
        $order = '';
        if($timeorder!=''){
            $order = " ORDER BY ts {$timeorder} ";
        }
        
        $ltext = '';
        if($limit!=''){
            $ltext = " limit {$limit}";
        }
        
        
        if($label != ''){
            $labels = explode(",", $label);
        }else{
            if(empty($sensortext)){
                     $sql = "SELECT DISTINCT  s.`label` FROM  `iot_unit_sensor` s LEFT JOIN `iot_sensor_list` l ON l.`id` = s.`sensorid` WHERE l.`did` IN ({$didtext}) ";
            }else{
                     $sql = "SELECT DISTINCT  s.`label` FROM  `iot_unit_sensor` s LEFT JOIN `iot_sensor_list` l ON l.`id` = s.`sensorid` WHERE l.`did` IN ({$didtext}) and l.id IN ({$sensortext})   ";
            }
           
            $labelsLog = $SensorLog ->query($sql);
            if(empty($labelsLog)){
                $this->error('无对应传感器');
            }
        }
           
        
       
        
        if($referenceCal!=''){
            if($referenceValue != ''){
                switch ($referenceCal){
                    case 'pass':
                        $wtext .= " and val > {$referenceValue} ";
                        break;
                    case 'equal':
                        $wtext .= " and val = {$referenceValue} ";
                        break;
                    case 'less':
                        $wtext .= " and val < {$referenceValue} ";
                        break;
                    default:
                        $this->error('值对比方式有误');
                        break;
                        
                }
                
            }else{
                $this->error('值对比方式不为空时，值不能为空');
            }
        }else{
            if($referenceValue != ''){
            $wtext .= " and val = {$referenceValue} ";
            }
        }
      
        if((intval($starttime)>1 && intval($endtime)>0) || ($starttime != '' && $endtime != '')){
            $wtext.=" and ts between {$starttime} and {$endtime} ";
        }elseif ((intval($starttime)>1) || ($starttime != '' && $endtime == '')){
            $wtext.=" and ts > {$starttime} ";
        }elseif ((intval($endtime)>0 && intval($starttime)!=1) ||  ($starttime == '' && $endtime != '')){
            $wtext.=" and ts < {$endtime} ";
        }
        $wtext .=   " and  did IN ({$didtext}) ";
        $wtext = ltrim($wtext,' and');
       
        $tdengine = new Tdengine();
        $list = array();
        $totals = array();
        if(!empty($labels)){
            foreach ($labels as $v){
                $sql = "select * from model_{$v} where {$wtext}    {$order} {$ltext};";
                $list[$v] = $tdengine->queryForData($sql);
               
                $sql = "select count(*) as total from model_{$v} where {$wtext};";
                 $total = $tdengine->queryForData($sql);
                 $totals[$v] = empty($total) ? 0 : $total[0]['total'];
            }
            
        }else{
            foreach ($labelsLog as $v){
                $sql = "select * from model_{$v['label']} where {$wtext}    {$order} {$ltext};"; 
                $list[$v['label']] = $tdengine->queryForData($sql);
                $sql = "select count(*) as total from model_{$v['label']} where {$wtext};";
                $total = $tdengine->queryForData($sql);
                $totals[$v['label']] = empty($total) ? 0 : $total[0]['total'];
                
            }
        }
        
        
        $result = [
            'result' => true,
            'code' => 1,
            'msg'  => '返回成功',
            'total' => $totals,
            'data' => $list
        ];
        return json($result);
        
    }
    
    public function aaa(){
         $tdengine = new Tdengine();
         $sql = 'SHOW CREATE STABLE model_p;';
        $r = $tdengine->queryForData($sql);
        print_r($r);
    }
    /**
     * 获取历史计算数据
     *
     */
    public function getCalculationList(){
        $token = $this->request->post('token');
        $groupid = $this->request->post('groupid');
        $did = $this->request->post('did');
        $calculate = $this->request->post('calculate');
        $timeunit = $this->request->post('timeunit');
        $timeorder = $this->request->post('timeorder');
        $starttime = $this->request->post('starttime');
        $endtime = $this->request->post('endtime');
        $label = $this->request->post('label');
        $referenceValue = $this->request->post('referenceValue');
        $referenceCal = $this->request->post('referenceCal');
        $sensorTitle = $this->request->post('sensorTitle');
         $sensorPort = $this->request->post('sensorPort');
        $classifyBy = $this->request->post('classifyBy');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $calculateArray = ['max','avg','sum','count','min','stddev'];
        if(!in_array($calculate,$calculateArray)){
           $this->error('计算方式有误');
        }
       
        if($timeorder!='desc' && $timeorder!='asc'){
            $this->error('排序有误');
        }
        if( $starttime == ''){
            $this->error('开始时间不能为空');
        }
        if($endtime == ''){
            $this->error('结束时间不能为空');
        }
       
        
        $wtext = '';
        if(!empty($groupid)){
            $groupModel =  new \app\common\model\Group;
            $groups = explode(",", $groupid);
            if(empty($groups)){
                $this->error('分组编号有误');
            }
            $sensorids = $groupModel->getSensorIdList($groups);
            if(empty($sensorids)){
                $this->error('分组编号有误');
            }
            $sensortext = implode(',',$sensorids);
            $wtext .=   " and sensorid IN ({$sensortext}) ";
        }
     
        
        if(!empty($did)){
            $unit = $examine->checkUnit($did, $member['id']);
            $dids = explode(",", $did);
        }else{
            $memberUnit = new \app\common\model\MemberUnit;
            $myunit = $memberUnit
            ->where(array('mid'=>$member['id']))
            ->order('id','desc')
            ->select();
            $myunit = collection($myunit)->toArray();
            
            $dids = array();
            if(!empty($myunit)){
                foreach ($myunit as $v){
                    $dids[] = $v['did'];
                }
                
            }else{
                $this->error('无授权组建单元');
            }
        }
        
       //根据传感器标题检索
        if(!empty($sensorTitle)){
            $titles = explode(",", $sensorTitle);
            $sensorids = array();
            if(!empty($sensorPort)){
                $ports = explode(",", $sensorPort);
                $sensorlist = Db::name('sensor_list')->where('title', 'in', $titles)->where('port', 'in', $ports)->order('id','desc') ->select();
            }else {
                $sensorlist = Db::name('sensor_list')->where('title', 'in', $titles)->order('id','desc') ->select();
                
            }
            $sensorlist = collection($sensorlist)->toArray();
            foreach ($sensorlist as $v){
                $sensorids[] = $v['id'];
            }
            if(!empty($sensorids)){
                $sensortext = implode(',',$sensorids);
                $wtext .=   " and sensorid IN ({$sensortext}) ";
            }else{
                $this->error('请检查传感器标题是否有误');
            }
        }else {
            if(!empty($sensorPort)){
                $this->error('端口检索必填传感器标题');
            }
        }
        
        
   
        
        $SensorLog = new \app\common\model\SensorLog;
        $didtext = '';
        foreach ($dids as $v){
            $didtext .= "'".$v."',";
        }
        $didtext = rtrim($didtext,',');
        
       
        if($label != ''){
            $labels = explode(",", $label);
        }else{
            if(!empty($sensortext)){
                $sql = "SELECT DISTINCT  s.`label` FROM  `iot_unit_sensor` s LEFT JOIN `iot_sensor_list` l ON l.`id` = s.`sensorid` WHERE l.`did` IN ({$didtext}) and l.id IN ({$sensortext})   ";
                
            }else{
                $sql = "SELECT DISTINCT  s.`label` FROM  `iot_unit_sensor` s LEFT JOIN `iot_sensor_list` l ON l.`id` = s.`sensorid` WHERE l.`did` IN ({$didtext})  ";
                
            }
           
            $labelsLog = $SensorLog ->query($sql);
            if(empty($labelsLog)){
                $this->error('无对应传感器');
            }
        }
       
        
        if($referenceCal!=''){
            if($referenceValue != ''){
                switch ($referenceCal){
                    case 'pass':
                        $wtext .= " and val > {$referenceValue} ";
                        break;
                    case 'equal':
                        $wtext .= " and val = {$referenceValue} ";
                        break;
                    case 'less':
                        $wtext .= " and val < {$referenceValue} ";
                        break;
                    default:
                        $this->error('值对比方式有误');
                        break;
                        
                }
                
            }else{
                $this->error('值对比方式不为空时，值不能为空');
            }
        }else{
            if($referenceValue != ''){
                $wtext .= " and val = {$referenceValue} ";
            }
        }
        
        if((intval($starttime)>1 && intval($endtime)>0) || ($starttime != '' && $endtime != '')){
            $wtext.=" and ts between {$starttime} and {$endtime} ";
        }elseif ((intval($starttime)>1) || ($starttime != '' && $endtime == '')){
            $wtext.=" and ts > {$starttime} ";
        }elseif ((intval($endtime)>0 && intval($starttime)!=1) ||  ($starttime == '' && $endtime != '')){
            $wtext.=" and ts < {$endtime} ";
        }
        $Calculation = new Calculation; 
        $list = array();
        if(!empty($labels)){
            foreach ($labels as $v){
                $list[$v] = $Calculation->getCalculationlist($calculate, $dids, $wtext, $timeunit, $timeorder,$v);
            }
            
        }else{
            foreach ($labelsLog as $v){
                $list[$v['label']] = $Calculation->getCalculationlist($calculate, $dids, $wtext, $timeunit, $timeorder,$v['label']);
                
            }
        }
        
        $this->success('查询成功',$list);
        
    }
    
    
    /**
     *获取对应分组的经纬度
     *
     */
    public function getGroupAddress(){
        $token = $this->request->post('token');
        $groupid = $this->request->post('groupid');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        if(!empty($groupid)){
            $groups = explode(",", $groupid);
            $list = $this->Group->where('groupid', 'in', $groups)->order('id','desc') ->select();
            if(empty($list)){
                $this->error('组别错误');
            }
        }else{
            $list = $this->Group->order('id','desc') ->select();
        }
        foreach ($list as $row) {
            $row->visible(['id','groupid','title','lat','lng']);
        }
        $list = collection($list)->toArray();
        $this->success('获取成功',$list);
    }
    /**
     *获取所绑定的设备信息
     *
     */
    public function getBuildingUnit(){
        $token = $this->request->post('token');
        $did = $this->request->post('did');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        
        $memberUnit = new \app\common\model\MemberUnit;
        if(!empty($did)){
            $dids = explode(",", $did);
            
            $list = $memberUnit
            ->with(['componentunit'])
            ->where(array(
                'mid' => $member['id']
            ))
            ->where('componentunit.did', 'in', $dids)
            ->order('id','desc')
            ->select();
            if(empty($list)){
                $this->error('组建单元错误');
            }
        }else{
            
            $list = $memberUnit
            ->with(['componentunit'])
            ->where(array(
                'mid' => $member['id']
            ))
            ->order('id','desc')
            ->select();
        }
        
        foreach ($list as $row) {
            $row->visible(['componentunit']);
            $row->getRelation('componentunit')->visible(['did','lat','lng','online']);
        }
        $list = collection($list)->toArray();
     
        $data = array();
        foreach ($list as $i=>$v){
            $data[$i]['did'] = $v['componentunit']['did'];
            $data[$i]['lat'] = $v['componentunit']['lat'];
            $data[$i]['lng'] = $v['componentunit']['lng'];
            $sql = "SELECT l.id,l.title,s.label FROM `iot_unit_sensor` s LEFT JOIN `iot_sensor_list` l ON l.`id` = s.`sensorid` WHERE did = '{$v['componentunit']['did']}'";
            $data[$i]['sensor'] = $this->Group->query($sql);
            $online = $v['componentunit']['online'];
          
            if($online == 1){
                $r =  $examine->checkOnLine($v['componentunit']['did']);
              
               if($r){
                   $isonline = 1;
               }else{
                   $isonline = 0;
               }
            }else{
                $isonline = 0;
            }
            $data[$i]['isonline'] = $isonline;
            
        }
        $redata = array(
            'result' => true,
            'deviceArr' => $data,
            'code' =>1,
            'msg' => '查询成功'
        );
        return json_encode($redata);
        
    }
    

    /**
     * 开关控制
     *
     */
    public function switchOperation(){
        $token = $this->request->post('token');
        $did = $this->request->post('did');
        $num = $this->request->post('num');
        $onoff = $this->request->post('onoff');
        $taskTime = $this->request->post('taskTime');
        $way = $this->request->post('way');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        if($onoff!='on' && $onoff!='off'){
            $this->error('状态有误');
        }
        if(!empty($way) && ($way != 'app' && $way != 'client')){
            $this->error('操作方式有误');
        }
        if(empty($way)){
            $way = 'app';
        }
        $sensor = $examine->checkUnit($did, $member['id']);
        if(intval($num) <=0 || intval($num)>16){
            $this->error('编号有误');
        };
        $time = new ChangeTime();
        $thistime = $time->getMsectime();
        $gizwits = new Gizwits;
        $unit = new Unit();
        $status = $onoff == 'on' ? 1 : 0;
        if($taskTime != 0){
            if($taskTime<=$thistime){
                $this->error('计划时间有误');
            }
           
        }else{
            $taskTime = $time->getMsectime();
        }
        $r = $unit->onoff($did, $num, $status,$taskTime,$way,0);
        if($r){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
    
    
    /**
     * 获取开关状态
     *
     */
    public function getSwitchInfo(){
        $token = $this->request->post('token');
        $did = $this->request->post('did');
        $num = $this->request->post('number');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        if(!empty($did)){
            $sensor = $examine->checkUnit($did, $member['id']);
            $dids = explode(",", $did);
        }else{
            $memberUnit = new \app\common\model\MemberUnit;
            $myunit = $memberUnit
            ->where(array('mid'=>$member['id']))
            ->order('id','desc')
            ->select();
            $myunit = collection($myunit)->toArray();
            
            $dids = array();
            if(!empty($myunit)){
                foreach ($myunit as $v){
                    $dids[] = $v['did'];
                }
                
            }else{
                $this->error('无授权组建单元');
            }
        }
        $statusModel = new \app\common\model\SensorStatus;
        if($num!=''){
            $nums = explode(",", $num);
            $list = $statusModel
            ->where('did', 'in', $dids)
            ->select();
            $onoff= array();
            foreach ($nums as $n){
                $onoff[] = 'onoff'.$n;
            }
            $onoff[sizeof($onoff)] = 'did';
            $onoff[sizeof($onoff)] = 'updatetime';
            foreach ($list as $row) {
                $row->visible(['updatetime','did','onoff1']);
            }
            
        }else{
            $list = $statusModel
            ->where('did', 'in', $dids)
            ->select();
        }
        $list = collection($list)->toArray();
        $data = array();
        if(!empty($list)){
            foreach ($list as $i => $v){
                $data[$i]['did'] = $v['did'];
            }
            $this->success('查询成功',$list);
        }else{
            $this->error('无对应开关数据');
        }
    }
    /**
     * 获取开关操作记录
     *
     */
    public function getSwitchLog(){
        $token = $this->request->post('token');
        $did = $this->request->post('did');
        $num = $this->request->post('number');
        $startTime = $this->request->post('startTime');
        $endTime = $this->request->post('endTime');
        $limit = $this->request->post('limit');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $memberUnit = new \app\common\model\MemberUnit;
        if(!empty($did)){
            $sensor = $examine->checkUnit($did, $member['id']);
            $dids = explode(",", $did);
        }else{
            $myunit = $memberUnit
            ->where(array('mid'=>$member['id']))
            ->order('id','desc')
            ->select();
            $myunit = collection($myunit)->toArray();
            
            $dids = array();
            if(!empty($myunit)){
                foreach ($myunit as $v){
                    $dids[] = $v['did'];
                }
                
            }else{
                $this->error('无授权组建单元');
            }
        }
        $where = '';
        if(!empty($startTime)){
            $where .=' and createtime >='.$startTime.' '; 
        }
        if(!empty($endTime)){
            $where .=' and createtime <='.$endTime.' ';
        }
        
        if(!empty($num)){
            $nums = explode(",", $num);
            $numText = '';
            foreach ($nums as $v){
                $numText .= "'".$v."',";
            }
            $numText = rtrim($numText,',');
            $where .=' and number in('.$numText.' ) ';
        }
        
        $didtext = '';
        foreach ($dids as $v){
            $didtext .= "'".$v."',";
        }
        $didtext = rtrim($didtext,',');
        
        $ltext = '';
        if($limit!=''){
            $limits = explode(",", $limit);
           
            if(!is_numeric($limits[0]) || $limits[0]<0){
                $this->error('条数限制有误');
            }
            if(!is_numeric($limits[1])|| $limits[1]<0){
                $this->error('条数限制有误');
            }
            $ltext = " limit {$limit}";
           
        }
        
        $sql = "SELECT did,number,IF(status='on', 1, 0) AS status,createtime AS createTime,FROM_UNIXTIME(createtime/1000,'%Y-%m-%d %H:%i:%S') timetText  FROM `iot_status_log` WHERE did IN ({$didtext}) {$where}  order by createtime desc {$ltext}";
     
        $list = $memberUnit->query($sql);
        $sql = "SELECT COUNT(id) as total FROM `iot_status_log` WHERE did IN ({$didtext}) {$where}";
        $total = $memberUnit->query($sql);

        
        $result = [
            'result' => true,
            'code' => 1,
            'msg'  => '返回成功',
            'total' => $total[0]['total'],
            'data' => $list
        ];
        return json($result);
       // $this->success('返回成功',$list);
    }
    
    /**
     * 检查任务
     *
     */
    public function checkTask(){
        $time = new ChangeTime();
        $thistime = $time->getMsectime();
        $sql = "SELECT  /*+ QB_NAME(QB1) NO_ICP(`iot_task`@QB1 `primary`, `did`) */ * FROM `iot_task` USE INDEX FOR ORDER BY(`did`) WHERE executetime <= {$thistime} AND isInvalid = '0' AND operationWay = 'polling' AND status = '0' ORDER BY executetime ASC";
        $taskList = $this->member->query($sql);
        $emqx = new Emqx();
        $gizwits = new Gizwits;
        if(!empty($taskList)){
            foreach ($taskList as $v){
                    
                
                
                    if(($v['did'] == 'hJVxMG4fS6Za4LPyvR01m4' && ($v['switchnum'] == 1 || $v['switchnum'] == 2 || $v['switchnum'] == 4)) || ($v['did'] == '1DIrTA5xH6CjVQ7jbNJSMw' &&  $v['switchnum'] == 3)){
                        $dehModel = new \app\common\model\Dehumidifiers;
                        $info = $dehModel->get(array('did'=>$v['did'],'num'=>$v['switchnum']));
                        //向设备发送信息
                        $getTopic = 'YHM/Rs485ToEth/ServerCmd/Ack/'.$info['dev_uid'];
                        $sendTopic = 'YHM/Rs485ToEth/ServerCmd/'.$info['dev_uid'];
                        $sendData =array();
                        $sendData['head'] = array(
                            'Symbol'=>'YHM',
                            'MsgId' =>$emqx->getRandromStr(),
                            'CmdStr' =>'SetMachinePara'
                        );
                        $onoffSwitch = $v['onoff'] =='on'?'On':'Off';
                        $sendData['body'] = array(
                            'DevUID'=>$info['dev_uid'],
                            'DehumiDevs' => array(0=>array(
                                'Id' => $info['hid'],
                                'SetOnoff' =>$onoffSwitch
                            ))
                        );
                        $sendMeg = json_encode($sendData);
                        $r = $emqx->subscribeForBack($getTopic, $sendTopic, $sendMeg);
                        $res = json_decode($r,true);
                        $info = $dehModel->get(array('did'=>$v['did'],'num'=>$v['switchnum']));
                        if($v['onoff'] != $info['status']){
                            Db::name('status_log')->insert(array(
                                'did' => $v['did'],
                                'number' => $v['switchnum'],
                                'status' => $v['onoff'],
                                'way' => 'linkage',
                                'taskId' => $v['id'],
                                'createtime' => $time->getMsectime()
                            ));
                        }
                       // if($res['body']['ResultCode'] == 0 || !empty($res['body']['DevUID'])){
                       
                             $dehModel->update(array('status'=>$v['onoff'],'updatetime'=>$time->getMsectime()),array('id'=>$info['id']));
                            
                             //Db::commit();
//                         }else{
//                             Db::rollback();
//                         }
                        
                        
                    }else if($v['did'] == '1DIrTA5xH6CjVQ7jbNJSMw' && ($v['switchnum'] == 1 || $v['switchnum'] == 2)){
                        $airModel = new \app\common\model\Air;
                        $info = $airModel->get(array('did'=>$v['did'],'num'=>$v['switchnum']));
                        $getTopic = 'YHM/Rs485ToEth/ServerCmd/Ack/'.$info['dev_uid'];
                        $sendTopic = 'YHM/Rs485ToEth/ServerCmd/'.$info['dev_uid'];
                        $sendData =array();
                        $sendData['head'] = array(
                            'Symbol'=>'YHM',
                            'MsgId' =>$emqx->getRandromStr(),
                            'CmdStr' =>'SetMachinePara'
                        );
                        $onoffSwitch = $v['onoff'] =='on'?'On':'Off';
                        $sendData['body'] = array(
                            'DevUID'=>$info['dev_uid'],
                            'CentralAirc' => array(0=>array(
                                'Id' => $info['hid'],
                                'SetOnoff' =>$onoffSwitch
                            ))
                        );
                       
                        $sendMeg = json_encode($sendData);
                        $r = $emqx->subscribeForBack($getTopic, $sendTopic, $sendMeg);
                        $res = json_decode($r,true);
                        $info = $airModel->get(array('did'=>$v['did'],'num'=>$v['switchnum']));
                     
                        if($v['onoff'] != $info['status']){
                            Db::name('status_log')->insert(array(
                                'did' => $v['did'],
                                'number' => $v['switchnum'],
                                'status' => $v['onoff'],
                                'way' => 'linkage',
                                'taskId' => $v['id'],
                                'createtime' => $time->getMsectime()
                            ));
                        }
                        $airModel->update(array('status'=>$v['onoff'],'updatetime'=>$time->getMsectime()),array('id'=>$info['id']));
                       // Db::commit();
                        
                    }else{
                    $status = $v['onoff']=='on'?1:0;
                    $statusInfo = Db::name('sensor_status')->where(
                        array('did'=>$v['did'],'number'=>$v['switchnum'])
                        )->find();
                        
                        if($statusInfo['status'] != $status){
                            Db::startTrans();
                            try {
                                $re = $gizwits->onoff($v['did'],intval($v['switchnum']),$v['onoff'],'linkage',$v['id']);
                                             
                    if(!empty($re)){
                        Db::rollback();
                    }else{
                      
                        Db::name('sensor_status')->where(array('did'=>$v['did'],'number'=>$v['switchnum']))->update(
                            array('status'=>$status,'updatetime'=>$time->getMsectime()));
                        Db::commit();
                    }
                            } catch (ValidateException $e) {
                                Db::rollback();
                            } catch (PDOException $e) {
                                Db::rollback();
                            } catch (Exception $e) {
                                Db::rollback();
                            }
                            
                            
                        
                        }
                    }
              
                $data = array(
                    'ntype' => 1,
                    'tid' => $v['id'],
                    'createtime' =>$time->getMsectime()
                );
//                 try {
                $r =  Db::name('task')->where(array('id'=>$v['id']))->update(
                    array('status'=>1));
//                 Db::commit();
                
//             } catch (ValidateException $e) {
//                 Db::rollback();
//             } catch (PDOException $e) {
//                 Db::rollback();
//             } catch (Exception $e) {
//                 Db::rollback();
//             }
                Db::name('record')->insert($data);
                
            }
            
        }
        $this->success('操作成功');
        
    }
    public function getGizwitsToken(){
        $gizwits = new Gizwits;
        $data = $gizwits->getToken();
        $this->success('获取成功',$data);
    }
    
     /****
     * 
     * 获取人员记录
     * ***/
    public function getPersonLog(){
        $token = $this->request->post('token');
        $groupid = $this->request->post('groupid');
        $did = $this->request->post('did');
        $limit = $this->request->post('limit');
        $timeorder = $this->request->post('timeorder');
        $starttime = $this->request->post('starttime');
        $endtime = $this->request->post('endtime');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        
        $sensorids = array();
        $wtext = '';
        
        if($timeorder!='desc' && $timeorder!='asc' && $timeorder!=''){
            $this->error('排序有误');
        }
        if(intval($starttime)>1 && intval($endtime)>0){
            $wtext.=" and createtime between {$starttime} and {$endtime} ";
        }elseif (intval($starttime)>1){
            $wtext.=" and createtime > {$starttime} ";
        }elseif (intval($endtime)>0 && intval($starttime)!=1){
            $wtext.=" and createtime < {$endtime} ";
        }
        
        if($limit!=''){
            $limits = explode(",", $limit);
            if(sizeof($limits) <2){
                $this->error('条数限制有误');
            }
            if(!is_numeric($limits[0]) || $limits[0]<0){
                $this->error('条数限制有误');
            }
            if(!is_numeric($limits[1])|| $limits[1]<0){
                $this->error('条数限制有误');
            }
        }
        
        if(!empty($groupid)){
            $groupModel =  new \app\common\model\Group;
            $groups = explode(",", $groupid);
            if(empty($groups)){
                $this->error('分组编号有误');
            }
            $sensorids = $groupModel->getSensorIdList($groups);
            if(empty($sensorids)){
                $this->error('分组编号有误');
            }
            $sensortext = implode(',',$sensorids);
            $wtext =   " and sensorid IN ({$sensortext}) ";
        }
        if(!empty($did)){
            $unit = $examine->checkUnit($did, $member['id']);
            $dids = explode(",", $did);
        }else{
            $memberUnit = new \app\common\model\MemberUnit;
            $dids = $memberUnit->getMyUnit($member['id']);
            if($dids == false){
                $this->error('无授权组建单元');
            }
        }
        $SensorLog = new \app\common\model\SensorLog;
        $didtext = '';
        $idtext = '';
        foreach ($dids as $v){
            $didtext .= "'".$v."',";
        }
        $didtext = rtrim($didtext,',');
        $wtext =   " and did IN ({$didtext}) ";
        
        $order = '';
        if ($timeorder!=''){
            $order = " ORDER BY createtime {$timeorder} ";
        }else {
            $order = " ORDER BY createtime desc ";
        }
        $ltext = '';
        if($limit!=''){
            $ltext = " limit {$limit}";
        }
        
     
        $sql = " SELECT did,sensorid AS sensorId,starttime AS startTime,endtime AS endTime FROM `iot_peoson_log` WHERE 1  {$wtext} {$order} {$ltext} ";
        $list =  $SensorLog->query($sql);
       
        foreach ($list as $k=>$v){
            $list[$k]['startTimeText'] = date("Y-m-d H:i:s", $v['startTime']/1000);
            $list[$k]['endTimeText'] = date("Y-m-d H:i:s", $v['endTime']/1000);
        }
        $sql = " SELECT count(id) as total FROM `iot_peoson_log` WHERE 1  {$wtext} ";
        $info =  $SensorLog->query($sql);
        if(!empty($info[0]['total'])){
            $total = $info[0]['total'];
        }else{
            $total = 0;
        }
        $result = [
            'result' => true,
            'code' => 1,
            'msg'  => '返回成功',
            'total' => $total,
            'data' => $list
        ];
        return json($result);
        
    }
    
    /****
     * 
     * 初始化任务获取(还没到达时间)
     * 
     * ***/
    public function getFutureTask(){
        $token = $this->request->post('token');
        $examine = new Examine;
        $member = $examine->checkMember($token,'unit');
        $time = new ChangeTime();
        $thistime = $time->getMsectime();
        $SensorLog = new \app\common\model\SensorLog;
        $sql = "SELECT t.`onoff`,t.`number`,t.`runTime`,t.`did`,t.`id` FROM `iot_run_task_member` m LEFT JOIN `iot_run_task` t ON m.`taskid` = t.`id` WHERE m.`userid` = {$member['id']} AND t.`cancel` = '0' AND t.`runTime` >= {$thistime}";
        $list = $SensorLog->query($sql);
        $this->success('获取成功',$list);
    }
   
    /*****
     * 
     * 获取联动任务
     * 
     * ***/
    public function getTask(){
        $token = $this->request->post('token');
        $did = $this->request->post('did');
        $num = $this->request->post('number');
        $startTime = $this->request->post('startTime');
        $endTime = $this->request->post('endTime');
        $limit = $this->request->post('limit');
        $cancel = $this->request->post('cancel');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $memberUnit = new \app\common\model\MemberUnit;
        if(!empty($did)){
            $sensor = $examine->checkUnit($did, $member['id']);
            $dids = explode(",", $did);
        }else{
            $myunit = $memberUnit
            ->where(array('mid'=>$member['id']))
            ->order('id','desc')
            ->select();
            $myunit = collection($myunit)->toArray();
            
            $dids = array();
            if(!empty($myunit)){
                foreach ($myunit as $v){
                    $dids[] = $v['did'];
                }
                
            }else{
                $this->error('无授权组建单元');
            }
        }
        $where = '';
        if(!empty($startTime)){
            $where .=' and runTime >='.$startTime.' ';
        }
        if(!empty($endTime)){
            $where .=' and runTime <='.$endTime.' ';
        }
        
        if(!empty($num)){
            $nums = explode(",", $num);
            $numText = '';
            foreach ($nums as $v){
                $numText .= "'".$v."',";
            }
            $numText = rtrim($numText,',');
            $where .=' and number in('.$numText.' ) ';
        }
        
        $didtext = '';
        foreach ($dids as $v){
            $didtext .= "'".$v."',";
        }
        $didtext = rtrim($didtext,',');
        
        $ltext = '';
        if($limit!=''){
            $limits = explode(",", $limit);
            
            if(!is_numeric($limits[0]) || $limits[0]<0){
                $this->error('条数限制有误');
            }
            if(!is_numeric($limits[1])|| $limits[1]<0){
                $this->error('条数限制有误');
            }
            $ltext = " limit {$limit}";
            
        }
        if($cancel!=''){
            $where .=" and cancel ='{$cancel}'";
        }
        $sql = "SELECT did,number,onoff,runTime,FROM_UNIXTIME(runTime/1000,'%Y-%m-%d %H:%i:%S') timetText,cancel  FROM `iot_run_task` WHERE did IN ({$didtext}) {$where}  order by runTime desc {$ltext}";
        
        $list = $memberUnit->query($sql);
        $sql = "SELECT COUNT(id) as total FROM `iot_run_task` WHERE did IN ({$didtext}) {$where}";
        $total = $memberUnit->query($sql);
        
        
        $result = [
            'result' => true,
            'code' => 1,
            'msg'  => '返回成功',
            'total' => $total[0]['total'],
            'data' => $list
        ];
        return json($result);
    }
    
    
}

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
/**
 * 通知相关对外接口
 */
class Notice extends Api
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
        
        $this->callByVoice('13286974817', 'TTS_216844384', 100, 3,array('alarmname'=>'功率大于3300w'));
        
    }
    /**
     * 获取未读最新通知
     */
    public function getLatestNotice(){
        $token = $this->request->post('token');
        $terminal = $this->request->post('terminal');
        $did = $this->request->post('did');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $noticeModel = new \app\common\model\Notice;
        $time = new ChangeTime();
        $thistime = $time->getMsectime();
      
        //免打扰设定
        $banApp = false;
        $banClient = false;
            $disturbModel = new \app\common\model\NoticeNotDisturb;
            $disturbInfo = $disturbModel->get(array('did'=>$did,'disturbtype'=>'unit'));
            if(!empty($disturbInfo)){
            if($disturbInfo['everyday'] == 1){
                $startTime = mktime(0,$disturbInfo['startminute'],$disturbInfo['starthour'],date('m'),date('d'),date('Y'));
                $endTime = mktime(0,$disturbInfo['endminute'],$disturbInfo['endhour'],date('m'),date('d'),date('Y'));
            }else{
                $startTime = $disturbInfo['starttime']/1000;
                $endTime = $disturbInfo['endtime']/1000;
            }
            $today = time();
            if($startTime>=$today && $endTime<=$today){
                $banApp = $disturbInfo['banapp'] == 1 ? true : false;
                $banClient = $disturbInfo['banclient'] == 1 ? true : false;
            }
            }
          
            
            
            
            
            if($banApp && $terminal == 'app'){
                $this->error('当前为免打扰状态');
            }
            if($banClient && $terminal == 'client'){
                $this->error('当前为免打扰状态');
            }
        
        $where = '';
        if(!empty($terminal)){
            if($terminal == 'app'){
                $where.=" AND n.isApp != '0' ";
            }elseif ($terminal == 'client'){
                $where.=" AND n.isClient != '0' ";
            }else {
                $this->error('终端信息有误');
            }
            
        }
        if(!empty($did)){
            $sensor = $examine->checkUnit($did, $member['id']);
            $where.=" AND r.did = '{$did}' ";
            $sql = "SELECT l.*,n.sendtime as noticeSend,n.delaytime,n.id as nid FROM `iot_record_log` l LEFT JOIN `iot_notice` n  ON n.id = l.`nid`  LEFT JOIN `iot_range` r ON r.id = n.`referenceid`   WHERE l.mid  = {$member['id']} AND l.isread = '0' AND l.isexpire = '0' and l.sendtime<={$thistime}  {$where}  ORDER BY l.id ASC LIMIT 1";
        }else{
            $sql = "SELECT l.*,n.sendtime as noticeSend,n.delaytime,n.id as nid FROM `iot_record_log` l LEFT JOIN `iot_notice` n  ON n.id = l.`nid`  WHERE l.mid = {$member['id']} AND l.isread = '0' and l.isexpire = '0' and l.sendtime<={$thistime} {$where}   ORDER BY l.id ASC LIMIT 1";
        }
        $info = $noticeModel->query($sql);
        
        if(empty($info)){
            $this->error('暂无通知');
        }
        
        $disturbInfo = array();
        $disturbInfo = $disturbModel->get(array('nid'=>$info[0]['nid'],'disturbtype'=>'notice'));
        if(!empty($disturbInfo)){
            if($disturbInfo['everyday'] == 1){
                $startTime = mktime(0,$disturbInfo['startminute'],$disturbInfo['starthour'],date('m'),date('d'),date('Y'));
                $endTime = mktime(0,$disturbInfo['endminute'],$disturbInfo['endhour'],date('m'),date('d'),date('Y'));
            }else{
                $startTime = $disturbInfo['starttime']/1000;
                $endTime = $disturbInfo['endtime']/1000;
            }
            $today = time();
            if($startTime>=$today && $endTime<=$today){
                $banApp = $disturbInfo['banapp'] == 1 ? true : $banApp;
                $banClient = $disturbInfo['banclient'] == 1 ? true : $banClient;
            }
        }
        
        if($banApp && $terminal == 'app'){
            Db::name('record_log')
            ->where(['id'=>$info[0]['id'],'mid'=>$member['id']])
            ->update([
                'isexpire' => 1
            ]);
            $this->error('当前为免打扰状态');
        }
        if($banClient && $terminal == 'client'){
            Db::name('record_log')
            ->where(['id'=>$info[0]['id'],'mid'=>$member['id']])
            ->update([
                'isexpire' => 1
            ]);
            $this->error('当前为免打扰状态');
        }
        $thistime = $time->getMsectime();
//         $delaytime = $info[0]['noticeSend'] + $info[0]['delaytime'] * 60 * 1000;
//         if($thistime<$delaytime){
//             $this->error('当前为免打扰状态');
//         }
        
        
        
        
        Db::startTrans();// 启动事务
        try
        {
            
          
                Db::name('record_log')
                ->where(['id'=>$info[0]['id'],'mid'=>$member['id']])
                ->update([
                    'issend' => 1,
                    'lasttime' => $thistime,
                    'sendtotal' => 1,
                    'isread' => 1
                ]);
            
            // else{
            //     if($info[0]['issend'] == 0){
            //         Db::name('record_log')
            //         ->where(['id'=>$info[0]['id'],'mid'=>$member['id']])
            //         ->update([
            //             'issend' => 1,
            //             'lasttime' => $thistime,
            //             'sendtotal' => 1
            //         ]);
            //     }else{
            //         Db::name('record_log')
            //         ->where(['id'=>$info[0]['id'],'mid'=>$member['id']])
            //         ->update([
            //             'lasttime' => $thistime,
            //             'sendtotal' => $info[0]['sendtotal'] + 1
            //         ]);
            //     }
            // }
         
         
          
            Db::commit();
         
        }
        catch (\Exception $e)
        {
            Db::rollback();
            $this->error('获取失败，请重试');
        }
        $notice = $noticeModel->get(array('id'=>$info[0]['nid']));
        
        $data = array(
            'noticeSign'  => $notice['noticesign'],
            'recordid' => $info[0]['recordid'],
            'nid' => $info[0]['nid']
        );
        $sinfo = $noticeModel->query("SELECT valueType,unitlabel,val,calculation FROM `iot_notice_value` WHERE tid = {$info[0]['id']} ");
        if(!empty($sinfo)){
            $data['valueType'] = $sinfo[0]['valueType'];
            $data['unitlabel'] = $sinfo[0]['unitlabel'];
            $data['val'] = $sinfo[0]['val'];
            $data['calculation'] = $sinfo[0]['calculation'];
        }else{
            $data['valueType'] = '';
            $data['unitlabel'] = '';
            $data['val'] = '';
            $data['calculation'] = '';
        }
        $this->success('获取成功',$data);
    }
    /**
     * 阅读通知，更新状态
     */
    public function toReadNotice(){
        $token = $this->request->post('token');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $recordId = $this->request->post('recordId');
        $RecordLog = new \app\common\model\RecordLog();
        if(empty($recordId)){
            $this->error('通知编号不能为空');
        }else{
            $record = $RecordLog->get(array('recordid'=>$recordId,'mid'=>$member['id']));
            if(empty($record) || $record['issend'] == 0){
                $this->error('请检查传输信息是否有误');
            }
            if($record['isread'] == 1){
                $this->error('该通知已阅读');
            }
         $time = new ChangeTime();
         $thistime = $time->getMsectime();
            Db::startTrans();// 启动事务
            try
            {
                Db::name('record_log')
                ->where(['id'=>$record['id'],'mid'=>$member['id']])
                ->update([
                    'isread' => 1,
                    'readtime' => $thistime
                ]);
               Db::commit();
                
            }
            catch (\Exception $e)
            {
                Db::rollback();
                $this->error('操作失败，请重试');
            }
            $this->success('操作成功');
        }
    }
    /**
     * 获取历史通知
     */
    public function getHistoryList(){  
        $token = $this->request->post('token');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $isRead = $this->request->post('isRead');
        $isSend = $this->request->post('isSend');
        $limit = $this->request->post('limit');
        $startTime = $this->request->post('startTime');
        $endTime = $this->request->post('endTime');
        $terminal = $this->request->post('terminal');
        $did = $this->request->post('did');
        if(empty($startTime) || empty($endTime)){
            $this->error('开始时间和结束时间不能为空');
        }
        
        $time = new ChangeTime();
        $thistime = $time->getMsectime();
        $ltext = '';
        $min = 0;
        if($limit!=''){
            $limits = explode(",", $limit);
            if(!is_numeric($limits[0]) || $limits[0]<0){
                $this->error('条数限制有误');
            }
            if(!is_numeric($limits[1]) || $limits[1]<0){
                $this->error('条数限制有误');
            }
            $min = $limits[0];
            $ltext = " limit {$limit}";
        }
        $where = '';
        if((!empty($isRead) && $isRead!='y' && $isRead!='n')){
            $this->error('请检查已读字段是否有误');
        }elseif ($isRead == 'y' || $isRead == 'n'){
            $readStatus = $isRead =='y' ? '1' : '0';
            $where .= " and l.isread = '{$readStatus}' ";
        }
        if((!empty($isSend) && $isSend!='y' && $isSend!='n')){
            $this->error('请检查已发送字段是否有误');
        }elseif ($isSend == 'y' || $isSend == 'n'){
            $sendStatus = $isSend =='y' ? '1' : '0';
            $where .= " and l.issend = '{$sendStatus}' ";
        }
        $nwhere = '';
        if(!empty($terminal)){
            if($terminal == 'app'){
                $nwhere.=" AND isApp != '0' ";
            }elseif ($terminal == 'client'){
                $nwhere.=" AND isClient != '0' ";
            }else {
                $this->error('终端信息有误');
            }
            
        }
        
        $RecordLog = new \app\common\model\RecordLog();

        if(!empty($did)){
            $sensor = $examine->checkUnit($did, $member['id']);
            $sql = "SELECT id FROM `iot_range` WHERE did = '{$did}'";
            $idList = $RecordLog->query($sql);
            $idText = '';
            if(!empty($idList)){
            foreach ($idList as $v){
                $idText .= "'".$v['id']."',";
            }
            $idText = rtrim($idText,',');
            $sql = "SELECT id FROM `iot_notice`  WHERE referenceid in({$idText}) $nwhere";
            $nidList = $RecordLog->query($sql);
            $nidText = '';
            if(!empty($nidList)){
                foreach ($nidList as $v){
                    $nidText .= "'".$v['id']."',";
                }
             $nidText = rtrim($nidText,',');
            
            $where.=" and l.nid in($nidText) ";
            }else{
                 $where.=" and l.nid =0 ";
            }
            }
           
            
            $sql = "SELECT l.id,l.recordid AS recordId,l.isread as isRead,l.issend as isSend,l.isexpire as isExpire,FROM_UNIXTIME(sendtime/1000,'%Y-%m-%d %H:%i:%S') timetText ,l.nid   FROM `iot_record_log` l  USE INDEX(`primary`)  ";
            $sql .="where l.mid = {$member['id']}  and  l.createtime>= {$startTime} and l.createtime<{$endTime} and l.sendtime<={$thistime}   {$where} order by l.id desc,l.createtime desc {$ltext}  ";
            $list = $RecordLog->query($sql);
            $sql = "SELECT count(l.id) as total FROM `iot_record_log` l  USE INDEX(`primary`)   where l.mid = {$member['id']}  and  l.createtime>= {$startTime} and l.createtime<{$endTime} and l.sendtime<={$thistime}  {$where}  ";
            $total = $RecordLog->query($sql);
            
        }else{
            $sql = "SELECT id FROM `iot_notice`  WHERE 1  $nwhere";
            $nidList = $RecordLog->query($sql);
            $nidText = '';
            if(!empty($nidList)){
                foreach ($nidList as $v){
                    $nidText .= "'".$v['id']."',";
                }
                
            }
        
        $nidText = rtrim($nidText,',');
        
        $where.=" and l.nid in($nidText) ";
        $sql = "SELECT l.id, l.recordid AS recordId,l.isread as isRead,l.issend as isSend,l.isexpire as isExpire,FROM_UNIXTIME(sendtime/1000,'%Y-%m-%d %H:%i:%S') timetText,l.nid   FROM `iot_record_log` l  USE INDEX(`primary`) where l.mid = {$member['id']}  and  l.createtime>= {$startTime} and l.createtime<{$endTime} and l.sendtime<={$thistime}   {$where} order by l.id desc,l.createtime desc {$ltext}  ";
       
        $list = $RecordLog->query($sql);
        $sql = "SELECT count(l.id) as total FROM `iot_record_log` l USE INDEX(`primary`)  where l.mid = {$member['id']}  and  l.createtime>= {$startTime} and l.createtime<{$endTime} and l.sendtime<={$thistime}  {$where}  ";
        $total = $RecordLog->query($sql);
        }
        $noticeModel = new \app\common\model\Notice;
       
        foreach ($list as $k=>$v){
            $info = $noticeModel->get(array('id'=>$v['nid']));
            $list[$k]['noticeSign'] = $info['noticesign'];
            $info = $RecordLog->query("SELECT valueType,unitlabel,val,calculation FROM `iot_notice_value` WHERE tid = {$v['id']} ");
            if(!empty($info)){
                $list[$k]['valueType'] = $info[0]['valueType'];
                $list[$k]['unitlabel'] = $info[0]['unitlabel'];
                $list[$k]['val'] = $info[0]['val'];
                $list[$k]['calculation'] = $info[0]['calculation'];
            }else{
                $list[$k]['valueType'] = '';
                $list[$k]['unitlabel'] = '';
                $list[$k]['val'] = '';
                $list[$k]['calculation'] = '';
            }
            
        }
        
        $result = [
            'result' => true,
            'code' => 1,
            'msg'  => '返回成功',
            'total' => $total[0]['total'],
            'data' => $list
        ];
        return json($result);
       // $this->success('获取成功',$list);
        
    }
    
    /**
     * 添加通知
     */
    public function addNotice(){
        $token = $this->request->post('token');
        $title = $this->request->post('title');
        $did = $this->request->post('did');
        $unitLabel = $this->request->post('unitLabel');
        $min = $this->request->post('min');
        $max = $this->request->post('max');
        $isApp = $this->request->post('isApp');
        $isClient = $this->request->post('isClient');
        $isPhone = $this->request->post('isPhone');
        $forbidden = $this->request->post('forbidden');
        $phone = $this->request->post('phone');
        $content = $this->request->post('content');
        $noticeSign = $this->request->post('noticeSign');
        $keeptime = $this->request->post('keepTime');
        $calculation = $this->request->post('calculation');
        $operationWay = $this->request->post('operationWay');
        $isCall = $this->request->post('isCall');
        $callNumber = $this->request->post('callNumber');
        $noticeType = $this->request->post('noticeType');
        $voiceCode = $this->request->post('voiceCode');
        $alarmName = $this->request->post('alarmName');
        //传感器标题
        $sensorTitle = $this->request->post('sensorTitle');
         $sensorPort = $this->request->post('sensorPort');
        //夜间模式
        $isNight = $this->request->post('isNight');
        //关闭所有开关
        $isOnoff = $this->request->post('isOnoff');
        $banDid = $this->request->post('banDid');
        //相隔时长
        $delaytime = (int)$this->request->post('delayTime');
        //权重
        $weigh = (int)$this->request->post('weigh');
        
        //检查
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $sensor = $examine->checkUnit($did, $member['id']);
        if(empty($noticeSign)){
            $this->error('通知标签不能为空');
        }
        if(empty($title)){
            $this->error('标题不能为空');
        }
        if($calculation != 'avg' && $calculation != 'max' && $calculation != 'min'){
            $this->error('取值有误');
        }
        if(!empty($operationWay) && $operationWay!='polling' && $operationWay!='task'){
            $this->error('运行方式设置有误');
        }
        if($isOnoff==1){
            if(empty($banDid)){
                $banDid = $did;
            }
        }
        
        if(empty($operationWay)){
            $operationWay = 'polling';
        }
        if(!empty($noticeType)){
            if($noticeType!='value' && $noticeType!='offline'){
                $this->error('类型有误');
            }
        }else{
            $noticeType = 'value';
        }
        if($keeptime<0){
            $this->error('延时时间有误');
        }
        
        if($min<0){
            $this->error('最小值有误');
        }
        if($max<0){
            $this->error('最大值有误');
        }
        if($isApp!=0 && $isApp!=1){
            $this->error('授权App有误');
        }
        if($isClient!=0 && $isClient!=1){
            $this->error('授权客户端有误');
        }
        if($isPhone!=0 && $isPhone!=1){
            $this->error('授权手机短信有误');
        }elseif($isPhone == 1){
            if(empty($phone)){
                $this->error('手机号不能为空');
            }
            if(empty($content)){
                $this->error('短信内容不能为空');
            }
        }
        if($forbidden!=0 && $forbidden!=1){
            $this->error('授权禁止有误');
        }
        if($isCall!=0 && $isCall!=1){
            $this->error('授权呼叫电话有误');
        }elseif($isCall == 1){
            if(empty($callNumber)){
                $this->error('呼叫电话不能为空');
            }
            if(empty($alarmName)){
                $this->error('呼叫警告内容不能为空');
            }
            if(empty($voiceCode)){
                $this->error('呼叫语音模板ID不能为空');
            }
        }
        if(!empty($isNight) && $isNight!='on' && $isNight!= 'off'){
            $this->error('夜间模式参数有误');
        }
        if(empty($isNight)){
            $isNight = 'off';
        }
        $sensorListModel = new \app\common\model\SensorList;
        $sensorModel = new \app\common\model\Sensor;
        if($noticeType!='offline'){
        if(empty($sensorTitle)){
            $this->error('传感器标题不能为空');
        }
      if(!empty($sensorPort)){
            $sensorInfo = $sensorListModel->get(array('did'=>$did,'port'=>$sensorPort,'title' => $sensorTitle,'label'=>$unitLabel));
        }else{
            $sensorInfo = $sensorListModel->get(array('did'=>$did,'title' => $sensorTitle,'label'=>$unitLabel));
        }
            if(empty($sensorInfo)){
                $this->error('该组建单元并没绑定该传感器');
            }

        }
        $time = new ChangeTime();
       // Db::startTrans();
      
            //参考值取值
            if($noticeType!='offline'){
            $data = array(
                'rtype' => 2,
                'did' => $did,
                'sensorid' => $sensorInfo['id'],
                'calculation' => $calculation,
                'unitlabel' => $unitLabel,
                'createtime' => $time->getMsectime()
            );
            }else{
                $data = array(
                    'rtype' => 4,
                    'did' => $did,
                    'createtime' => $time->getMsectime()
                );
            }
            Db::name('range')->insert($data);
            $referenceid = Db::name('range')->getLastInsID();
            
            //最小值取值
            if($noticeType!='offline'){
           if($min!=0){
            $data = array(
                'rtype' => 1,
                'fixedvalue' => $min,
                'unitlabel' => $unitLabel,
                'createtime' => $time->getMsectime()
            );
            }else{
                $data = array(
                    'rtype' => 0,
                    'createtime' => $time->getMsectime()
                );
            }
            }else{
                $data = array(
                    'rtype' => 0,
                    'did' => $did,
                    'createtime' => $time->getMsectime()
                );
            }
            Db::name('range')->insert($data);
            $minid =  Db::name('range')->getLastInsID();
           
//             if($max>0){
                //最大值取值
            if($noticeType!='offline'){
                if($max!=0){
                $data = array(
                    'rtype' => 1,
                    'fixedvalue' => $max,
                    'unitlabel' => $unitLabel,
                    'createtime' => $time->getMsectime()
                );
                }else{
                    $data = array(
                        'rtype' => 0,
                        'did' => $did,
                        'createtime' => $time->getMsectime()
                    );
                }
            }else{
                $data = array(
                    'rtype' => 0,
                    'did' => $did,
                    'createtime' => $time->getMsectime()
                );
            }

            Db::name('range')->insert($data);
            $maxid =  Db::name('range')->getLastInsID();
            
            if($isPhone == 1){
                $phone = explode(",", $phone);
                $phones = serialize($phone);
            }else{
                $phones = '';
            }
            $sql = "SELECT u.mid FROM `iot_member_unit` u JOIN `iot_member` m ON m.`id` = u.`mid` WHERE m.`member_type` = 'common' AND did = '{$did}'";
            $memberList = $sensorListModel->query($sql);
            $members = array();
            foreach ($memberList as $k=>$v){
                $members[$k] = $v['mid'];
            }
            $person = $members; 
            $people = serialize($person);
//             try {
            $data = array(
                'mid' => $member['id'],
                'title' => $title,
                'referenceid' => $referenceid,
                'minid' => $minid,
                'maxid' => $maxid,
                'phone' => $phones,
                'content' => $content,
                'noticesign' => $noticeSign,
                'createtime' => $time->getMsectime(),
                'keeptime' => intval($keeptime),
                'forbidden' => intval($forbidden),
                'isClient' => intval($isClient),
                'isApp' => intval($isApp),
                'people' => $people,
                'operationWay' => $operationWay,
                'isCall' => $isCall,
                'callNumber' => $callNumber,
                'alarmName' => $alarmName,
                'voiceCode' => $voiceCode,
                'isNight' => $isNight,
                'isOnoff' => $isOnoff,
                'banDid' => $banDid,
                'delaytime' =>$delaytime
                
            );
            Db::name('notice')->insert($data);
            $addId =  Db::name('notice')->getLastInsID();
            if($addId>0){
             //   Db::commit();
                
                //如是任务模式，需发送检查任务
                if($data['operationWay'] == 'task' && $data['forbidden']== 0){
                    $client = new WebSocketClient;
                    $client->connect('127.0.0.1', '8282', '/');
                    $sendData = array();
                    $sendData['cmd'] = 'addTask';
                    $sendData['data'] = array(
                        'title' => 'cherkNotice'.$addId,
                        'url' => $_SERVER['SERVER_NAME'].'/api/task/checkNotice',
                        'option'=>array(
                            'id'=>$addId
                        ),
                        'time'=>$data['keeptime'] * 60,
                        'persistent'=>1,
                        'checkUrl' => $_SERVER['SERVER_NAME'].'/api/task/checkNoticeTask?id='.$addId
                    );
                    $sendText = json_encode($sendData);
                    $rs = $client->sendData($sendText);
                    $client->disconnect();
                }
                
                $this->success('添加成功',array('addId'=>$addId));
            }else{
               // Db::rollback();
                $this->error('添加失败');
            }
           
//         } catch (ValidateException $e) {
//             Db::rollback();
//             $this->error($e->getMessage());
//         } catch (PDOException $e) {
//             Db::rollback();
//             $this->error($e->getMessage());
//         } catch (Exception $e) {
//             Db::rollback();
//             $this->error($e->getMessage());
//         }
        
        
        
    }
       
       
     /**
     * 修改通知
     */
    public function updateNotice(){
        $token = $this->request->post('token');
        $id = $this->request->post('id');
        $noticeModel = new \app\common\model\Notice;
        $info = $noticeModel->get(array('id'=>$id));
        if(empty($info)){
            $this->error('该通知不存在');
        }
        
        $title = $this->request->post('title');
        $did = $this->request->post('did');
        $unitLabel = $this->request->post('unitLabel');
        $min = $this->request->post('min');
        $max = $this->request->post('max');
        $isApp = $this->request->post('isApp');
        $isClient = $this->request->post('isClient');
        $isPhone = $this->request->post('isPhone');
        $forbidden = $this->request->post('forbidden');
        $phone = $this->request->post('phone');
        $content = $this->request->post('content');
        $noticeSign = $this->request->post('noticeSign');
        $keeptime = $this->request->post('keepTime');
        $calculation = $this->request->post('calculation');
        $operationWay = $this->request->post('operationWay');
        $isCall = $this->request->post('isCall');
        $callNumber = $this->request->post('callNumber');
        $noticeType = $this->request->post('noticeType');
        $voiceCode = $this->request->post('voiceCode');
        $alarmName = $this->request->post('alarmName');
        //传感器标题
        $sensorTitle = $this->request->post('sensorTitle');
        $sensorPort = $this->request->post('sensorPort');
        //关闭所有开关
        $isOnoff = $this->request->post('isOnoff');
        $banDid = $this->request->post('banDid');
        
        $delaytime = (int)$this->request->post('delayTime');
        
        $port = $this->request->post('port');
        //夜间模式
        $isNight = $this->request->post('isNight');
        //权重
        $weigh = $this->request->post('weigh');
        
        //检查
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $sensor = $examine->checkUnit($did, $member['id']);
     
       
       
        $data = array();
        if(!empty($noticeSign)){
            $data['noticesign'] = $noticeSign;
        }
        if(!empty($title)){
            $data['title'] = $title;
        }
        if($calculation != 'avg' && $calculation != 'max' && $calculation != 'min'){
            $this->error('取值有误');
        }
        if(!empty($operationWay) && $operationWay!='polling' && $operationWay!='task'){
            $this->error('运行方式设置有误');
        }
        if($isOnoff==1){
            if(empty($banDid)){
               $banDid = $did;
            }
        }
        if(empty($operationWay)){
            $operationWay = 'polling';
        }
        if(!empty($noticeType)){
            if($noticeType!='value' && $noticeType!='offline'){
                $this->error('类型有误');
            }
        }else{
            $noticeType = 'value';
        }
        if($keeptime<0){
            $this->error('延时时间有误');
        }
        $data['keeptime'] = intval($keeptime);
        if($min<0){
            $this->error('最小值有误');
        }
        if($max<0){
            $this->error('最大值有误');
        }
        if($isApp!=0 && $isApp!=1){
            $this->error('授权App有误');
        }
        if($isClient!=0 && $isClient!=1){
            $this->error('授权客户端有误');
        }
        if($isPhone!=0 && $isPhone!=1){
            $this->error('授权手机短信有误');
        }elseif($isPhone == 1 && empty($info['phone'])){
            if(empty($phone)){
                $this->error('手机号不能为空');
            }
            if(empty($content)){
                $this->error('短信内容不能为空');
            }
        }
        if($isCall!=0 && $isCall!=1){
            $this->error('授权呼叫电话有误');
        }elseif($isCall == 1){
            if(empty($callNumber)){
                $this->error('呼叫电话不能为空');
            }
            if(empty($alarmName)){
                $this->error('呼叫报警内容不能为空');
            }
            if(empty($voiceCode)){
                $this->error('呼叫语音模板Id不能为空');
            }
        }
        if(!empty($isNight) && $isNight!='on' && $isNight!= 'off'){
            $this->error('夜间模式参数有误');
        }
        if(empty($isNight)){
            $isNight = 'off';
        }
        if($forbidden!=0 && $forbidden!=1){
            $this->error('授权禁止有误');
        }
        $sensorModel = new \app\common\model\Sensor;
        $sensorListModel = new \app\common\model\SensorList;
        if($noticeType!='offline'){
        
        if(empty($sensorTitle)){
            $this->error('传感器标题不能为空');
        }
      if(!empty($sensorPort)){
            $sensorInfo = $sensorListModel->get(array('did'=>$did,'port'=>$sensorPort,'title' => $sensorTitle,'label'=>$unitLabel));
        }else{
            $sensorInfo = $sensorListModel->get(array('did'=>$did,'title' => $sensorTitle,'label'=>$unitLabel));
        }
       
            if(empty($sensorInfo)){
                $this->error('该组建单元并没绑定该传感器');
            }
         
        }
        $rangeModel = new \app\common\model\Range;
        $time = new ChangeTime();
        Db::startTrans();
       
            if($noticeType!='offline'){
            $rdata = array(
                'rtype' => 2,
                'did' => $did,
                'sensorid' => $sensorInfo['id'],
                'calculation' => $calculation,
                'unitlabel' => $unitLabel,
            );
            }else{
                $rdata = array(
                    'rtype' => 4,
                    'did' => $did,
                    'sensorid' => 0,
                    'calculation' => '',
                    'unitlabel' => '',
                );
            }
            $rangeModel->update($rdata,array('id'=>$info['referenceid']));
            if($noticeType!='offline'){
            if($min!=0){
                $rdata = array(
                    'rtype' => 1,
                    'fixedvalue' => $min,
                    'unitlabel' => $unitLabel,
                );
            }else{
                $rdata = array(
                    'rtype' => 0,
                );
            }
            }else{
                $rdata = array(
                    'rtype' => 0,
                    'fixedvalue' => 0,
                    'unitlabel' => ''
                );
            }
            $rangeModel->update($rdata,array('id'=>$info['minid']));
            
//             if($max>0){
            if($noticeType!='offline'){
                if($max!=0){
                $rdata = array(
                    'rtype' => 1,
                    'fixedvalue' => $max,
                    'unitlabel' => $unitLabel,
                );
                }else{
                    $rdata = array(
                        'rtype' => 0,
                        'fixedvalue' => 0,
                        'unitlabel' => ''
                    );
                }
            }else{
                $rdata = array(
                    'rtype' => 0,
                    'fixedvalue' => 0,
                    'unitlabel' => ''
                );
            }

            $rangeModel->update($rdata,array('id'=>$info['maxid']));
            
            if($isPhone == 1){
                if(!empty($phone)){
                $phone = explode(",", $phone);
                $phones = serialize($phone);
                }else{
                    $phones = $info['phone'];
                }
            }elseif($isPhone == 0){
                $phones = '';
            }
            $data['isCall'] = $isCall;
            if($isCall == 1){
                $data['callNumber'] = $callNumber;
                $data['alarmName'] = $alarmName;
                $data['voiceCode'] = $voiceCode;
            }else{
                $data['isCall'] = 0;
                $data['callNumber'] = '';
                $data['alarmName'] = '';
                $data['voiceCode'] = '';
            }
            $data['isOnoff'] = $isOnoff;
            if($isOnoff == 1){
                $data['banDid'] = $banDid;
            }else{
                $data['banDid'] = '';
            }
            if(!empty($content)){
                $data['content'] = $content;
            }
            if(!empty($operationWay)){
            $data['operationWay'] = $operationWay;
            }
            $data['phone'] = $phones;
            $data['forbidden'] = intval($forbidden);
            $data['isClient'] = intval($isClient);
            $data['isApp'] = intval($isApp);
            $data['isNight'] = $isNight;
            $data['delaytime'] = $delaytime;
            $data['weigh'] = $weigh;
           // $data['noticetype'] = $unitLabel;
            try {
            $noticeModel->update($data,array('id'=>$id));
                Db::commit();
               
        } catch (ValidateException $e) {
            Db::rollback();
            $this->error($e->getMessage());
        } catch (PDOException $e) {
            Db::rollback();
            $this->error($e->getMessage());
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        
        $info['forbidden']  = intval($info['forbidden']);
        $client = new WebSocketClient;
        $client->connect('127.0.0.1', '8282', '/');
        if($info['operationWay'] == 'task' && $info['forbidden'] == 0){
            $sendData = array();
            $sendData['cmd'] = 'delTask';
            $sendData['data'] = array(
                'title' => 'checkNotice'.$info['id'],
            );
            $sendText = json_encode($sendData);
            $rs = $client->sendData($sendText);
        }
        //如是任务模式，需发送检查任务
        if($data['operationWay'] == 'task'  &&  $data['forbidden']== 0){
            $sendData = array();
            $sendData['cmd'] = 'addTask';
            $sendData['data'] = array(
                'title' => 'checkNotice'.$info['id'],
                'url' => $_SERVER['SERVER_NAME'].'/api/task/checkNotice?id='.$info['id'],
                'option'=>array(
                    'id'=>$info['id']
                ),
                'time'=>$data['keeptime'] * 60,
                'persistent'=>1,
                'checkUrl' => $_SERVER['SERVER_NAME'].'/api/task/checkNoticeTask?id='.$info['id']
            );
            $sendText = json_encode($sendData);
            $rs = $client->sendData($sendText);
           
        }
      
        //如果通知为禁用就删除任务
        if($data['forbidden'] == 1 && $info['forbidden'] == 0 && $info['operationWay'] == 'task'){
            $client = new WebSocketClient;
            $client->connect('127.0.0.1', '8282', '/');
            $sendData['data'] = array(
                'title' => 'checkNotice'.$info['id'],
            );
            $sendText = json_encode($sendData);
            $rs = $client->sendData($sendText);
        }
        $client->disconnect();
        $this->success('修改成功');
        
    }
    
    
    
    
    /****
     * 
     * 删除通知
     * 
     * ***/
    public function delNotice(){
        $id = $this->request->post('id');
        $token = $this->request->post('token');
        //检查
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $noticeModel = new \app\common\model\Notice;
        $rangeModel = new \app\common\model\Range;
        $info = $noticeModel->get(array('id'=>$id));
        if(empty($info)){
            $this->error('该通知不存在');
        }
        //通知删除，任务销毁
        if($info['operationWay'] == 'task' && $info['forbidden'] == 0){
            $client = new WebSocketClient;
            $client->connect('127.0.0.1', '8282', '/');
            $sendData['data'] = array(
                'title' => 'checkNotice'.$info['id'],
            );
            $sendText = json_encode($sendData);
            $rs = $client->sendData($sendText);
            $client->disconnect();
        }
      
        $r = $rangeModel->where(array('id'=>$info['referenceid']))->delete();
        $r = $rangeModel->where(array('id'=>$info['minid']))->delete();
        $r = $rangeModel->where(array('id'=>$info['maxid']))->delete();
        $r = $noticeModel->where(array('id'=>$id))->delete();
        if(!empty($r)){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
        
    }
       
    /****
     * 
     * 查询通知
     * 
     * ***/
    public function getNoticeInfo(){
        $id = $this->request->post('id');
        $token = $this->request->post('token');
        //检查
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $noticeModel = new \app\common\model\Notice;
        $rangeModel = new \app\common\model\Range;
        $info = $noticeModel->get(array('id'=>$id));
        if(empty($info)){
            $this->error('该通知不存在');
        }
        
        $refereceInfo = $rangeModel->get(array('id'=>$info['referenceid']));
        $minInfo = $rangeModel->get(array('id'=>$info['minid']));
        $maxInfo = $rangeModel->get(array('id'=>$info['maxid']));
        $max = $maxInfo['rtype'] == 0?0:$maxInfo['fixedvalue'];
        $min = $minInfo['rtype'] == 0?0:$minInfo['fixedvalue'];
        $isPhone = !empty($info['phone']) ? 1:0;
        $phone = '';
        if(!empty($info['phone'])){
            $phone = unserialize($info['phone']);
        }
         $sensorListModel = new \app\common\model\SensorList;
        $sensorInfo = $sensorListModel->get(array('id'=>$refereceInfo['sensorid']));
        $reData = array(
            'title' => $info['title'],
            'did' => $refereceInfo['did'],
            'unitLabel' =>$refereceInfo['unitlabel'],
            'min' => $min,
            'max' => $max,
            'isApp' => $info['isApp'],
            'isClient' => $info['isClient'],
            'isPhone' => $isPhone,
            'phone' => $phone,
            'content' => $info['content'],
            'noticeSign' => $info['noticesign'],
            'forbidden' => $info['forbidden'],
            'keepTime' => $info['keeptime'],
            'delayTime' => $info['delaytime'],
            'calculation' => $refereceInfo['calculation'],
            'operationWay' => $info['operationWay'],
            'isCall' => $info['isCall'],
            'callNumber' => $info['callNumber'],
            'isOnoff' => $info['isOnoff'],
             'noticeSign' => $info['noticesign'],
            'noticeType' => $refereceInfo['rtype'],
            'sensorTitle' =>$sensorInfo['title'],
            'sensorPort' =>$sensorInfo['port']
        );
        
        $this->success('获取成功',$reData);
    }
   
    /****
     *
     * 免打扰
     *
     * ***/
    public function toSetNotDisturb(){
        $disturbType  = $this->request->post('disturbType');
        $nid  = (int)$this->request->post('nid');
        $did = $this->request->post('did');
        $token = $this->request->post('token');
        $startTime = $this->request->post('startTime');
        $endTime = $this->request->post('endTime');
        $banApp = (int)$this->request->post('banApp');
        $banClient = (int)$this->request->post('banClient');
        $banOnoff = (int)$this->request->post('banOnoff');
        $banMessage = (int)$this->request->post('banMessage');
        $banPhone = (int)$this->request->post('banPhone');
        $everyday = (int)$this->request->post('everyday');
        //检查
        $examine = new Examine;
        $member = $examine->checkMember($token);
        if($startTime == $endTime){
            $this->error('开始结束时间不能一样');
        }
        $startHour = date('H',$startTime/1000);
        $endHour = date('H',$endTime/1000);
        if($startHour>$endHour && !empty($everyday)){
            $this->error('时间有误');
        }
        
        $startMinute = date('i',$startTime/1000);
        $endMinute = date('i',$endTime/1000);
        switch ($disturbType){
            case 'unit':
                
                if(!empty($did)){
                    $unit = $examine->checkUnit($did, $member['id']);
                }else{
                    $this->error('组建单元id不能为空');
                }
              
                $disturbModel = new \app\common\model\NoticeNotDisturb;
                $info = $disturbModel->get(array('did'=>$did,'disturbtype'=>$disturbType));
                $time = new ChangeTime();
                $data = array(
                    'starttime' => $startTime,
                    'endtime' => $endTime,
                    'starthour' => $startHour,
                    'endhour' => $endHour,
                    'startminute' => $startMinute,
                    'endminute' => $endMinute,
                    'banapp' => $banApp,
                    'banclient' => $banClient,
                    'banonoff' => $banOnoff,
                    'banmessage' => $banMessage,
                    'banphone' => $banPhone,
                    'everyday' => $everyday,
                    'updatetime' => $time->getMsectime()
                );
                if(!empty($info)){
                    $r = $disturbModel->update($data,array('did'=>$did,'disturbtype'=>$disturbType));
                }else{
                    $data['did'] = $did;
                    $data['disturbtype'] = $disturbType;
                    $r = $disturbModel->save($data);
                }
                
                
                break;
                
           case 'notice':
               
               $noticeModel = new \app\common\model\Notice;
               $notice = $noticeModel->get(array('id'=>$nid));
               if(empty($notice)){
                   $this->error('该通知不存在');
               }else{
                   
                   $disturbModel = new \app\common\model\NoticeNotDisturb;
                   $info = $disturbModel->get(array('nid'=>$nid,'disturbtype'=>$disturbType));
                   $time = new ChangeTime();
                   $data = array(
                       'starttime' => $startTime,
                       'endtime' => $endTime,
                       'starthour' => $startHour,
                       'endhour' => $endHour,
                       'startminute' => $startMinute,
                       'endminute' => $endMinute,
                       'banapp' => $banApp,
                       'banclient' => $banClient,
                       'banonoff' => $banOnoff,
                       'banmessage' => $banMessage,
                       'banphone' => $banPhone,
                       'everyday' => $everyday,
                       'updatetime' => $time->getMsectime()
                   );
                   if(!empty($info)){
                       $r = $disturbModel->update($data,array('nid'=>$nid,'disturbtype'=>$disturbType));
                   }else{
                       $data['nid'] = $nid;
                       $data['disturbtype'] = $disturbType;
                       $r = $disturbModel->save($data);
                   }
                   
               }
               
               break;
               default:
                   $this->error('类型有误');
                   break;
        }
       
        
        
        if(!empty($r)){
            $this->success('设置成功');
        }else{
            $this->error('设置失败');
        }
        
    }
    
    /****
     *
     * 获取免打扰信息
     *
     * ***/
    public function getNotDisturbInfo(){
        $disturbType  = $this->request->post('disturbType');
        $noticeid  = $this->request->post('nid');
        $did = $this->request->post('did');
        $token = $this->request->post('token');
        //检查
        $examine = new Examine;
        $member = $examine->checkMember($token);
      
        $disturbModel = new \app\common\model\NoticeNotDisturb;
        $where = '';
        if(!empty($disturbType)){
            $where .= ' and disturbtype = '.$disturbType.' ';
        }
        if(!empty($noticeid) && !empty($did)){
            $unit = $examine->checkUnit($did, $member['id']);
            $where .= " and (nid in ({$noticeid})  or did = '{$did}' )";
            
        }else if(!empty($did) && empty($noticeid)){
            $unit = $examine->checkUnit($did, $member['id']);
            $where .= " and  did = '{$did}' ";
        }elseif (!empty($noticeid) && empty($did)){
            $where .= " and nid in ({$noticeid})  ";
        }else{
         $this->error('组建单元和通知id必填一项');
        }
        
        $sql = "SELECT * FROM `iot_notice_not_disturb` WHERE 1 {$where}";
        try {
        $list = $disturbModel->query($sql);
        }catch (PDOException $e){
            $this->error('获取失败');
        }
        $data = array();
        foreach ($list as $i=>$info){
        $data[$i] = array(
            'startTime' => $info['starttime'],
            'startDate' => date("Y-m-d H:i:s", $info['starttime']/1000),
            'endTime' => $info['endtime'],
            'endDate' => date("Y-m-d H:i:s", $info['endtime']/1000),
            'startHour' => $info['starthour'],
            'endHour' => $info['endhour'],
            'startMinute' => $info['startminute'],
            'endMinute' => $info['endminute'],
            'banApp' => $info['banapp'],
            'banClient' => $info['banclient'],
            'banOnoff' => $info['banonoff'],
            'banMessage' => $info['banmessage'],
            'banPhone' => $info['banphone'],
            'everyday' => $info['everyday'],
            'updateDate' =>  date("Y-m-d H:i:s", $info['updatetime']/1000)
        );
        }
        
        if(!empty($info)){
            $this->success('获取成功',$data);
        }else{
            $this->error('暂无设置免打扰配置');
        }
        
    }
    
      public function getNoticeList(){
        $token = $this->request->post('token');
        //检查
        $examine = new Examine;
        $member = $examine->checkMember($token);
        $min = $this->request->post('min',0);
        $max = $this->request->post('max',0);
        $unitlabel = $this->request->post('unitlabel');
        $did = $this->request->post('did');
         $forbidden = $this->request->post('forbidden');
        $where = array();
        if(empty($did)){
            $this->error('组建单元id不能为空');
        }
           if(!empty($forbidden)){
            $where['forbidden'] = $forbidden=='on'?1:0;
        }
        $didArray = explode(",", $did);
        $rangeModel = new \app\common\model\Range;
        if(!empty($unitlabel)){
            $unitlabelArray = explode(",", $unitlabel);
            $total = $rangeModel->where('unitlabel', 'in', $unitlabelArray)->count();
            $total = intval($total);
            if($total == 0){
                $this->error('请检查标签是否有误');
            }
            $where['Range.unitlabel'] = array('in', $unitlabelArray);
        }
        
        $minids = array();
        if($min!=0){
            if(!empty($unitlabelArray)){
                $minList = $rangeModel->field('id')->where(array('rtype' => 1,'fixedvalue'=>array('egt',$min)))->where('unitlabel', 'in', $unitlabelArray)->select();
            }else{
                $minList = $rangeModel->field('id')->where(array('rtype' => 1,'fixedvalue'=>array('egt',$min)))->select();
            }
            $minList = collection($minList)->toArray($minList);
            foreach ($minList as $k=>$v){
                $minids[$k] = $v['id'];
            }
            $where['minid'] = array('in', $minids);
        }
        
        
        $maxids = array();
        if($max!=0){
            if(!empty($unitlabelArray)){
                $maxList = $rangeModel->field('id')->where(array('rtype' => 1,'fixedvalue'=>array('elt',$max)))->where('unitlabel', 'in', $unitlabelArray)->select();
            }else{
                $maxList = $rangeModel->field('id')->where(array('rtype' => 1,'fixedvalue'=>array('elt',$max)))->select();
            }
            $maxList = collection($maxList)->toArray($maxList);
            foreach ($maxList as $k=>$v){
                $maxids[$k] = $v['id'];
            }
            $where['maxid'] = array('in', $maxids);
        }
      
        
        $noticeModel = new \app\common\model\Notice;
        
        $list = $noticeModel->with('Range')
        ->where(array('Range.did'=>array('in',$didArray)))
        ->where($where)
        ->select();
        
        
        $list = collection($list)->toArray($list);
        $relist = array();
        foreach ($list as $i=>$v){
            $relist[$i]['id'] = $v['id'];
            $relist[$i]['title'] = $v['title'];
            $relist[$i]['forbidden'] = intval($v['forbidden']);
            $relist[$i]['rtype'] = intval($v['range']['rtype']);
            $relist[$i]['unitlabel'] = $v['range']['unitlabel'];
              $relist[$i]['isApp'] = intval($v['isApp']);
            $relist[$i]['isClient'] = intval($v['isClient']);
            if(!empty($v['phone'])){
                $relist[$i]['isPhone'] = 1;
            }else{
                $relist[$i]['isPhone'] = 0;
            }
        }
        $this->success('获取成功',$relist);
    }
     /**
     * 修改通知备用选项
     */
    public function updateNoticeMore(){
        $token = $this->request->post('token');
        $ids = $this->request->post('ids');
        $type = $this->request->post('type');
        $val = $this->request->post('val');
        $examine = new Examine;
        $member = $examine->checkMember($token);
        if(empty($ids)){
           $this->error('通知id不能为空'); 
        }
        if(empty($val)){
            $this->error('值不能为空');
        }
        $noticeModel = new \app\common\model\Notice;
        $idsArray = explode(",", $ids);
        $total = $noticeModel->where('id', 'in', $idsArray)->count();
        if($total!=sizeof($idsArray)){
            $this->error('通知id有误'); 
        }
        switch ($type){
            case 'personban':
                if($val != 'on'){
                    $val = 'off';
                }
               
                Db::startTrans();
                try {
                    $noticeModel->update(array('personban'=>$val),array('id'=>array('in',$idsArray)));
                Db::commit();
                
                $this->success('修改成功');
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                break;
            case 'forbidden':
                if($val != 'on'){
                    $val = 0;
                }else{
                    $val = 1;
                }
                
                Db::startTrans();
                try {
                    $noticeModel->update(array('forbidden'=>$val),array('id'=>array('in',$idsArray)));
                    Db::commit();
                    
                    $this->success('修改成功');
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                break;
            default:
                $this->error('类型有误');
                break;
        }
        
        
        $this->success('修改成功');
        
    }
}

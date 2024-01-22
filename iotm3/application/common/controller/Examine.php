<?php
namespace app\common\controller;

use app\common\model\PeosonLog;
use think\Db;
use think\exception\HttpResponseException;



class Examine
{
    /**
     * 用户检查
     *
     */
    public function checkMember($token,$type='common'){
        if(empty($token)){
           // $this->error('用户有误，请重新登录');
            $result = [
                'result' => false,
                'code' => 0,
                'msg'  => '用户有误，请重新登录',
                'data' =>null
            ];
            $r =  json($result);
            throw new HttpResponseException($r);
            exit();
        }
        $memberModel = new \app\common\model\Member;
        $member = $memberModel->get(array('token'=>$token));
        if(!empty($member)){
            $time = new ChangeTime();
            $thistime = $time->getMsectime();
            if($thistime> $member['expiretime']+1000){
                $result = [
                    'result' => false,
                    'code' => 0,
                    'msg'  => '登录已超时，请重新登录',
                    'data' =>null
                ];
                $r =  json($result);
                throw new HttpResponseException($r);
                exit();
            }else{
                if($member['member_type'] != $type){
                    $result = [
                        'result' => false,
                        'code' => 0,
                        'msg'  => '用户类型有误',
                        'data' =>null
                    ];
                    $r =  json($result);
                    throw new HttpResponseException($r);
                    exit();
                }else{
                    return $member;
                }
              
            }
            
        }else{
            $result = [
                'result' => false,
                'code' => 0,
                'msg'  => '用户有误，请重新登录',
                'data' =>null
            ];
            $r =  json($result);
            throw new HttpResponseException($r);
            exit();
        }
    }
    /**
     * 组建单元检查
     *
     */
    public function checkUnit($did,$mid){
        if(empty($did)){
            $result = [
                'result' => false,
                'code' => 0,
                'msg'  => '组建单元有误',
                'data' =>null
            ];
            $r =  json($result);
            throw new HttpResponseException($r);
            exit();
        }
        $dids = explode(",", $did);
        
        $unit = new \app\common\model\ComponentUnit;
        $list = $unit ->where('did', 'in', $dids)
        ->order('id','desc')
        ->select();
        $list = collection($list)->toArray();
        if(empty($list) || sizeof($list) != sizeof($dids)){
            $result = [
                'result' => false,
                'code' => 0,
                'msg'  => '组建单元有误',
                'data' =>null
            ];
            echo json_encode($result);
            exit();
        }
        $memberUnit = new \app\common\model\MemberUnit;
        $myunit = $memberUnit
        ->with(['componentunit'])
        ->where('member_unit.did', 'in', $dids)
        ->where(array('mid'=>$mid))
        ->order('id','desc')
        ->select();
        $myunit = collection($myunit)->toArray();
        if(!empty($myunit) || sizeof($myunit) != sizeof($dids)){
            return $myunit;
        }else{
            $result = [
                'result' => false,
                'code' => 0,
                'msg'  => '您没有该组建单元使用权限',
                'data' =>null
            ];
            $r =  json($result);
            throw new HttpResponseException($r);
            exit();
        }
    }
 
    
    

    public function cherkReading($did,$type,$getData){
       $unit = new \app\common\model\ComponentUnit;
       $info = $unit->get(array('did'=>$did));
       if(empty($info)){
           $this->error('组建单元编号有误');
       }
       
       $dataArray = $getData;
       
       //按条件筛选值
       $rangeModel = new \app\common\model\Range;
       $sql = "SELECT id FROM `iot_range` WHERE did = '{$did}' and  rtype = 2";
       $rangeList = $rangeModel->query($sql);
       $rangeIdList = array();
       foreach ($rangeList as $v){
           $rangeIdList[] = $v['id'];
       }
       $rangeText = implode(',',$rangeIdList);
       $time = new ChangeTime();
       $thistime = $time->getMsectime();
       
       $Calculation = new Calculation;
       $gizwits = new Gizwits;
       $examine = new Examine;
       //通知部分
       if($type == 'notice'){
           
           $noticeList = array();
           if($rangeText!=''){
               $lessTime = $thistime - 60  * 1000;
               $sql = "SELECT  * FROM `iot_notice` WHERE (referenceid IN({$rangeText}) OR minid IN({$rangeText}) OR maxid IN({$rangeText})) and executiontime< {$thistime} and runtime<={$lessTime} and forbidden = '0' and operationWay = 'task' order by weigh asc ";
               $noticeList = $rangeModel->query($sql);
           }
           $rangeModel = new \app\common\model\Range;
           if(!empty($noticeList)){
               foreach ($noticeList as $v){
                   
                   $rangeInfo = $rangeModel->get(array('id'=>$v['referenceid']));
                   $maxInfo = $rangeModel->get(array('id'=>$v['minid']));
                   $minInfo = $rangeModel->get(array('id'=>$v['maxid']));
                   
                   //检查最大值最小值相关联数值，是否为最新
                   if($rangeInfo['did'] != $dataArray['did']){
                       $res = $examine->cherkDataTime($v['referenceid']);
                   }else {
                       $res = true;
                   }
               }
               if($res == true){
                   if($minInfo['did'] != $dataArray['did']){
                       $minRes = $examine->cherkDataTime($v['minid']);
                   }else {
                       $minRes = true;
                   }
                   if($minRes == true){
                       if($maxInfo['did'] != $dataArray['did']){
                           $maxRes = $examine->cherkDataTime($v['maxid']);
                       }else {
                           $maxRes = true;
                       }
                       if($maxRes == true){
                           $thistime = $time->getMsectime();
                           Db::name('notice')->where(array('id'=>$v['id']))->update(array('runtime'=>$thistime));
                           $referenceInfo = $rangeModel->get(array('id'=>$v['referenceid']));
                           if($rangeInfo['did'] != $dataArray['did']){
                               $referenceVal =  $Calculation->getRange($v['referenceid']);
                           }else{
                               $referenceVal = $dataArray[$rangeInfo['unitlabel']];
                           }
                           if($referenceInfo['rtype'] != 4){
                               if($minInfo['did'] != $dataArray['did']){
                                   $minVal =  $Calculation->getRange($v['minid']);
                               }else{
                                   $minVal = $dataArray[$rangeInfo['unitlabel']];
                               }
                               if($maxInfo['did'] != $dataArray['did']){
                                   $maxVal =  $Calculation->getRange($v['maxid']);
                               }else{
                                   $maxVal = $dataArray[$rangeInfo['unitlabel']];
                               }
                               $msg = $v['content'];
                               $msg = strtr($msg, array("{reference}" => $referenceVal));
                               $msg = strtr($msg, array("{max}" => $maxVal));
                               $msg = strtr($msg, array("{min}" => $minVal));
                               $msg = "【物联网】".$msg;
                               
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
                           }
                       }
                       
                   }
               }
           }
           
       }else{
           $thistime = $time->getMsectime();
           //联动部分
           $linkageList = array();
           if($rangeText!=''){
               $lessTime = $thistime;
               $sql = "SELECT * FROM `iot_linkage` WHERE (referenceid IN({$rangeText}) OR minid IN({$rangeText}) OR maxid IN({$rangeText})) and executiontime<= {$thistime}  and runtime<={$lessTime} and forbidden = '0' and operationWay = 'task'  order by weigh asc ";
               $linkageList = $rangeModel->query($sql);
           }
           
           if(!empty($linkageList)){
               foreach ($linkageList as $v){
                   //检查最大值最小值相关联数值，是否为最新
                   if($rangeInfo['did'] != $dataArray['did']){
                       $res = $examine->cherkDataTime($v['referenceid']);
                   }else {
                       $res = true;
                   }
                   
                   if($res == true){
                       if($minInfo['did'] != $dataArray['did']){
                           $minRes = $examine->cherkDataTime($v['minid']);
                       }else {
                           $minRes = true;
                       }
                       if($minRes == true){
                           if($maxInfo['did'] != $dataArray['did']){
                               $maxRes = $examine->cherkDataTime($v['maxid']);
                           }else {
                               $maxRes = true;
                           }
                           if($maxRes == true){
                               $thistime = $time->getMsectime();
                               Db::name('linkage')->where(array('id'=>$v['id']))->update(array('runtime'=>$thistime));
                               if($minInfo['did'] != $dataArray['did']){
                               $minVal =  $Calculation->getRange($v['minid']);
                               }else {
                                   $minVal =  $dataArray[$minInfo['unitlabel']];;
                               }
                               if($maxInfo['did'] != $dataArray['did']){
                               $maxVal =  $Calculation->getRange($v['maxid']);
                               }else {
                                   $maxVal =  $dataArray[$maxInfo['unitlabel']];;
                               }
                               
                               $referenceInfo = $rangeModel->get(array('id'=>$v['referenceid']));
                               if($rangeInfo['did'] != $dataArray['did']){
                               $referenceVal =  $Calculation->getRange($v['referenceid']);
                               }else{
                                   $referenceVal = $dataArray[$rangeInfo['unitlabel']];
                               }
                               //读取触发值
                               $rangeModel = new \app\common\model\Range;
                               $rangeInfo = $rangeModel->get(array('id'=>$v['referenceid']));
                               
                               
                               if($referenceInfo['rtype'] != 4){
                                   
                                   $runData = array(
                                       'val' => $referenceVal,
                                       'unitlabel' => $rangeInfo['unitlabel'],
                                       'calculation' => $rangeInfo['calculation']
                                   );
                                   
                                   if($minVal!='null' && $maxVal!='null'){
                                       
                                       if($referenceVal>=$minVal && $referenceVal<$maxVal){
                                           $gizwits->sendTask($v,'task',$runData);
                                       }
                                       
                                   }elseif ($minVal=='null'){
                                       if($maxVal == 0){
                                           if($referenceVal<=$maxVal){
                                               $gizwits->sendTask($v,'task',$runData);
                                           }
                                           
                                       }else{
                                           if($referenceVal<$maxVal){
                                               $gizwits->sendTask($v,'task',$runData);
                                           }
                                       }
                                       
                                       
                                   }elseif ($maxVal=='null'){
                                       
                                       if($referenceVal>=$minVal){
                                           $gizwits->sendTask($v,'task',$runData);
                                       }
                                   }
                               }
                           }
                       }
                   }
               }
               
           }
       }
       
   }
    

   /**
    * 检查是否要加入人体记录处理
    * 
    */
    public function checkPeosonLog($type,$data){
        print_r('$type='.$type.PHP_EOL);
        $personLogModel = new PeosonLog;
        if($type == 'person'){
            if($data['val'] == 1){
           
                $this->setPeosonLog($data);
            }
        }else{
            $sql = "SELECT id FROM `iot_sensor_group` WHERE sensorid = {$data['sensorid']} AND groupid = 'bf61b459a768288e4b426282149843cf'";
            $sensorInfo =  $personLogModel->query($sql);
            if(!empty($sensorInfo[0]['id']) && $data['val'] > 2){
                $this->setPeosonLog($data);
            }
           
        }

    }
    /**
     * 人体记录处理
     *
     */
    public function setPeosonLog($data){
        $personLogModel = new PeosonLog;
        $time = new ChangeTime();
        $sql = "SELECT id,endtime FROM `iot_peoson_log` WHERE did = '{$data['did']}' AND sensorid = {$data['sensorid']} AND isexpire = '0' LIMIT 1";
        $info =  $personLogModel->query($sql);
        print_r($info);
        if(!empty($info[0]['id'])){
            $thisTime = $time->getMsectime();
            $beforeTime = $thisTime - 5 * 60 * 1000;
            if($info[0]['endtime'] >= $beforeTime){
                try
                {
                    $personLogModel->update(array('endtime'=>$thisTime),array('id'=>$info[0]['id']));
                    Db::commit();
                }catch (\Exception $e){
                    Db::rollback();
                }
            }else{
                try
                {
                    $personLogModel->update(array('isexpire'=>1),array('did'=>$data['did'],'isexpire'=>0,'sensorid'=>$data['sensorid']));
                    $addData = array(
                        'did' => $data['did'],
                        'sensorid' => $data['sensorid'],
                        'starttime' => $thisTime,
                        'endtime' => $thisTime,
                        'createtime' => $thisTime
                    );
                    $personLogModel->save($addData);
                  
                    Db::commit();
                }catch (\Exception $e){
                    Db::rollback();
                }
            }
        }else{
            $thisTime = $time->getMsectime();
            try
            {
                $addData = array(
                    'did' => $data['did'],
                    'sensorid' => $data['sensorid'],
                    'starttime' => $thisTime,
                    'endtime' => $thisTime,
                    'createtime' => $thisTime
                );
                $personLogModel->save($addData);
                Db::commit();
            }catch (\Exception $e){
                Db::rollback();
            }
            
        }
    }
    
    /******
     * 
     *检查在线状态 
     * */
    public function checkOnLine($did){
        $sensorModel =  new \app\common\model\SensorList;
        $sql = "SELECT id FROM `iot_sensor_list` WHERE did = '{$did}' ORDER BY id asc LIMIT 1";
        $info = $sensorModel->query($sql);
        $timeCon = new ChangeTime;
       
        if(empty($info)){
            return false;
        }else{
            $tdengine = new Tdengine();
            $sql = "select LAST(ts) as ts from sensor_{$info[0]['id']};";
            $log = $tdengine->queryForData($sql);

            if(!empty($log[0]['ts'])){
                $thistime = $timeCon->getMsectime();
                $datatime = $timeCon->getDataFormat($log[0]['ts']);
                $less = ($thistime - $datatime)/1000;
            
                if($less > 1800){
                    return false;
                }else{
                    return true;
                }
            }else{
                return false;
            }
        }
        
    }
    
}

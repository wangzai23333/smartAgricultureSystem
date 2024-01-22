<?php

namespace app\admin\controller;

use Complex\Exception;
use app\common\controller\Backend;
use app\common\controller\Emqx;
use app\common\controller\WebSocketClient;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Linkage extends Backend
{
    
    /**
     * Linkage模型对象
     * @var \app\common\model\Linkage
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Linkage;
        $this->view->assign("onoffList", $this->model->getOnoffList());
    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['range'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                if($params['rrtype'] == '' || $params['mrtype'] == '' || $params['maxrtype'] == ''){
                    $this->error('请选择参考值和区间');
                    return;
                }
                if($params['operationWay'] == 'local' ){
                    if($params['rrtype'] != 2){
                        $this->error('本地方式仅支持单组建单元');
                        return;
                    }
                    if($params['mrtype'] != 1){
                        $this->error('本地方式最小值仅支持值');
                        return;
                    }
                    if($params['maxrtype'] != 1){
                        $this->error('本地方式最大值仅支持值');
                        return;
                    }
                    // if($params['delaytime'] >0){
                    //     $this->error('本地方式延迟时间为0');
                    //     return;
                    // }
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $data = array(
                        'rtype' => $params['rrtype'],
                        'groupid' => $params['rgroupid'],
                        'did' => $params['rdid'],
                        'sensorid' => $params['rsensor'],
                        'fixedvalue' => $params['rfixedvalue'],
                        'calculation' => $params['rcalculation'],
                        'unitlabel' => $params['runitlabel'],
                        'createtime' => $this->model->getMsectime()
                    );
                    Db::name('range')->insert($data);
                    $params['referenceid'] = Db::name('range')->getLastInsID();
                    $data = array(
                        'rtype' => $params['mrtype'],
                        'groupid' => $params['mgroupid'],
                        'did' => $params['mdid'],
                        'sensorid' => $params['msensor'],
                        'fixedvalue' => $params['mfixedvalue'],
                        'calculation' => $params['mcalculation'],
                        'unitlabel' => $params['munitlabel'],
                        'createtime' => $this->model->getMsectime()
                    );
                    Db::name('range')->insert($data);
                    $params['minid'] =  Db::name('range')->getLastInsID();
                    $data = array(
                        'rtype' => $params['maxrtype'],
                        'groupid' => $params['maxgroupid'],
                        'did' => $params['maxdid'],
                        'sensorid' => $params['maxsensor'],
                        'fixedvalue' => $params['maxfixedvalue'],
                        'calculation' => $params['maxcalculation'],
                        'unitlabel' => $params['maxunitlabel'],
                        'createtime' => $this->model->getMsectime()
                    );
                    Db::name('range')->insert($data);
                    $params['maxid'] =  Db::name('range')->getLastInsID();
                    
                    $params['createtime'] =   $this->model->getMsectime();
                    $data = array(
                        'title' => $params['title'],
                        'referenceid' => $params['referenceid'],
                        'did'=>$params['did'],
                        'minid' => $params['minid'],
                        'maxid' => $params['maxid'],
                        'switchnum' => $params['switchnum'],
                        'onoff' => $params['onoff'],
                        'delaytime' => floatval($params['delaytime']),
                        'createtime' => $this->model->getMsectime(),
                        'keeptime' => floatval($params['keeptime']),
                        'weigh' => 100,
                        'operationWay' =>$params['operationWay'],
                        'mid'=>0,
                        'forbidden' => intval($params['forbidden']),
                        'startban' => $params['startban'],
                        'endban' => $params['endban']
                        
                    );
                    $result = $this->model->insert($data);
                    $addId = $this->model->getQuery()->getLastInsID();
                    Db::commit();
                  
                    //如是任务模式，需发送检查任务
                    if($params['forbidden']== 0){
                        if($params['operationWay'] == 'task' ){
                    $client = new WebSocketClient;
                    $client->connect('127.0.0.1', '8282', '/');
                    $sendData = array();
                    $sendData['cmd'] = 'addTask';
                    $sendData['data'] = array(
                        'title' => 'cherkLinkage'.$addId,
                        'url' => $_SERVER['SERVER_NAME'].'/api/task/checkLinkage',
                        'option'=>array(
                            'id'=>$addId
                        ),
                        'time'=>$params['delaytime'] * 60,
                        'persistent'=>1,
                        'checkUrl' => $_SERVER['SERVER_NAME'].'/api/task/checkLinkageTask?id='.$addId
                    );
                    $sendText = json_encode($sendData);
                    $rs = $client->sendData($sendText);
                    $client->disconnect();
                        }else{
                            $emqx = new Emqx();
                            //向设备发送信息
                            $sendTopic = 'UNIT/toReceive/'.$params['rdid'];
                            $sendData =array();
                            $sendData['head'] = array(
                                'symbol'=>'IOTM',
                                'msgid' =>$emqx->getRandromStr(),
                                'cmd' =>'toLinkage'
                            );
                            $sensorModel = new \app\common\model\SensorList;
                            $sensor = $sensorModel->get(array('id'=>$params['rsensor']));
                            $onoff = $params['onoff'] == 'on' ? 1 : 0;
                            $sendData['body'] = array(
                                'taskId' => 'linkage_'.$addId,
                                'minVal' => $params['mfixedvalue'],
                                'maxVal' => $params['maxfixedvalue'],
                                'label' => $params['runitlabel'],
                                'port' => $sensor['port'],
                                'sensorTitle' => $sensor['title'],
                                'onoff' => $onoff,
                                'switchNum' => $params['switchnum'],
                                'keeptime' => floatval($params['keeptime']),
                                'delaytime' => floatval($params['delaytime'])
                            );
                            $sendMeg = json_encode($sendData);
                           $emqx->subscribeForSend($sendTopic, $sendMeg);
                         
                        }
                    }
                    
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
                if ($result !== false) {
                  
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $unit = new \app\common\model\ComponentUnit;
        $units = $unit->select();
        
        $units =  collection($units)->toArray();
        
        $unitlist = array();
        foreach ($units as $v){
            $unitlist[$v['did']] = $v['title'];
        }
        $url = $_SERVER["REQUEST_URI"];
        $urlArray = explode('/',$url);
        $this->view->assign("url", $urlArray['1']);
        $this->view->assign("unitlist", $unitlist);
        return $this->view->fetch();
    }
    /**
     * 批量更新
     */
    public function multi($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        if ($ids) {
            if ($this->request->has('params')) {
                parse_str($this->request->post("params"), $values);
                $values = $this->auth->isSuperAdmin() ? $values : array_intersect_key($values, array_flip(is_array($this->multiFields) ? $this->multiFields : explode(',', $this->multiFields)));
                if ($values) {
                    $rangeModel = new \app\common\model\Range;
                    if(isset($values['forbidden']) && $values['forbidden'] == 0){
                        $info = $this->model->get(array('id'=>$ids));
                        if($info['operationWay'] == 'task'){
                            $client = new WebSocketClient;
                            $client->connect('127.0.0.1', '8282', '/');
                            $sendData = array();
                            $sendData['cmd'] = 'addTask';
                            $sendData['data'] = array(
                                'title' => 'cherkLinkage'.$info['id'],
                                'url' => $_SERVER['SERVER_NAME'].'/api/task/checkLinkage',
                                'option'=>array(
                                    'id'=>$info['id']
                                ),
                                'time'=>$info['delaytime'] * 60,
                                'persistent'=>1,
                                'checkUrl' => $_SERVER['SERVER_NAME'].'/api/task/checkLinkageTask?id='.$info['id']
                            );
                            $sendText = json_encode($sendData);
                            $rs = $client->sendData($sendText);
                            $client->disconnect();
                        }else{
                            $emqx = new Emqx();
                            //向设备发送信息
                            $range = $rangeModel->get(array('id'=>$info['referenceid']));
                            $sendTopic = 'UNIT/toReceive/'.$range['did'];
                            $sendData = array();
                            $sendData['head'] = array(
                                'symbol'=>'IOTM',
                                'msgId' =>$emqx->getRandromStr(),
                                'cmd' =>'toLinkage'
                            );
                            $maxRange = $rangeModel->get(array('id'=>$info['maxid']));
                            $minRange = $rangeModel->get(array('id'=>$info['minid']));
                            $sensorModel = new \app\common\model\SensorList;
                            $sensor = $sensorModel->get(array('id'=>$range['sensorid']));
                            $onoff = $info['onoff'] == 'on' ? 1 : 0;
                            $sendData['body'] = array(
                                'taskId' => 'linkage_'.$info['id'],
                                'minVal' => $minRange['fixedvalue'],
                                'maxVal' => $maxRange['fixedvalue'],
                                'label' => $range['unitlabel'],
                                'port' => $sensor['port'],
                                'sensorTitle' => $sensor['title'],
                                'onoff' => $onoff,
                                'switchNum' => $info['switchnum'],
                                'keeptime' => floatval($info['keeptime']),
                                'delaytime' => floatval($info['delaytime'])
                            );
                            $sendMeg = json_encode($sendData);
                            $emqx->subscribeForSend($sendTopic, $sendMeg);
                        }
                            
                       
                    }else{
                        $info = $this->model->get(array('id'=>$ids));
                        if($info['operationWay'] == 'task'){
                            $client = new WebSocketClient;
                            $client->connect('127.0.0.1', '8282', '/');
                            $sendData = array();
                            $sendData['cmd'] = 'delTask';
                            $sendData['data'] = array(
                                'title' => 'cherkLinkage'.$info['id'],
                            );
                            $sendText = json_encode($sendData);
                            $rs = $client->sendData($sendText);
                            $client->disconnect();
                        }else{
                            $emqx = new Emqx();
                            $range = $rangeModel->get(array('id'=>$info['referenceid']));
                            //向设备发送信息
                            $sendTopic = 'UNIT/toReceive/'.$range['did'];
                            $sendData =array();
                            $sendData['head'] = array(
                                'symbol'=>'IOTM',
                                'msgId' =>$emqx->getRandromStr(),
                                'cmd' =>'toCancel'
                            );
                            $linkageId = array();
                            $linkageId[0] = 'linkage_'.$info['id'];
                            $thistime = $this->model->getMsectime();
                            $sql = "SELECT id FROM `iot_run_task` WHERE lid = {$info['id']} AND cancel = '0' AND runTime>{$thistime}";
                            $idList = $rangeModel->query($sql);
                            $updateText = '';
                            $tids = array();
                            foreach ($idList as $k=>$v){
                                $tids[$k] = $v['id'];
                                $updateText.=$v['id'].',';
                            }
                            $updateText = rtrim($updateText,',');
                            if($updateText!=''){
                            $sql = "UPDATE `iot_run_task` SET cancel = '1' WHERE id IN($updateText)";
                            $r = $rangeModel->query($sql);
                            }
                            $sendData['body'] = array(
                                'taskId' => $tids,
                                'linkageTaskId' => $linkageId
                            );
                            $sendMeg = json_encode($sendData);
                            $emqx->subscribeForSend($sendTopic, $sendMeg);
                        }
                    }
                    
                    $adminIds = $this->getDataLimitAdminIds();
                    if (is_array($adminIds)) {
                        $this->model->where($this->dataLimitField, 'in', $adminIds);
                    }
                    $count = 0;
                    Db::startTrans();
                    try {
                        $list = $this->model->where($this->model->getPk(), 'in', $ids)->select();
                        foreach ($list as $index => $item) {
                            $count += $item->allowField(true)->isUpdate(true)->save($values);
                        }
                        Db::commit();
                    } catch (PDOException $e) {
                        Db::rollback();
                        $this->error($e->getMessage());
                    } catch (Exception $e) {
                        Db::rollback();
                        $this->error($e->getMessage());
                    }
                    if ($count) {
                        $this->success();
                    } else {
                        $this->error(__('No rows were updated'));
                    }
                } else {
                    $this->error(__('You have no permission'));
                }
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }
    
    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                if($params['operationWay'] == 'local' ){
                    if($params['rrtype'] != 2){
                        $this->error('本地方式仅支持单组建单元');
                        return;
                    }
                    if($params['mrtype'] != 1){
                        $this->error('本地方式最小值仅支持值');
                        return;
                    }
                    if($params['maxrtype'] != 1){
                        $this->error('本地方式最大值仅支持值');
                        return;
                    }
                    // if($params['delaytime'] >0){
                    //     $this->error('本地方式延迟时间为0');
                    //     return;
                    // }
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $data = array(
                        'rtype' => $params['rrtype'],
                        'groupid' => $params['rgroupid'],
                        'did' => $params['rdid'],
                        'sensorid' => $params['rsensor'],
                        'fixedvalue' => $params['rfixedvalue'],
                        'calculation' => $params['rcalculation'],
                        'unitlabel' => $params['runitlabel'],
                    );
                    Db::name('range')->where(array('id'=>$row['referenceid']))->update($data);
                    $data = array(
                        'rtype' => $params['mrtype'],
                        'groupid' => $params['mgroupid'],
                        'did' => $params['mdid'],
                        'sensorid' => $params['msensor'],
                        'fixedvalue' => $params['mfixedvalue'],
                        'calculation' => $params['mcalculation'],
                        'unitlabel' => $params['munitlabel'],
                    );
                    Db::name('range')->where(array('id'=>$row['minid']))->update($data);
                    $data = array(
                        'rtype' => $params['maxrtype'],
                        'groupid' => $params['maxgroupid'],
                        'did' => $params['maxdid'],
                        'sensorid' => $params['maxsensor'],
                        'fixedvalue' => $params['maxfixedvalue'],
                        'calculation' => $params['maxcalculation'],
                        'unitlabel' => $params['maxunitlabel'],
                    );
                    Db::name('range')->where(array('id'=>$row['maxid']))->update($data);
                    $params['forbidden'] = intval($params['forbidden']);
                    $oldInfo = $this->model->get($ids);
                    $result = $row->allowField(true)->save($params);
                    Db::commit();
                    $client = new WebSocketClient;
                    $emqx = new Emqx();
                    $rangeModel = new \app\common\model\Range;
                    if($oldInfo['forbidden'] == 0){
                        if($oldInfo['operationWay'] == 'task'){
                        $client->connect('127.0.0.1', '8282', '/');
                        $sendData = array();
                        $sendData['cmd'] = 'delTask';
                        $sendData['data'] = array(
                            'title' => 'cherkLinkage'.$row['id'],
                        );
                        $sendText = json_encode($sendData);
                        $rs = $client->sendData($sendText);
                        $client->disconnect();
                        }else{
                            $range = $rangeModel->get(array('id'=>$oldInfo['referenceid']));
                            //向设备发送信息
                            $sendTopic = 'UNIT/toReceive/'.$range['did'];
                            $sendData =array();
                            $sendData['head'] = array(
                                'symbol'=>'IOTM',
                                'msgId' =>$emqx->getRandromStr(),
                                'cmd' =>'toCancel'
                            );
                            $linkageId = array();
                            $linkageId[0] = 'linkage_'.$oldInfo['id'];
                            $thistime = $this->model->getMsectime();
                            $sql = "SELECT id FROM `iot_run_task` WHERE lid = {$oldInfo['id']} AND cancel = '0' AND runTime>{$thistime}";
                            $idList = $rangeModel->query($sql);
                            $tids = array();
                            $updateText = '';
                            foreach ($idList as $k=>$v){
                                $tids[$k] = $v['id'];
                                $updateText.=$v['id'].',';
                            }
                            $updateText = rtrim($updateText,',');
                            if($updateText!=''){
                            $sql = "UPDATE `iot_run_task` SET cancel = '1' WHERE id IN($updateText)";
                            $r = $rangeModel->query($sql);
                            }
                            $sendData['body'] = array(
                                'taskId' => $tids,
                                'linkageTaskId' => $linkageId
                            );
                            $sendMeg = json_encode($sendData);
                            $emqx->subscribeForSend($sendTopic, $sendMeg);
                        }
                    }
                    //如是任务模式，需发送检查任务
                    if($params['forbidden']==0){
                        if($params['operationWay'] == 'task'){
                        $client->connect('127.0.0.1', '8282', '/');
                        $sendData = array();
                        $sendData['cmd'] = 'addTask';
                        $sendData['data'] = array(
                            'title' => 'cherkLinkage'.$row['id'],
                            'url' => $_SERVER['SERVER_NAME'].'/api/task/checkLinkage',
                            'option'=>array(
                                'id'=>$row['id']
                            ),
                            'time'=>$params['delaytime'] * 60,
                            'persistent'=>1,
                            'checkUrl' => $_SERVER['SERVER_NAME'].'/api/task/checkLinkageTask?id='.$row['id']
                        );
                        $sendText = json_encode($sendData);
                        $rs = $client->sendData($sendText);
                        $client->disconnect();
                        }else{
                            $emqx = new Emqx();
                            //向设备发送信息
                            $sendTopic = 'UNIT/toReceive/'.$params['rdid'];
                            $sendData =array();
                            $sendData['head'] = array(
                                'symbol'=>'IOTM',
                                'msgId' =>$emqx->getRandromStr(),
                                'cmd' =>'toLinkage'
                            );
                            $sensorModel = new \app\common\model\SensorList;
                            $sensor = $sensorModel->get(array('id'=>$params['rsensor']));
                            $onoff = $params['onoff'] == 'on' ? 1 : 0;
                            $sendData['body'] = array(
                                'taskId' => 'linkage_'.$row['id'],
                                'minVal' => $params['mfixedvalue'],
                                'maxVal' => $params['maxfixedvalue'],
                                'label' => $params['runitlabel'],
                                'port' => $sensor['port'],
                                'sensorTitle' => $sensor['title'],
                                'onoff' => $onoff,
                                'switchNum' => $params['switchnum'],
                               'keeptime' => floatval($params['keeptime']),
                                'delaytime' => floatval($params['delaytime'])
                                
                            );
                            $sendMeg = json_encode($sendData);
                            $emqx->subscribeForSend($sendTopic, $sendMeg);
                            
                        }
                    }
                    
                    
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
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        
        $rdata = Db::name('range')->where(array('id'=>$row['referenceid']))->select();
        $mindata = Db::name('range')->where(array('id'=>$row['minid']))->select();
        $maxdata = Db::name('range')->where(array('id'=>$row['maxid']))->select();
        
        $unit = new \app\common\model\ComponentUnit;
        $units = $unit->select();
        
        $units =  collection($units)->toArray();
        
        $unitlist = array();
        foreach ($units as $v){
            $unitlist[$v['did']] = $v['title'];
        }
        $url = $_SERVER["REQUEST_URI"];
        $urlArray = explode('/',$url);
        $this->view->assign("url", $urlArray['1']);
        $this->view->assign("unitlist", $unitlist);
        $this->view->assign("rdata", $rdata[0]);
        $this->view->assign("mindata", $mindata[0]);
        $this->view->assign("maxdata", $maxdata[0]);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
    /**
     * 删除
     */
    public function del($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        if ($ids) {
            $rangeModel = new \app\common\model\Range;
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = $this->model->where($pk, 'in', $ids)->select();
            
            $count = 0;
            Db::startTrans();
            try {
                foreach ($list as $k => $v) {
                    $count += $v->delete();
                    $info = $v;
                    if($info['operationWay'] == 'task'){
                        $client = new WebSocketClient;
                        $client->connect('127.0.0.1', '8282', '/');
                        $sendData = array();
                        $sendData['cmd'] = 'delTask';
                        $sendData['data'] = array(
                            'title' => 'cherkLinkage'.$info['id'],
                        );
                        $sendText = json_encode($sendData);
                        $rs = $client->sendData($sendText);
                        $client->disconnect();
                    }else{
                        $emqx = new Emqx();
                        $range = $rangeModel->get(array('id'=>$info['referenceid']));
                        //向设备发送信息
                        $sendTopic = 'UNIT/toReceive/'.$range['did'];
                        $sendData =array();
                        $sendData['head'] = array(
                            'symbol'=>'IOTM',
                            'msgId' =>$emqx->getRandromStr(),
                            'cmd' =>'toCancel'
                        );
                        $linkageId = array();
                        $linkageId[0] = 'linkage_'.$info['id'];
                        $thistime = $this->model->getMsectime();
                        $sql = "SELECT id FROM `iot_run_task` WHERE lid = {$info['id']} AND cancel = '0' AND runTime>{$thistime}";
                        $idList = $rangeModel->query($sql);
                        $updateText = '';
                        $tids = array();
                        foreach ($idList as $k=>$v){
                            $tids[$k] = $v['id'];
                            $updateText.=$v['id'].',';
                        }
                        $updateText = rtrim($updateText,',');
                        if($updateText!=''){
                        $sql = "UPDATE `iot_run_task` SET cancel = '1' WHERE id IN($updateText)";
                        $r = $rangeModel->query($sql);
                        }
                        $sendData['body'] = array(
                            'taskId' => $tids,
                            'linkageTaskId' => $linkageId
                        );
                        $sendMeg = json_encode($sendData);
                        $emqx->subscribeForSend($sendTopic, $sendMeg);
                    }
                }
                
                Db::commit();
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($count) {
                $this->success();
            } else {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

}

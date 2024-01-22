<?php

namespace app\admin\controller;

use Complex\Exception;
use app\common\controller\Backend;
use app\common\controller\WebSocketClient;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Notice extends Backend
{
    
    /**
     * Notice模型对象
     * @var \app\common\model\Notice
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Notice;

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
                    if($params['people']!=''){
                    $params['people'] = serialize($params['people']);
                    }
                    if($params['phone']!=''){
                    $phone = explode(",", $params['phone']);
                    $params['phone'] = serialize($phone);
                    }
                    $params['createtime'] =   $this->model->getMsectime();
                    
                    $data = array(
                        'mid' => 0,
                        'title' => $params['title'],
                        'referenceid' => $params['referenceid'],
                        'minid' => $params['minid'],
                        'maxid' => $params['maxid'],
                        'phone' => $params['phone'],
                        'content' => $params['content'],
                        'noticesign' => $params['noticesign'],
                        'createtime' => $params['createtime'],
                        'keeptime' => $params['keeptime'],
                        'isClient' => $params['isClient'],
                        'isApp' => $params['isApp'],
                        'people' => $params['people'],
                        'isCall' => $params['isCall'],
                        'callNumber' => $params['callNumber'],
                        'forbidden' => $params['forbidden'],
                        'alarmName' => $params['alarmName'],
                        'voiceCode' => $params['voiceCode'] ,
                        'isOnoff' => intval($params['isOnoff']),
                        'banDid' => $params['banDid'],
                        'delaytime' => $params['delaytime'],
                        
                    );
                    
                    $result = $this->model->insert($data);
                    $addId = $this->model->getQuery()->getLastInsID();
                    Db::commit();
                    
                    //如是任务模式，需发送检查任务
                    if($params['forbidden']==0){
                 
                        $client = new WebSocketClient;
                        $client->connect('127.0.0.1', '8282', '/');
                        $sendData = array();
                        $sendData['cmd'] = 'addTask';
                        $sendData['data'] = array(
                            'title' => 'checkNotice'.$addId,
                            'url' => $_SERVER['SERVER_NAME'].'/api/task/checkNotice',
                            'option'=>array(
                                'id'=>$addId
                            ),
                            'time'=>$params['keeptime'] * 60,
                            'persistent'=>1,
                            'checkUrl' => $_SERVER['SERVER_NAME'].'/api/task/checkNoticeTask?id='.$addId
                        );
                        $sendText = json_encode($sendData);
                        $rs = $client->sendData($sendText);
                        $client->disconnect();
                        
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
        $memberModel = new \app\common\model\Member;
        $members = $memberModel->select();
        
        $members =  collection($members)->toArray();
        
        $memberList = array();
        foreach ($members as $v){
            $memberList[$v['id']] = $v['username'];
        }
        $memberModel = new \app\common\model\Member;
        $members = $memberModel->select();
        
        $members =  collection($members)->toArray();
        
        $memberList = array();
        foreach ($members as $v){
            $memberList[$v['id']] = $v['username'];
        }
        $ids = [];
        
        $unit = new \app\common\model\ComponentUnit;
        $units = $unit->select();
        
        $units =  collection($units)->toArray();
        
        $unitlist = array();
        foreach ($units as $v){
            $unitlist[$v['did']] = $v['title'];
        }
        $this->view->assign("unitlist", $unitlist);
        $this->view->assign("memberList", $memberList);
        $this->view->assign("ids", $ids);
        return $this->view->fetch();
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
                    if($params['people']!=''){
                        $params['people'] = serialize($params['people']);
                    }
                    if($params['phone']!=''){
                        $phone = explode(",", $params['phone']);
                        $params['phone'] = serialize($phone);
                    }
                    $params['forbidden'] = intval($params['forbidden']);
                    $oldInfo = $this->model->get($ids);
                    $result = $row->allowField(true)->save($params);
                    Db::commit();
                    $client = new WebSocketClient;
                    $client->connect('127.0.0.1', '8282', '/');
                    if($oldInfo['forbidden'] == 0){
                        $sendData = array();
                        $sendData['cmd'] = 'delTask';
                        $sendData['data'] = array(
                            'title' => 'checkNotice'.$row['id'],
                        );
                        $sendText = json_encode($sendData);
                        $rs = $client->sendData($sendText);
                    }
                  
                    if($params['forbidden']==0){
                      
                        $sendData = array();
                        $sendData['cmd'] = 'addTask';
                        $sendData['data'] = array(
                            'title' => 'checkNotice'.$row['id'],
                            'url' => $_SERVER['SERVER_NAME'].'/api/task/checkNotice',
                            'option'=>array(
                                'id'=>$row['id']
                            ),
                            'time'=>$params['keeptime'] * 60,
                            'persistent'=>1,
                            'checkUrl' => $_SERVER['SERVER_NAME'].'/api/task/checkNoticeTask?id='.$row['id']
                        );
                        $sendText = json_encode($sendData);
                        $rs = $client->sendData($sendText);
                        
                    }
                    $client->disconnect();
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
        $memberModel = new \app\common\model\Member;
        $members = $memberModel->select();
        
        $members =  collection($members)->toArray();
        
        $memberList = array();
        foreach ($members as $v){
            $memberList[$v['id']] = $v['username'];
        }
        $ids = unserialize($row['people']);
        if($row['phone']!=''){
        $phone = unserialize($row['phone']);
        $row['phone'] = implode($phone,',');
        }
        $url = $_SERVER["REQUEST_URI"];
        $urlArray = explode('/',$url);
        $this->view->assign("url", $urlArray['1']);
        $this->view->assign("memberList", $memberList);
        $this->view->assign("ids", $ids);
        $this->view->assign("rdata", $rdata[0]);
        $this->view->assign("mindata", $mindata[0]);
        $this->view->assign("maxdata", $maxdata[0]);
        $this->view->assign("row", $row);
        $unit = new \app\common\model\ComponentUnit;
        $units = $unit->select();
        
        $units =  collection($units)->toArray();
        
        $unitlist = array();
        foreach ($units as $v){
            $unitlist[$v['did']] = $v['title'];
        }
        $this->view->assign("unitlist", $unitlist);
        return $this->view->fetch();
        
        return $this->view->fetch();
    }

    public function addRange(){
        $unit = new \app\common\model\ComponentUnit;
        $units = $unit->select();
        
        $units =  collection($units)->toArray();
        
        $unitlist = array();
        foreach ($units as $v){
            $unitlist[$v['did']] = $v['title'];
        }
        $group = new \app\common\model\Group;
        $groups = $group->select();
        
        $groups =  collection($groups)->toArray();
        
        $grouplist = array();
        foreach ($groups as $v){
            $grouplist[$v['groupid']] = $v['title'];
        }
        $this->view->assign("unitlist", $unitlist);
        $this->view->assign("grouplist", $grouplist);
        $url = $_SERVER["REQUEST_URI"];
        $urlArray = explode('/',$url);
        $this->view->assign("url", $urlArray['1']);
        return $this->view->fetch();
    }
    public function getSensorList(){
        $did = $this->request->get('did');
      
        $sensorModel = new \app\common\model\SensorList;
        $list = $sensorModel->where(array('did'=>$did))->select();
        $result = [
            'result' => true,
            'code' => 1,
            'msg'  => '返回成功',
            'data' => $list
        ];
        echo json_encode($result);
        
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
                    $info = $this->model->get(array('id'=>$ids));
                    if(isset($values['forbidden']) && $values['forbidden'] == 0){
                       
                       
                            $client = new WebSocketClient;
                            $client->connect('127.0.0.1', '8282', '/');
                            $sendData = array();
                            $sendData['cmd'] = 'addTask';
                            $sendData['data'] = array(
                                'title' => 'checkNotice'.$info['id'],
                                'url' => $_SERVER['SERVER_NAME'].'/api/task/checkNotice',
                                'option'=>array(
                                    'id'=>$info['id']
                                ),
                                'time'=>$info['keeptime'] * 60,
                                'persistent'=>1,
                                'checkUrl' => $_SERVER['SERVER_NAME'].'/api/task/checkNoticeTask?id='.$info['id']
                            );
                            $sendText = json_encode($sendData);
                            $rs = $client->sendData($sendText);
                            $client->disconnect();
                        
                    }elseif (isset($values['forbidden']) && $values['forbidden'] == 1){
                        $client = new WebSocketClient;
                        $client->connect('127.0.0.1', '8282', '/');
                            $sendData = array();
                            $sendData['cmd'] = 'delTask';
                            $sendData['data'] = array(
                                'title' => 'checkNotice'.$info['id'],
                            );
                            $sendText = json_encode($sendData);
                            $rs = $client->sendData($sendText);
                            $client->disconnect();
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
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $list = $this->model
            ->where($where)
            ->order($sort, $order)
            ->paginate($limit);
            
            
            $result = array("total" => $list->total(), "rows" => $list->items());
            
            return json($result);
        }
        return $this->view->fetch();
    }
    
    
}

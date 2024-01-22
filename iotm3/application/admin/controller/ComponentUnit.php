<?php

namespace app\admin\controller;

use Complex\Exception;
use app\common\controller\Backend;
use app\common\controller\Examine;
use app\common\controller\Gizwits;
use app\common\controller\WebSocketClient;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\common\controller\Unit;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class ComponentUnit extends Backend
{
    
    /**
     * ComponentUnit模型对象
     * @var \app\common\model\ComponentUnit
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\ComponentUnit;

    }

    public function import()
    {
        parent::import();
    }
    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $params['createtime'] = $this->model->getMsectime();
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $info = $this->model->get(array('did'=>$params['did']));
                if(!empty($info)){
                    $this->error('组建单元编号不能重复');
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
                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
//                     $gizwits = new Gizwits;
//                     $data = $gizwits->getMessage($params['did']);
                  
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
        return $this->view->fetch();
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
            
            $examine = new Examine();
            foreach ($list as  $i=>$row){
                try {
                $r = $examine->checkOnLine($row['did']);
                
                if($r){
                    $list[$i]['cstatus'] = 'success';
                }else {
                    $list[$i]['cstatus'] = 'deleted';
                }
                } catch (ValidateException $e) {
                    $list[$i]['cstatus'] = 'deleted';
                } 
            }
            $result = array("total" => $list->total(), "rows" => $list->items());
            
            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 开关
     */
    public function switch($ids = null)
    {
        
        $row = $this->model->get($ids);
        $info = Db::name('sensor_status')->where(array('did'=>$row['did']))->find();
        $did = $row['did'];
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $dinfo = $this->model->get($ids);
            if ($params) {
                $sql = " SELECT mid FROM `iot_member_unit` u LEFT JOIN  `iot_member` m  ON m.id = u.`mid` WHERE u.did = '{$dinfo['did']}' AND m.`member_type` = 'unit' ";
               
                $memberList = $this->model->query($sql);
                $uid = array();
                foreach ($memberList as $i=>$v){
                    $uid[$i] = $v['mid'];
                }
                $count = sizeof($uid);
                if($count==0){
                    $this->error('该组建单元未绑定设备人员');
                }
                $result = false;
                Db::startTrans();
                try {
                    $onoffData = array();
                    $k = 0;
                    $unit = new Unit();
                   for ($i=1;$i<=16;$i++){
                       if($info['onoff'.$i] != $params['onoff'.$i]){
                           $r = $unit->onoff($dinfo['did'], $i, intval($params['onoff'.$i]),$this->model->getMsectime(),'system',0);
                           $k++;
                       }
                     
                   }  
                  
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
                    $this->success();
               
            }
          //  $this->error(__('Parameter %s can not be empty', ''));
        }
   
        $this->view->assign("info", $info);
        $this->view->assign("id", $row['id']);
        return $this->view->fetch();
    }
    

    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
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
                  
                    $glog = Db::name('member_unit')->where(array('did' => $v['did']))->order('id','desc')->limit(0, 1)->select();
                    $glog =  collection($glog)->toArray();
                    if(!empty($glog['id'])){
                        Db::rollback();
                        $this->error('已被绑定，不能删除');
                    }
                    $glog = Db::name('sensor_list')->where(array('did' => $v['did']))->order('id','desc')->limit(0, 1)->select();
                    $glog =  collection($glog)->toArray();
                    if(!empty($glog['id'])){
                        Db::rollback();
                        $this->error('已被绑定，不能删除');
                    }
                    $glog = Db::name('component_unit_log')->where(array('did' => $v['did']))->order('id','desc')->limit(0, 1)->select();
                    $glog =  collection($glog)->toArray();
                    if(!empty($glog['id'])){
                        Db::rollback();
                        $this->error('已存在记录的不能删除');
                    }
                    $glog = Db::name('sensor_log')->where(array('did' => $v['did']))->order('id','desc')->limit(0, 1)->select();
                    $glog =  collection($glog)->toArray();
                    if(!empty($glog['id'])){
                        Db::rollback();
                        $this->error('已存在记录的不能删除');
                    }
                    $count += $v->delete();
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

<?php

namespace app\admin\controller;

use Complex\Exception;
use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class SensorList extends Backend
{
    
    /**
     * SensorList模型对象
     * @var \app\common\model\SensorList
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\SensorList;

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
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id','title','label','did','kind','port','createtime']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
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
                    if(!empty($params['group'])){
                        foreach ($params['group'] as $v){
                            
                            Db::name('sensor_group')->insert(array(
                                'sensorid' => $this->model->id,
                                'groupid' => $v,
                                'createtime' => $this->model->getMsectime()
                            ));
                            
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
       
        
       
        
        $group = new \app\common\model\Group;
        $groups = $group->select();
        
        $groups =  collection($groups)->toArray();
        
        $grouplist = array();
        foreach ($groups as $v){
            $grouplist[$v['groupid']] = $v['title'];
        }
        $ids = [];
        $this->view->assign("ids", $ids);
        $this->view->assign("grouplist", $grouplist);
        $this->view->assign("unitlist", $unitlist);
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
        $memberunits = Db::name('sensor_group')->where(array('sensorid'=>$row['id']))->select();
        
        $memberunits =  collection($memberunits)->toArray();
        $gids = array();
        foreach ($memberunits as $v){
            $gids[] = $v['groupid'];
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
                    $result = $row->allowField(true)->save($params);
                    if( $gids!=$params['group']){
                        Db::name('sensor_group')->where(['sensorid' => $row['id']])->delete();
                        if(!empty($params['group'])){
                        foreach ($params['group'] as $v){
                            
                            Db::name('sensor_group')->insert(array(
                                'sensorid' => $row['id'],
                                'groupid' => $v,
                                'createtime' => $this->model->getMsectime()
                            ));
                            
                        }
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
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
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
        
        
      
        $group = new \app\common\model\Group;
        $groups = $group->select();
        
        $groups =  collection($groups)->toArray();
        
        $grouplist = array();
        foreach ($groups as $v){
            $grouplist[$v['groupid']] = $v['title'];
        }
       
        $this->view->assign("gids", $gids);
        $this->view->assign("grouplist", $grouplist);
        $this->view->assign("unitlist", $unitlist);
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
                  
                    $glog = Db::name('sensor_group')->where(array('sensorid' => $v['id']))->order('id','desc')->limit(0, 1)->select();
                    $glog =  collection($glog)->toArray();
                   
                    if(!empty($glog['groupid'])){
                        Db::rollback();
                        $this->error('已绑定分组的不能删除');
                    }
                    $glog = Db::name('sensor_log')->where(array('sensorid' => $v['id']))->order('id','desc')->limit(0, 1)->select();
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
    
    /**
     * 数据校准
     */
    public function adjust()
    {
        $type = $this->request->get('id');
        $id = $this->request->get('ids');
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
          
           
            if ($params) {
                Db::startTrans();
                try {
                    $sensorAdjustModel = new \app\common\model\SensorAdjust;
                    $unitModel = new \app\common\model\Unit;
                    $sensorModel = new \app\common\model\Sensor;
                    foreach ($params as $k=>$v){
                        $info =  $sensorAdjustModel->get(array('label'=>$k,'sensorid'=>$id));
                        $isChange = 0;
                        if(empty($info)){
                            $result =  $sensorAdjustModel->insert(
                                array(
                                    'sensorid'=>$id,
                                    'label' =>$k,
                                    'change_type' => $v['change_type'],
                                    'change_value' => $v['change_value'],
                                    'updatetime' => $this->model->getMsectime(),
                                    'createtime' => $this->model->getMsectime()
                                ));
                            
                        }else {
                            $result =  $sensorAdjustModel->update(
                                array(
                                    'label' =>$k,
                                    'change_type' => $v['change_type'],
                                    'change_value' => $v['change_value'],
                                    'updatetime' => $this->model->getMsectime(),
                                ),array('label'=>$k,'sensorid'=>$id));
                        }
                        if($v['change_value'] != 0){
                            $isChange = 1;
                        }
                    }
                    if($isChange == 0){
                        $this->model->update(array('isAdjust'=>0),array('id'=>$id));
                    }else{
                        $this->model->update(array('isAdjust'=>1),array('id'=>$id));
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
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        
        $unitSensorModel = new \app\common\model\UnitSensor;
        $list = $unitSensorModel
        ->where(array('sensorid'=>$id))
        ->select();
        $list =  collection($list)->toArray();
       
        $sensorAdjustModel = new \app\common\model\SensorAdjust;
        $adjustlist = $sensorAdjustModel
        ->where(array('sensorid'=>$id))
        ->select();
        $adjustVal = array();
        foreach ($adjustlist as $v){
            $adjustVal[$v['label']]['change_type'] = $v['change_type'];
            $adjustVal[$v['label']]['change_value'] = $v['change_value'];
        }
        foreach ($list as $v){
            if(empty($adjustVal[$v['label']])){
                $adjustVal[$v['label']]['change_type'] = 'value';
                $adjustVal[$v['label']]['change_value'] = 0;
            }
        }
        
        $this->view->assign("adjustVal", $adjustVal);
        $this->view->assign("list", $list);
        return $this->view->fetch();
    }
    
}

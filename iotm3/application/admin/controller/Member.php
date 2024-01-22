<?php

namespace app\admin\controller;

use app\api\controller\Validate;
use app\common\controller\Backend;
use fast\Random;
use PDOException;
use think\Db;
use think\Exception;
use think\exception\ValidateException;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Member extends Backend
{
    
    /**
     * Member模型对象
     * @var \app\common\model\Member
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Member;

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
        $this->relationSearch = false;
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
                $row->visible(['id','username','prevtime','createtime']);
            }
          //  $list1 = collection($list->items())->toArray();
//             foreach ($list1 as $i=>$v){
//                 if(!empty($v['prevtime'])){
                   
//                     $list1[$i]['prevtime_text'] = $this->model->msecdate($v['prevtime']);
//                 }
               
//             }
           //$list->items() = $list1;
            //print_r($list1);
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
                $member = $this->model->get(array('username'=>$params['username']));
                  if(!empty($member)){
                      $this->error('用户名不能重复');
                  }
                    $params['salt'] = Random::alnum();
                    $params['password'] = md5(md5($params['password']) . $params['salt']);
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
                    $memberunit = new \app\common\model\MemberUnit;
                   
                    $result = $this->model->allowField(true)->save($params);
                    if(!empty($params['unit'])){
                        foreach ($params['unit'] as $v){
                            
                            $memberunit->insert(array(
                                'mid' => $this->model->id,
                                'did' => $v,
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
      
        $ids = [];
        $this->view->assign("ids", $ids);
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
        $memberunit = new \app\common\model\MemberUnit;
        $memberunits = $memberunit->where(array('mid'=>$row['id']))->select();
        
        $memberunits =  collection($memberunits)->toArray();
        $dids = array();
        if(!empty($memberunits)){
        foreach ($memberunits as $v){
            $dids[] = $v['did'];
        }
        }else{
            $dids =[];
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
                if($params['username'] != $row['username']){
                $member = $this->model->get(array('username'=>$params['username']));
                if(!empty($member)){
                    $this->error('用户名不能重复');
                }
                }
              
                if (isset($params['password']) && $params['password']!='') {
                    $params['salt'] = Random::alnum();
                    $params['password'] = md5(md5($params['password']) . $params['salt']);
                }else{
                    $params['password'] = $row['password'];
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
                    $result = $row->allowField(true)->save($params);
                    if( $dids!=$params['unit']){
                        $memberunit->where(['mid' => $row['id']])->delete();
                        foreach ($params['unit'] as $v){
                            
                            $memberunit->insert(array(
                                'mid' => $row['id'],
                                'did' => $v,
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
        $this->view->assign("unitlist", $unitlist);
        $this->view->assign("dids", $dids);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
    
}

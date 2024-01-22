<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class SensorStatus extends Backend
{
    
    /**
     * SensorStatus模型对象
     * @var \app\common\model\SensorStatus
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\SensorStatus;
        $this->view->assign("onoff1List", $this->model->getOnoff1List());
        $this->view->assign("onoff2List", $this->model->getOnoff2List());
        $this->view->assign("onoff3List", $this->model->getOnoff3List());
        $this->view->assign("onoff4List", $this->model->getOnoff4List());
        $this->view->assign("onoff5List", $this->model->getOnoff5List());
        $this->view->assign("onoff6List", $this->model->getOnoff6List());
        $this->view->assign("onoff7List", $this->model->getOnoff7List());
        $this->view->assign("onoff8List", $this->model->getOnoff8List());
        $this->view->assign("onoff9List", $this->model->getOnoff9List());
        $this->view->assign("onoff10List", $this->model->getOnoff10List());
        $this->view->assign("onoff11List", $this->model->getOnoff11List());
        $this->view->assign("onoff12List", $this->model->getOnoff12List());
        $this->view->assign("onoff13List", $this->model->getOnoff13List());
        $this->view->assign("onoff14List", $this->model->getOnoff14List());
        $this->view->assign("onoff15List", $this->model->getOnoff15List());
        $this->view->assign("onoff16List", $this->model->getOnoff16List());
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
                    ->with(['componentunit'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                $row->getRelation('componentunit')->visible(['title']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

}

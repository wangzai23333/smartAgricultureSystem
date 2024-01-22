<?php

namespace app\common\model;

use think\Db;
use think\Model;


class Linkage extends Model
{

    

    

    // 表名
    protected $name = 'linkage';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'onoff_text',
        'createtime_text',
        'reference_text',
        'min_text',
        'max_text'
    ];
    
    public function getCreatetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['createtime']) ? $data['createtime'] : '');
        return is_numeric($value) ? $this->msecdate($value) : $value;
    }
    
    public function getReferenceTextAttr($value, $data)
    {
        $data = Db::name('range')->where(array('id'=>$data['referenceid']))->select();
        switch ($data[0]['rtype']){
            case '0':
                $info = '为空';
                break;
            case '1':
                $info = '值';
                break;
            case '2':
                $info = '组建单元某值';
                break;
            case '3':
                $info = '组别';
                break;
            case '4':
                $info = '离线';
                break;
        }
        return $info;
    }
    
    public function getMinTextAttr($value, $data)
    {
        $data = Db::name('range')->where(array('id'=>$data['minid']))->select();
        switch ($data[0]['rtype']){
            case '0':
                $info = '为空';
                break;
            case '1':
                $info = '值';
                break;
            case '2':
                $info = '组建单元某值';
                break;
            case '3':
                $info = '组别';
                break;
            case '4':
                $info = '离线';
                break;
        }
        return $info;
    }
    
    public function getMaxTextAttr($value, $data)
    {
        $data = Db::name('range')->where(array('id'=>$data['maxid']))->select();
        switch ($data[0]['rtype']){
            case '0':
                $info = '为空';
                break;
            case '1':
                $info = '值';
                break;
            case '2':
                $info = '组建单元某值';
                break;
            case '3':
                $info = '组别';
                break;
            case '4':
                $info = '离线';
                break;
        }
        return $info;
    }
    
    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getOnoffList()
    {
        return ['on' => __('Onoff on'), 'off' => __('Onoff off')];
    }


    public function getOnoffTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['onoff']) ? $data['onoff'] : '');
        $list = $this->getOnoffList();
        return isset($list[$value]) ? $list[$value] : '';
    }


   

    public function range()
    {
        return $this->belongsTo('Range', 'referenceid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}

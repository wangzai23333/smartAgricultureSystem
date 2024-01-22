<?php

namespace app\common\model;

use think\Db;
use think\Model;


class Notice extends Model
{

    

    

    // 表名
    protected $name = 'notice';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'createtime_text',
        'reference_text',
        'min_text',
        'max_text'
    ];
    
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
    
    public function getCreatetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['createtime']) ? $data['createtime'] : '');
        return is_numeric($value) ? $this->msecdate($value) : $value;
    }
    
    public function range()
    {
        return $this->belongsTo('Range', 'referenceid', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    


}

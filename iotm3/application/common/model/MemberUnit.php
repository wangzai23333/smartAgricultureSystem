<?php

namespace app\common\model;

use think\Model;


class MemberUnit extends Model
{

    

    

    // 表名
    protected $name = 'member_unit';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    

    public function  getMyUnit($mid){
        $myunit = $this
        ->where(array('mid'=>$mid))
        ->order('id','desc')
        ->select();
        $myunit = collection($myunit)->toArray();
        
        $dids = array();
        if(!empty($myunit)){
            foreach ($myunit as $v){
                $dids[] = $v['did'];
            }
            return $dids;
        }else{
           return false;
        }
    }





    public function member()
    {
        return $this->belongsTo('Member', 'mid', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function componentunit()
    {
        return $this->belongsTo('ComponentUnit', 'did', 'did', [], 'LEFT')->setEagerlyType(0);
    }
}

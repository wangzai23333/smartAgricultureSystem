<?php
namespace app\common\controller;



class ChangeTime
{
    /* 
     * 毫秒时间戳转换成日期 
     * 
     * */
    function msecdate($time)
    {
        $tag='Y-m-d H:i:s';
        $a = substr($time,0,10);
        $b = substr($time,10);
        $date = date($tag,$a).'.'.$b;
        return $date;
    }
    
    /*
     * 毫秒时间戳转换成按格式日期 
     *
     * */
    function msecOwnDate($time,$tag='')
    { 
        $a = substr($time,0,10);
        $b = substr($time,10);
        $date = date($tag,$a);
        return $date;
    }
    
    
    
    //返回当前的毫秒时间戳
    function getMsectime() {
        list($msec, $sec) = explode(' ', microtime());
        $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
        
    }
    /** 
     * 时间日期转时间戳格式，精确到毫秒，
     *
     */
    function getDataFormat($time)
    {
        list($usec, $sec) = explode(".", $time);
        $date = strtotime($usec);
        $return_data = str_pad($date.$sec,13,"0",STR_PAD_RIGHT); //不足13位。右边补0
        return $return_data;
    }
    
}

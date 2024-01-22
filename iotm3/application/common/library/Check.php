<?php
namespace app\common\library;

class Check
{
    protected static $key = "e8f90e48a305d12340bbaeef543b1b3f";
    
    /**
     * 生成验证密钥
     *
     * @param array $params 请求参数
     * @param string $key 密钥
     * @return string
     */
    public static function generateVerificationKey($params, $key="")
    {
        $param = '';
        ksort($params);
        foreach ($params as $k => $value) {
            if ($k == 'token') {
                continue;
            }
            $param .= "$k=$value&";
        }
        if($key == ""){
            $key = self::$key;
        }
        $param .= "token=$key";
        return md5($param);
    }
}
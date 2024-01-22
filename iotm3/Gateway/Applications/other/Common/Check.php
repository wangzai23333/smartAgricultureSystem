<?php
namespace Common;

class Check
{
    /**
     * 生成验证密钥
     *
     * @param array $array
     * @param string $token
     * @return string
     */
    public static function generateVerificationKey($array, $token)
    {
        $param = '';
        ksort($array);
        foreach ($array as $k => $value) {
            if ($k == 'token') {
                continue;
            }
            $param .= "$k=$value&";
        }
        $param .= "token=$token";
        return md5($param);
    }
}
<?php

namespace think;

use think\facade\Config;
use think\auth\controller\Auth AS Controller;

class Auth
{
    /**
     * 静态魔术方法
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $model = new Controller();

        return call_user_func_array([$model, $method], $args);
    }
}
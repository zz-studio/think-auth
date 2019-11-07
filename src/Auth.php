<?php
/**
 * +----------------------------------------------------------------------
 * | Zz-Admin
 * +----------------------------------------------------------------------
 *  .--,       .--,             | FILE: Controller.php
 * ( (  \.---./  ) )            | AUTHOR: byron sampson
 *  '.__/o   o\__.'             | EMAIL: xiaobo.sun@qq.com
 *     {=  ^  =}                | QQ: 150093589
 *      >  -  <                 | WECHAT: wx5ini99
 *     /       \                | DATETIME: 2018/6/20
 *    //       \\               |
 *   //|   .   |\\              |
 *   "'\       /'"_.-~^`'-.     |
 *      \  _  /--'         `    |
 *    ___)( )(___               |-----------------------------------------
 *   (((__) (__)))              | 高山仰止,景行行止.虽不能至,心向往之。
 * +----------------------------------------------------------------------
 * | Copyright (c) 2017 http://www.zzstudio.net All rights reserved.
 * +----------------------------------------------------------------------
 */
namespace think;

/**
 * auth 权限检测入口
 * Class Auth
 * @package think
 * @method static check($name, $uid, $type = 1, $mode = 'url', $relation = 'or')
 * @method static rules($uid, $type = 1)
 * @method static roles($uid)
 */
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
        $model = new \think\auth\service\Auth(app());

        return call_user_func_array([$model, $method], $args);
    }
}

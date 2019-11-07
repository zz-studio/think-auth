<?php
/**
 * +----------------------------------------------------------------------
 * | think-auth [thinkphp6]
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
declare(strict_types=1);

namespace think\auth\model;

use think\Model;
use think\facade\Db;

/**
 * 权限角色
 * Class Role
 * @package think\auth\model
 */
class Role extends Model
{
    // 表名
    protected $name = "AuthRole";

    /**
     * 模型初始化
     */
    public static function init()
    {
        // 编辑前事件
        self::event('before_update', function ($role) {
            return RoleRule::where(['role_id'=>$role->id])->delete();
        });
    }

    /**
     * 标准化状态值
     * @param $val
     * @return int
     */
    protected function setStatusAttr($val)
    {
        switch ($val) {
            case 'on':
            case 'true':
            case '1':
            case 1:
                $val = 1;
                break;
            default:
                $val = 0;
        }
        return $val;
    }

    /**
     * 用户数
     * @return float|int|string
     * @throws \think\Exception
     */
    protected function getUserNumAttr()
    {
        $role_id = $this->getData('id');
        return RoleUser::where(['role_id'=>$role_id])->count();
    }

    /**
     * 角色对应权限规则
     * @return \think\model\relation\HasMany
     */
    public function rules()
    {
        return $this->hasMany('RoleRule', 'role_id', 'id');
    }
}

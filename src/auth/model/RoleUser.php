<?php
/**
 * +----------------------------------------------------------------------
 * | Rebate Admin
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
namespace think\auth\model;

use think\Model;

/**
 * 权限组与用户关系
 * Class RoleUser
 * @package think\auth\model
 */
class RoleUser extends Model
{
    // 表名
    protected $name = "AuthRoleUser";
    /**
     * 数据表主键 复合主键使用数组定义
     * @var string|array
     */
    protected $pk = 'user_id';

    /**
     * 用户角色列表
     * @return \think\model\relation\HasMany
     */
    public function rules()
    {
        return $this->hasMany(RoleRule::class, 'role_id', 'role_id');
    }
}

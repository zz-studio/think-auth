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
 * 权限组与规则关系
 * Class RoleRule
 * @package think\auth\model
 */
class RoleRule extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = "AuthRoleRule";
    /**
     * 数据表主键 复合主键使用数组定义
     * @var string|array
     */
    protected $pk = 'role_id';
    /**
     * 追加一对一字段
     * @var array
     */
    protected $append = ['rules'];

    /**
     * 角色规则表列
     * @return $this
     */
    public function rules()
    {
        return $this->hasOne(Rule::class, 'id', 'rule_id')->bind(['name', 'title', 'type', 'condition', 'status']);
    }
}

# think-auth
The ThinkPHP5.1 Auth Package

## 安装
> composer require zzstudio/think-auth

## 配置
### 公共配置
```
// auth配置
'auth'  => [
    'auth_on'           => 1, // 权限开关
    'auth_type'         => 1, // 认证方式，1为实时认证；2为登录认证。
    'auth_user'         => 'member', // 用户信息不带前缀表名
],
```

### 导入数据表
> `think_` 为自定义的数据表前缀

```
------------------------------
-- think_auth_rule，规则表，
-- id:主键，name：规则唯一标识, title：规则中文名称 status 状态：为1正常，为0禁用，condition：规则表达式，为空表示存在就验证，不为空表示按照条件验证
------------------------------
DROP TABLE IF EXISTS `think_auth_rule`;
CREATE TABLE `think_auth_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `title` char(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `condition` char(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='权限路由表';
------------------------------
-- think_auth_role 用户组表， 
-- id：主键， title:用户组中文名称，status 状态：为1正常，为0禁用
------------------------------
DROP TABLE IF EXISTS `think_auth_role`;
CREATE TABLE `think_auth_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='权限组';

------------------------------
-- think_auth_role_user 用户与组关系表
-- user_id: 用户id，role_id：用户组id
------------------------------
DROP TABLE IF EXISTS `think_auth_role_user`;
CREATE TABLE `think_auth_role_user` (
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`user_id`,`role_id`),
  KEY `uid` (`user_id`),
  KEY `group_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户及用户组对应表';

------------------------------
-- think_auth_role_rule 用户与组关系表
-- rule_id: 规则id，role_id：用户组id
------------------------------
DROP TABLE IF EXISTS `think_auth_role_rule`;
CREATE TABLE `think_auth_role_rule` (
  `rule_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`rule_id`,`role_id`),
  UNIQUE KEY `uid_group_id` (`rule_id`,`role_id`),
  KEY `uid` (`rule_id`),
  KEY `group_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='权限规则与用户组对应表';
```

## 原理
Auth权限认证是按规则进行认证。
在数据库中我们有 

- 规则表（think_auth_rule） 
- 用户组表(think_auth_role) 
- 用户与组关系表（think_auth_role_user）
- 权限规则与组关系表（think_auth_role_rule）

我们在规则表中定义权限规则， 在用户组表中定义用户与组的关系，在权限规则与组的关系表中定义权限组所拥有的权限。 

下面举例说明：

我们要判断用户是否有显示一个操作按钮的权限， 首先定义一个规则， 在规则表中添加一个名为 show_button 的规则。 然后在用户组表添加一个用户组，定义这个用户组有show_button 的权限规则， 然后在用户与组关系表中定义 UID 为1 的用户 属于刚才这个的这个用户组。 

## 使用
判断权限方法
```
// 引入类库
use think\Auth;

// 检测权限
if(Auth::check('show_button', 1)){// 第一个参数是规则名称,第二个参数是用户UID
	//有显示操作按钮的权限
}else{
	//没有显示操作按钮的权限
}
```
或通过全局函数进行判断
```
if(auth_check('show_button', 1)){// 第一个参数是规则名称,第二个参数是用户UID
	//有显示操作按钮的权限
}else{
	//没有显示操作按钮的权限
}
```

Auth类也可以对节点进行认证，我们只要将规则名称，定义为节点名称就行了。 
可以在公共控制器方法或中间件中进行验证了，以下为中间件的示例

可以通过命令行快速生成权限认证中间件
```
php think make:middleware Auth
```
这个指令会 `application/http/middleware` 目录下面生成一个 Auth 中间件。
```
<?php
namespace app\http\middleware;

use think\exception\HttpResponseException;
use think\Auth AS AuthHandle;
use traits\controller\Jump;

class Auth
{
    use Jump;

    /**
     * 授权业务处理
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // 白名单
        $allow = ['user/login'];

        $rule = strtolower("{$request->controller()}/{$request->action()}");
        
        // 初始化 user_id
        $user_id = is_login();

        // 权限检查
        $check = AuthHandle::check($rule, $user_id);
        if (false === $check) {
            $this->error('[403] 未授权访问');
        }

        return $next($request);
    }

}
```
这时候我们可以在数据库中添加的节点规则， 格式为： “控制器名称/方法名称”

Auth 类 还可以多个规则一起认证 如： 
```
$auth->check('rule1,rule2',uid); 
```
表示 认证用户只要有rule1的权限或rule2的权限，只要有一个规则的权限，认证返回结果就为true 即认证通过。 默认多个权限的关系是 “or” 关系，也就是说多个权限中，只要有个权限通过则通过。 我们也可以定义为 “and” 关系
```
$auth->check('rule1,rule2',uid,'and'); 
```
第三个参数指定为"and" 表示多个规则以and关系进行认证， 这时候多个规则同时通过认证才有权限。只要一个规则没有权限则就会返回false。

Auth认证，一个用户可以属于多个用户组。 比如我们对 show_button这个规则进行认证， 用户A 同时属于 用户组1 和用户组2 两个用户组 ， 用户组1 没有show_button 规则权限， 但如果用户组2 有show_button 规则权限，则一样会权限认证通过。 

Auth类还可以按用户属性进行判断权限， 比如
按照用户积分进行判断， 假设我们的用户表 (think_members) 有字段 score 记录了用户积分。 
我在规则表添加规则时，定义规则表的condition 字段，condition字段是规则条件，默认为空 表示没有附加条件，用户组中只有规则 就通过认证。
如果定义了 condition字段，用户组中有规则不一定能通过认证，程序还会判断是否满足附加条件。
比如我们添加几条规则： 

> `name`字段：grade1 `condition`字段：{score}<100 <br/>
> `name`字段：grade2 `condition`字段：{score}>100 and {score}<200<br/>
> `name`字段：grade3 `condition`字段：{score}>200 and {score}<300

这里 `{score}` 表示 `think_members` 表 中字段 `score` 的值。 

那么这时候 

> $auth->check('grade1', uid) 是判断用户积分是不是0-100<br/>
> $auth->check('grade2', uid) 判断用户积分是不是在100-200<br/>
> $auth->check('grade3', uid) 判断用户积分是不是在200-300

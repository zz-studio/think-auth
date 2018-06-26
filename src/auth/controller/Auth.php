<?php

namespace think\auth\controller;

use think\Container;
use think\facade\Config;
use think\auth\model\RoleUser;

class Auth
{
    /**
     * 当前 app 对象
     * @var object
     */
    protected $app;
    /**
     * 当前用户模型
     * @var
     */
    protected $user;
    /**
     * 默认配置
     * @var array
     */
    protected $config = [
        'auth_on' => 1, // 权限开关
        'auth_type' => 1, // 认证方式，1为实时认证；2为登录认证。
        'auth_user' => 'member', // 用户信息表
    ];

    /**
     * 类架构函数
     * Auth constructor.
     */
    public function __construct(App $app = null)
    {
        $this->app = $app ?: Container::get('app');
        $this->request = $this->app['request'];
        //可设置配置项 auth, 此配置项为数组。
        if ($auth = Config::pull('auth')) {
            $this->config = array_merge($this->config, $auth);
        }
        // 初始化用户模型
        $this->user = $this->app->model($this->config['auth_user']);
    }

    /**
     * 检查权限
     * @param $name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
     * @param $uid  int           认证用户的id
     * @param int $type 认证类型
     * @param string $mode 执行check的模式
     * @param string $relation 如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
     * @return bool               通过验证返回true;失败返回false
     */
    public function check($name, $uid, $type = 1, $mode = 'url', $relation = 'or')
    {
        if (!$this->config['auth_on']) {
            return true;
        }
        // 获取用户需要验证的所有有效规则列表
        $authList = $this->getAuthList($uid, $type);
        if (is_string($name)) {
            $name = explode(',', strtolower($name));
        }
        $list = []; //保存验证通过的规则名
        if ('url' == $mode) {
            $REQUEST = unserialize(strtolower(serialize($this->request->param())));
        }

        foreach ($authList as $auth) {
            $query = preg_replace('/^.+\?/U', '', $auth);
            if ('url' == $mode && $query != $auth) {
                parse_str($query, $param); //解析规则中的param
                $intersect = array_intersect_assoc($REQUEST, $param);
                $auth = preg_replace('/\?.*$/U', '', $auth);
                if (in_array($auth, $name) && $intersect == $param) {
                    //如果节点相符且url参数满足
                    $list[] = $auth;
                }
            } else {
                if (in_array($auth, $name)) {
                    $list[] = $auth;
                }
            }
        }
        if ('or' == $relation && !empty($list)) {
            return true;
        }
        $diff = array_diff($name, $list);
        if ('and' == $relation && empty($diff)) {
            return true;
        }

        return false;
    }

    /**
     * 获得权限列表
     * @param integer $uid 用户id
     * @param integer $type
     * @return array
     */
    protected function getAuthList($uid, $type)
    {
        static $_authList = []; //保存用户验证通过的权限列表
        $t = implode(',', (array)$type);

        if (isset($_authList[$uid . $t])) {
            return $_authList[$uid . $t];
        }

        if (2 == $this->config['auth_type'] && Session::has('_auth_list_' . $uid . $t)) {
            return Session::get('_auth_list_' . $uid . $t);
        }

        $roles = $this->app->model(RoleUser::class)->with(['rules'])->where(['user_id' => $uid])->select();
        if (empty($roles)) {
            $_authList[$uid . $t] = [];
            return [];
        }

        $pk = $this->user->getPk();
        $user = $this->user->where([$pk => $uid])->column('*');
        if (is_array($user) && isset($user[$uid])) {
            $user = $user[$uid];
        } else {
            $user = [];
        }
        //循环规则，判断结果。
        $authList = [];
        foreach ($roles as $role) {
            foreach ($role->rules as $rule) {
                $rule = $rule->toArray();
                // 当规则为空或状态不为1时跳过
                if (empty($rule) || $rule['status'] != '1') {
                    continue;
                }
                if (!empty($rule['condition'])) {
                    //根据condition进行验证
                    $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                    //dump($command); //debug
                    @(eval('$condition=(' . $command . ');'));
                    if ($condition) {
                        $authList[] = strtolower($rule['name']);
                    }
                } else {
                    //只要存在就记录
                    $authList[] = strtolower($rule['name']);
                }
            }
        }
        $_authList[$uid . $t] = $authList;
        if (2 == $this->config['auth_type']) {
            //规则列表结果保存到session
            Session::set('_auth_list_' . $uid . $t, $authList);
        }

        return array_unique($authList);
    }
}
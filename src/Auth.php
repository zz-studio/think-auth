<?php

namespace think;

use think\auth\model\RoleUser;

class Auth
{
    /**
     * @var object 对象实例
     */
    protected static $instance;
    /**
     * 当前 app 对象
     * @var object
     */
    protected $app;

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
        if ($auth = Config::get('auth.')) {
            $this->config = array_merge($this->config, $auth);
        }
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
        $authlist = $this->getAuthList($uid, $type);
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

        $roles = $this->app->model(RoleUser::class)->where(['user_id' => $uid])->select();

        dump($roles);
    }

    /**
     * 静态魔术方法
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $model = new static();

        return call_user_func_array([$model, $method], $args);
    }
}
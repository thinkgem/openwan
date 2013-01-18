<?php
// $Id: myapp.php 883 2010-03-23 01:44:52Z thinkgem $

/**
 * MyApp 封装了应用程序的基本启动流程和初始化操作，并为应用程序提供一些公共服务。
 *
 * 主要完成下列任务：
 * - 初始化运行环境
 * - 提供应用程序入口
 * - 为应用程序提供公共服务
 * - 处理访问控制和用户信息在 session 中的存储
 */
class MyApp
{
    /**
     * 应用程序的基本设置
     *
     * @var array
     */
    protected $_app_config;

    /**
     * 构造函数
     *
     * @param array $app_config
     *
     * 构造应用程序对象
     */
    protected function __construct(array $app_config)
    {
        // #IFDEF DEBUG
        global $g_boot_time;
        QLog::log('--- STARTUP TIME --- ' . $g_boot_time, QLog::DEBUG);
        // #ENDIF

        /**
         * 初始化运行环境
         */
        // 禁止 magic quotes
        set_magic_quotes_runtime(0);

        // 处理被 magic quotes 自动转义过的数据
        if (get_magic_quotes_gpc())
        {
            $in = array(& $_GET, & $_POST, & $_COOKIE, & $_REQUEST);
            while (list ($k, $v) = each($in))
            {
                foreach ($v as $key => $val)
                {
                    if (! is_array($val))
                    {
                        $in[$k][$key] = stripslashes($val);
                        continue;
                    }
                    $in[] = & $in[$k][$key];
                }
            }
            unset($in);
        }

        // 设置异常处理函数
        set_exception_handler(array($this, 'exception_handler'));

        // 初始化应用程序设置
        $this->_app_config = $app_config;
        $this->_initConfig();
        Q::replaceIni('app_config', $app_config);

        // 设置默认的时区
        date_default_timezone_set(Q::ini('l10n_default_timezone'));

        // 设置 session 服务
        if (Q::ini('runtime_session_provider'))
        {
            Q::loadClass(Q::ini('runtime_session_provider'));
        }

        // 打开 session
        if (Q::ini('runtime_session_start'))
        {
            session_start();
            // #IFDEF DEBUG
            QLog::log('session_start()', QLog::DEBUG);
            QLog::log('session_id: ' . session_id(), QLog::DEBUG);
            // #ENDIF
        }

        // 导入类搜索路径
        Q::import($app_config['APP_DIR']);
        Q::import($app_config['APP_DIR'] . '/model');
        Q::import($app_config['MODULE_DIR']);

        // 注册应用程序对象
        Q::register($this, 'app');
    }

    /**
     * 析构函数
     */
    function __destruct()
    {
        // #IFDEF DBEUG
        global $g_boot_time;
        $shutdown_time = microtime(true);
        $length = $shutdown_time - $g_boot_time;
        QLog::log("--- SHUTDOWN TIME --- {$shutdown_time} ({$length})sec", QLog::DEBUG);
        // #ENDIF
    }

    /**
     * 返回应用程序类的唯一实例
     *
     * @param array $app_config
     *
     * @return MyApp
     */
    static function instance(array $app_config = null)
    {
        static $instance;
        if (is_null($instance))
        {
            if (empty($app_config))
            {
                die('INVALID CONSTRUCT APP');
            }
            $instance = new MyApp($app_config);
        }
        return $instance;
    }

    /**
     * 返回应用程序基础配置的内容
     *
     * 如果没有提供 $item 参数，则返回所有配置的内容
     *
     * @param string $item
     *
     * @return mixed
     */
    function config($item = null)
    {
        if ($item)
        {
            return isset($this->_app_config[$item]) ? $this->_app_config[$item] : null;
        }
        return $this->_app_config;
    }

    /**
     * 根据运行时上下文对象，调用相应的控制器动作方法
     *
     * @param array $args
     *
     * @return mixed
     */
    function dispatching(array $args = array())
    {
        // 构造运行时上下文对象
        $context = QContext::instance();

        // 获得请求对应的 UDI（统一目的地信息）
        $udi = $context->requestUDI('array');
        #IFDEF DEBUG
        QLog::log('REQUEST UDI: ' . $context->UDIString($udi), QLog::DEBUG);
        #ENDIF

        // 检查是否有权限访问
        if (!$this->authorizedUDI($this->currentUserRoles(), $udi))
        {
            // 拒绝访问
            $response = $this->_on_access_denied();
        }
        else
        {
            // 确定控制器的类名称
            // 控制器类名称 = 模块名_Controller_名字空间_控制器名
            $module_name = $udi[QContext::UDI_MODULE];
            if ($module_name != QContext::UDI_DEFAULT_MODULE && $module_name)
            {
                $dir = "{$this->_app_config['MODULE_DIR']}/{$module_name}/controller";
                $class_name = "{$module_name}_controller_";
            }
            else
            {
                $dir = "{$this->_app_config['APP_DIR']}/controller";
                $class_name = 'controller_';
            }

            $namespace = $udi[QContext::UDI_NAMESPACE];
            if ($namespace != QContext::UDI_DEFAULT_NAMESPACE && $namespace)
            {
                $class_name .= "{$namespace}_";
                $dir .= "/{$namespace}";
            }
            $controller_name = $udi[QContext::UDI_CONTROLLER];
            $class_name .= $controller_name;
            $filename = "{$controller_name}_controller.php";

            do
            {
                // 载入控制器文件
                try
                {
                    if (!class_exists($class_name, false))
                    {
                        Q::loadClassFile($filename, array($dir), $class_name);
                    }
                }
                catch (Q_ClassNotDefinedException $ex)
                {
                    $response = $this->_on_action_not_defined();
                    break;
                }
                catch (Q_FileNotFoundException $ex)
                {
                    $response = $this->_on_action_not_defined();
                    break;
                }

                // 构造控制器对象
                $controller = new $class_name($this);
                $action_name = $udi[QContext::UDI_ACTION];
                if ($controller->existsAction($action_name))
                {
                    // 如果指定动作存在，则调用
                    $response = $controller->execute($action_name, $args);
                }
                else
                {
                    // 如果指定动作不存在，则尝试调用控制器的 _on_action_not_defined() 函数处理错误
                    $response = $controller->_on_action_not_defined($action_name);
                    if (is_null($response))
                    {
                        // 如果控制器的 _on_action_not_defined() 函数没有返回处理结果
                        // 则由应用程序对象的 _on_action_not_defined() 函数处理
                        $response = $this->_on_action_not_defined();
                    }
                }
            } while (false);
        }

        if (is_object($response) && method_exists($response, 'execute'))
        {
            // 如果返回结果是一个对象，并且该对象有 execute() 方法，则调用
            $response = $response->execute();
        }
        elseif ($response instanceof QController_Forward)
        {
            // 如果是一个 QController_Forward 对象，则将请求进行转发
            $response = $this->dispatching($response->args);
        }

        // 其他情况则返回执行结果
        return $response;
    }

    /**
     * 将用户数据保存到 session 中
     *
     * @param mixed $user
     * @param mixed $roles
     */
    function changeCurrentUser($user, $roles)
    {
        $user['roles'] = implode(',', Q::normalize($roles));
        $_SESSION[Q::ini('acl_session_key')] = $user;
    }

    /**
     * 获取保存在 session 中的用户数据
     *
     * @return array
     */
    function currentUser()
    {
        $key = Q::ini('acl_session_key');
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * 获取 session 中用户信息包含的角色
     *
     * @return array
     */
    function currentUserRoles()
    {
        $user = $this->currentUser();
        return isset($user['roles']) ? Q::normalize($user['roles']) : array();
    }

    /**
     * 从 session 中清除用户数据
     */
    function cleanCurrentUser()
    {
        unset($_SESSION[Q::ini('acl_session_key')]);
    }

    /**
     * 检查指定角色是否有权限访问特定的控制器和动作
     *
     * @param array $roles
     * @param string|array $udi
     *
     * @return boolean
     */
    function authorizedUDI($roles, $udi)
    {
        /**
         * 将 UDI 封装为一个资源
         * 读取控制器的 ACL（访问控制列表）
         * 通过 QACL 组件进行权限检查
         */
        $roles = Q::normalize($roles);
        $udi = QContext::instance()->normalizeUDI($udi);
        $controller_acl = $this->controllerACL($udi);

        // 首先检查动作 ACT
        $acl = Q::singleton('QACL');
        $action_name = strtolower($udi[QContext::UDI_ACTION]);
        if (isset($controller_acl['actions'][$action_name]))
        {
            // 如果动作的 ACT 检验通过，则忽略控制器的 ACT
            return $acl->rolesBasedCheck($roles, $controller_acl['actions'][$action_name]);
        }

        if (isset($controller_acl['actions'][QACL::ALL_ACTIONS]))
        {
            // 如果为所有动作指定了默认 ACT，则使用该 ACT 进行检查
            return $acl->rolesBasedCheck($roles, $controller_acl['actions'][QACL::ALL_ACTIONS]);
        }

        // 否则检查是否可以访问指定控制器
        return $acl->rolesBasedCheck($roles, $controller_acl);
    }

    /**
     * 获得指定控制器的 ACL
     *
     * @param string|array $udi
     *
     * @return array
     */
    function controllerACL($udi)
    {
        if (!is_array($udi))
        {
            $udi = QContext::instance()->normalizeUDI($udi);
        }

        $path = 'acl_global';
        if ($udi[QContext::UDI_MODULE] && $udi[QContext::UDI_MODULE] != QContext::UDI_DEFAULT_MODULE)
        {
            $path .= '/' . $udi[QContext::UDI_MODULE];
        }
        if ($udi[QContext::UDI_NAMESPACE] && $udi[QContext::UDI_NAMESPACE] != QContext::UDI_DEFAULT_NAMESPACE)
        {
            $path .= '/' . $udi[QContext::UDI_NAMESPACE];
        }
        $acl = Q::ini($path);

        if (!is_array($acl))
        {
            return Q::ini('acl_default');
        }

        $acl = array_change_key_case($acl, CASE_LOWER);

        if (isset($acl[$udi[QContext::UDI_CONTROLLER]]))
        {
            return (array)$acl[$udi[QContext::UDI_CONTROLLER]];
        }

        return isset($acl[QACL::ALL_CONTROLLERS]) ? (array)$acl[QACL::ALL_CONTROLLERS] : Q::ini('acl_default');
    }

    /**
     * 载入配置文件内容
     *
     * @param array $app_config
     *
     * @return array
     */
    static function loadConfigFiles(array $app_config)
    {
        $ext = !empty($app_config['CONFIG_FILE_EXTNAME'])
               ? $app_config['CONFIG_FILE_EXTNAME']
               : 'yaml';
        $cfg = $app_config['CONFIG_DIR'];
        $run_mode = strtolower($app_config['RUN_MODE']);

        $files = array
        (
            "{$cfg}/environment.{$ext}"               => 'global',
            "{$cfg}/database.{$ext}"                  => 'db_dsn_pool',
            "{$cfg}/acl.{$ext}"                       => 'acl_global',
            "{$cfg}/environments/{$run_mode}.{$ext}"  => 'global',
            "{$cfg}/app.{$ext}"                       => 'appini',
            "{$cfg}/routes.{$ext}"                    => 'routes',
        );

        $replace = array();
        foreach ($app_config as $key => $value)
        {
            if (!is_array($value)) $replace["%{$key}%"] = $value;
        }

        $config = require(Q_DIR . '/_config/default_config.php');
        foreach ($files as $filename => $scope)
        {
            if (!file_exists($filename)) continue;
            $contents = Helper_YAML::load($filename, $replace);
            if ($scope == 'global')
            {
                $config = array_merge($config, $contents);
            }
            else
            {
                if (!isset($config[$scope]))
                {
                    $config[$scope] = array();
                }
                $config[$scope] = array_merge($config[$scope], $contents);
            }
        }

        if (!empty($config['db_dsn_pool'][$run_mode]))
        {
            $config['db_dsn_pool']['default'] = $config['db_dsn_pool'][$run_mode];
        }

        return $config;
    }

    /**
     * 初始化应用程序设置
     */
    protected function _initConfig()
    {
        #IFDEF DEBUG
        QLog::log(__METHOD__, QLog::DEBUG);
        #ENDIF

        // 载入配置文件
        if ($this->_app_config['CONFIG_CACHED'])
        {
            /**
             * 从缓存载入配置文件内容
             */

            // 构造缓存服务对象
            $backend = $this->_app_config['CONFIG_CACHE_BACKEND'];
            $settings = isset($this->_app_config['CONFIG_CACHE_SETTINGS'][$backend]) ? $this->_app_config['CONFIG_CACHE_SETTINGS'][$backend] : null;
            $cache = new $backend($settings);

            // 载入缓存内容
            $cache_id = $this->_app_config['APPID'] . '_app_config';
            $config = $cache->get($cache_id);

            if (!empty($config))
            {
                Q::replaceIni($config);
                return;
            }
        }

        // 没有使用缓存，或缓存数据失效
        $config = self::loadConfigFiles($this->_app_config);
        if ($this->_app_config['CONFIG_CACHED'])
        {
            $cache->set($cache_id, $config);
        }

        Q::replaceIni($config);
    }

	/**
	 * 访问被拒绝时的错误处理函数
	 */
	protected function _on_access_denied()
    {
        $filename = str_replace(array('/', '\\'), '/', substr(__FILE__, strlen($this->_app_config['ROOT_DIR']) + 1));
        $message = "修改文件 \"{$filename}\" 中的 _on_access_denied() 方法可以定制拒绝访问错误的处理方式";
        require($this->_app_config['APP_DIR'] . '/view/403.php');
	}

	/**
	 * 视图调用未定义的控制器或动作时的错误处理函数
	 */
	protected function _on_action_not_defined()
    {
        $filename = str_replace(array('/', '\\'), '/', substr(__FILE__, strlen($this->_app_config['ROOT_DIR']) + 1));
        $message = "修改文件 \"{$filename}\" 中的 _on_action_not_defined() 方法可以定制页面未找到错误的处理方式";
		require($this->_app_config['APP_DIR'] . '/view/404.php');
	}

	/**
	 * 默认的异常处理
	 */
	function exception_handler(Exception $ex)
	{
        QException::dump($ex);

        dump('如果要改变对异常的处理，请修改文件 "' . __FILE__ . '" 的 exception_handler() 方法');
	}
}


/**
 * MyAppException 封装应用程序运行过程中产生的异常
 *
 * @package app
 */
class MyAppException extends QException
{

}


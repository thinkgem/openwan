<?php
// $Id: context.php 2679 2010-01-08 13:27:40Z dualface $

/**
 * 定义 QContext 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: context.php 2679 2010-01-08 13:27:40Z dualface $
 * @package core
 */

/**
 * QContext 封装了运行时上下文
 *
 * 所谓运行时上下文是指 QeePHP 应用程序的运行环境本身。
 * QContext 主要封装了请求参数和请求状态，以及 URL 解析等功能。
 *
 * QContext 使用了单子设计模式，因此只能使用 QContext::instance() 来获得
 * QContext 对象的唯一实例。
 *
 * QContext 实现了 ArrayAccess 接口，可以将 QContext 对象当作数组一样使用。
 *
 * @code php
 * if (isset($context['title']))
 * {
 *     echo $context['title'];
 * }
 * @endcode
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: context.php 2679 2010-01-08 13:27:40Z dualface $
 * @package core
 */
class QContext implements ArrayAccess
{
    /**
     * 指示 UDI 中的部分
     */
    // UDI 中的所有部分
    const UDI_ALL        = 'all';
    // UDI 中的控制器
    const UDI_CONTROLLER = 'controller';
    // UDI 中的动作
    const UDI_ACTION     = 'action';
    // UDI 中的名字空间
    const UDI_NAMESPACE  = 'namespace';
    // UDI 中的模块
    const UDI_MODULE     = 'module';

    /**
     * 指示 UDI 的默认值
     */
    // 默认控制器
    const UDI_DEFAULT_CONTROLLER = 'default';
    // 默认动作
    const UDI_DEFAULT_ACTION     = 'index';
    // 默认的模块
    const UDI_DEFAULT_MODULE     = 'default';
    // 默认的名字空间
    const UDI_DEFAULT_NAMESPACE  = 'default';

    /**
     * 指示 URL 模式
     */
    // 标准 URL 模式
    const URL_MODE_STANDARD = 'standard';
    // 使用 PATHINFO
    const URL_MODE_PATHINFO = 'pathinfo';
    // 使用 URL 重写
    const URL_MODE_REWRITE  = 'rewrite';

    /**
     * 请求包含的模块名
     *
     * 为了性能原因，$module_name 设置为了 public 成员变量。
     * 但开发者应该使用 changeRequestUDI() 方法来修改 $module_name 等变量。
     *
     * @var string
     */
    public $module_name;

    /**
     * 请求包含的命名空间
     *
     * @var string
     */
    public $namespace;

    /**
     * 请求包含的控制器名称
     *
     * @var string
     */
    public $controller_name;

    /**
     * 请求包含的动作名
     *
     * @var string
     */
    public $action_name;

    /**
     * 附加的参数
     *
     * @var array
     */
    private $_params = array();

    /**
     * 路由对象
     *
     * @var QRouter
     */
    private $_router;

    /**
     * 当前请求 URL
     *
     * @var string
     */
    static private $_request_uri;

    /**
     * 当前请求 URL 不包含查询参数的部分
     *
     * @var string
     */
    static private $_base_uri;

    /**
     * 当前请求 URL 的目录部分
     *
     * @var string
     */
    static private $_base_dir;

    /**
     * 路由模式
     *
     * @var string
     */
    static private $_url_mode;

    /**
     * UDI 的默认值
     */
    private static $_udi_defaults = array(
        self::UDI_MODULE => self::UDI_DEFAULT_MODULE,
        self::UDI_NAMESPACE => self::UDI_DEFAULT_NAMESPACE,
        self::UDI_CONTROLLER => self::UDI_DEFAULT_CONTROLLER,
        self::UDI_ACTION     => self::UDI_DEFAULT_ACTION,
    );

    /**
     * 构造函数
     */
    private function __construct()
    {
        $this->reinit();
    }

    /**
     * 根据服务器运行环境，重新初始化 QContext 对象
     *
     * @param boolean $full_init 是否进行完全的初始化
     */
    function reinit($full_init = false)
    {
        if ($full_init)
        {
            $this->_params = array();
            $this->_router = null;
        }

        self::$_request_uri = null;
        self::$_base_uri    = null;
        self::$_base_dir    = null;
        self::$_url_mode    = null;

        // 如果有必要，初始化路由服务
        $url_mode = strtolower(Q::ini('dispatcher_url_mode'));
        if (is_null($this->_router)
            && ($url_mode == self::URL_MODE_PATHINFO || $url_mode == self::URL_MODE_REWRITE))
        {
            $this->_router = new QRouter();
            $this->_router->import(Q::ini('routes'), 'global_default_routes');
            $result = $this->_router->match('/' . ltrim($this->pathinfo(), '/'));
            if ($result)
            {
                foreach ($result as $var => $value)
                {
                    if (strlen($value) === 0) continue;
                    if (!isset($_GET[$var]) || strlen($_GET[$var]) === 0)
                    {
                        $_GET[$var] = $_REQUEST[$var] = $value;
                    }
                }
            }
        }
        self::$_url_mode = $url_mode;

        // 从 $_GET 中提取请求参数
        $keys = array_keys($_GET);
        if (!empty($keys))
        {
            $keys = array_combine($keys, $keys);
            $keys = array_change_key_case($keys);
        }

        $udi = array();
        $udi[self::UDI_CONTROLLER] = (isset($keys[self::UDI_CONTROLLER]))
                                     ? $_GET[$keys[self::UDI_CONTROLLER]] : null;
        $udi[self::UDI_ACTION]     = (isset($keys[self::UDI_ACTION]))
                                     ? $_GET[$keys[self::UDI_ACTION]] : null;
        $udi[self::UDI_MODULE]     = (isset($keys[self::UDI_MODULE]))
                                     ? $_GET[$keys[self::UDI_MODULE]] : null;
        $udi[self::UDI_NAMESPACE]  = (isset($keys[self::UDI_NAMESPACE]))
                                     ? $_GET[$keys[self::UDI_NAMESPACE]] : null;

        $this->changeRequestUDI($udi);
    }

    /**
     * 返回 QContext 对象的唯一实例
     *
     * @code php
     * $context = QContext::instance();
     * @endcode
     *
     * @return QContext QContext 对象的唯一实例
     */
    static function instance()
    {
        static $instance;
        if (is_null($instance)) $instance = new QContext();
        return $instance;
    }

    /**
     * 魔法方法，访问请求参数
     *
     * __get() 魔法方法让开发者可以用 $context->parameter 的形式访问请求参数。
     * 如果指定的参数不存在，则返回 null。
     *
     * @code php
     * $title = $context->title;
     * @endcode
     *
     * 查找请求参数的顺行是 $_GET、$_POST 和 QContext 对象附加参数。
     *
     * @param string $parameter 要访问的请求参数
     *
     * @return mixed 参数值
     */
    function __get($parameter)
    {
        return $this->query($parameter);
    }

    /**
     * 魔法方法，设置附加参数
     *
     * 与 __get() 魔法方法不同，__set() 仅仅设置 QContext 对象附加参数。
     * 因此当 $_GET 或 $_POST 中包含同名参数时，用 __set() 设置的参数值
     * 只能使用 QContext::param() 方法来获得。
     *
     * @code php
     * $context->title = $title;
     * echo $context->param('title');
     * @endcode
     *
     * @param string $parameter 要设置值的参数名
     * @param mixed $value 参数值
     */
    function __set($parameter, $value)
    {
        $this->changeParam($parameter, $value);
    }

    /**
     * 魔法方法，确定是否包含指定的参数
     *
     * @param string $parameter 要检查的参数
     *
     * @return boolean 是否具有指定参数
     */
    function __isset($parameter)
    {
        return $this->offsetExists($parameter);
    }

    /**
     * 删除指定的附加参数
     *
     * __unset() 魔法方法只影响 QContext 对象的附加参数。
     *
     * @code php
     * unset($context['title']);
     * // 此时读取 title 附加参数将获得 null
     * echo $context->param('title');
     * @endcode
     *
     * @param string $parameter 要删除的参数
     */
    function __unset($parameter)
    {
        unset($this->_params[$parameter]);
    }

    /**
     * 确定是否包含指定的参数，实现 ArrayAccess 接口
     *
     * @code php
     * echo isset($context['title']);
     * @endcode
     *
     * @param string $parameter 要检查的参数
     *
     * @return boolean 参数是否存在
     */
    function offsetExists($parameter)
    {
        if (isset($_GET[$parameter]))
            return true;
        elseif (isset($_POST[$parameter]))
            return true;
        else
            return isset($this->_params[$parameter]);
    }

    /**
     * 设置附加参数，实现 ArrayAccess 接口
     *
     * 该方法功能同 __set() 魔法方法。
     *
     * @code php
     * $context['title'] = $title;
     * echo $context->param('title');
     * @endcode
     *
     * @param string $parameter 要设置的参数
     * @param mixed $value 参数值
     */
    function offsetSet($parameter, $value)
    {
        $this->changeParam($parameter, $value);
    }

    /**
     * 访问请求参数，实现 ArrayAccess 接口
     *
     * @code php
     * $title = $context['title'];
     * @endcode
     *
     * @param string $parameter 要访问的请求参数
     *
     * @return mixed 参数值
     */
    function offsetGet($parameter)
    {
        return $this->query($parameter);
    }

    /**
     * 取消附加参数，实现 ArrayAccess 接口
     *
     * 同 __unset() 方法，QContext::offsetUnset() 只影响 QContext 对象的附加参数。
     *
     * @code php
     * unset($context['title']);
     * @endcode
     *
     * @param string $parameter 要取消的附加参数
     */
    function offsetUnset($parameter)
    {
        unset($this->_params[$parameter]);
    }

    /**
     * 魔法方法，访问请求参数
     *
     * QContext::query() 方法让开发者可以用 $context->parameter 的形式访问请求参数。
     * 如果指定的参数不存在，则返回 $default 参数指定的默认值。
     *
     * @code php
     * $title = $context->query('title', 'default title');
     * @endcode
     *
     * 查找请求参数的顺行是 $_GET、$_POST 和 QContext 对象附加参数。
     *
     * @param string $parameter 要访问的请求参数
     * @param mixed $default 参数不存在时要返回的默认值
     *
     * @return mixed 参数值
     */
    function query($parameter, $default = null)
    {
        if (isset($_GET[$parameter]))
            return $_GET[$parameter];
        elseif (isset($_POST[$parameter]))
            return $_POST[$parameter];
        elseif (isset($this->_params[$parameter]))
            return $this->_params[$parameter];
        else
            return $default;
    }

    /**
     * 获得 GET 数据
     *
     * 从 $_GET 中获得指定参数，如果参数不存在则返回 $default 指定的默认值。
     *
     * @code php
     * $title = $context->get('title', 'default title');
     * @endcode
     *
     * 如果 $parameter 参数为 null，则返回整个 $_GET 的内容。
     *
     * @param string $parameter 要查询的参数名
     * @param mixed $default 参数不存在时要返回的默认值
     *
     * @return mixed 参数值
     */
    function get($parameter = null, $default = null)
    {
        if (is_null($parameter))
            return $_GET;
        return isset($_GET[$parameter]) ? $_GET[$parameter] : $default;
    }

    /**
     * 获得 POST 数据
     *
     * 从 $_POST 中获得指定参数，如果参数不存在则返回 $default 指定的默认值。
     *
     * @code php
     * $body = $context->post('body', 'default body');
     * @endcode
     *
     * 如果 $parameter 参数为 null，则返回整个 $_POST 的内容。
     *
     * @param string $parameter 要查询的参数名
     * @param mixed $default 参数不存在时要返回的默认值
     *
     * @return mixed 参数值
     */
    function post($parameter = null, $default = null)
    {
        if (is_null($parameter))
            return $_POST;
        return isset($_POST[$parameter]) ? $_POST[$parameter] : $default;
    }

    /**
     * 获得 Cookie 数据
     *
     * 从 $_COOKIE 中获得指定参数，如果参数不存在则返回 $default 指定的默认值。
     *
     * @code php
     * $auto_login = $context->cookie('auto_login');
     * @endcode
     *
     * 如果 $parameter 参数为 null，则返回整个 $_COOKIE 的内容。
     *
     * @param string $parameter 要查询的参数名
     * @param mixed $default 参数不存在时要返回的默认值
     *
     * @return mixed 参数值
     */
    function cookie($parameter = null, $default = null)
    {
        if (is_null($parameter))
            return $_COOKIE;
        return isset($_COOKIE[$parameter]) ? $_COOKIE[$parameter] : $default;
    }

    /**
     * 从 $_SERVER 查询服务器运行环境数据
     *
     * 如果参数不存在则返回 $default 指定的默认值。
     *
     * @code php
     * $request_time = $context->server('REQUEST_TIME');
     * @endcode
     *
     * 如果 $parameter 参数为 null，则返回整个 $_SERVER 的内容。
     *
     * @param string $parameter 要查询的参数名
     * @param mixed $default 参数不存在时要返回的默认值
     *
     * @return mixed 参数值
     */
    function server($parameter = null, $default = null)
    {
        if (is_null($parameter))
            return $_SERVER;
        return isset($_SERVER[$parameter]) ? $_SERVER[$parameter] : $default;
    }

    /**
     * 从 $_ENV 查询服务器运行环境数据
     *
     * 如果参数不存在则返回 $default 指定的默认值。
     *
     * @code php
     * $os_type = $context->env('OS', 'non-win');
     * @endcode
     *
     * 如果 $parameter 参数为 null，则返回整个 $_ENV 的内容。
     *
     * @param string $parameter 要查询的参数名
     * @param mixed $default 参数不存在时要返回的默认值
     *
     * @return mixed 参数值
     */
    function env($parameter = null, $default = null)
    {
        if (is_null($parameter))
            return $_ENV;
        return isset($_ENV[$parameter]) ? $_ENV[$parameter] : $default;
    }

    /**
     * 设置 QContext 对象的附加参数
     *
     * @code php
     * $context->changeParam('arg', $value);
     * @endcode
     *
     * @param string $parameter 要设置的参数名
     * @param mixed $value 参数值
     *
     * @return QContext 返回 QContext 对象本身，实现连贯接口
     */
    function changeParam($parameter, $value)
    {
        $this->_params[$parameter] = $value;
    }

    /**
     * 获得 QContext 对象的附加参数
     *
     * 如果参数不存在则返回 $default 指定的默认值。
     *
     * @code php
     * $value = $context->param('arg', 'default value');
     * @endcode
     *
     * 如果 $parameter 参数为 null，则返回所有附加参数的内容。
     *
     * @param string $parameter 要查询的参数名
     * @param mixed $default 参数不存在时要返回的默认值
     *
     * @return mixed 参数值
     */
    function param($parameter, $default = null)
    {
        if (is_null($parameter))
            return $this->_params;
        return isset($this->_params[$parameter]) ? $this->_params[$parameter] : $default;
    }

    /**
     * 返回所有上下文参数
     *
     *
     * @return array
     */
    function params()
    {
        return $this->_params;
    }

    /**
     * 取得当前请求使用的协议
     *
     * 返回值不包含协议的版本。常见的返回值是 HTTP。
     *
     * @code php
     * $protocol = $context->protocol();
     * echo $protocol;
     * @endcode
     *
     * @return string 当前请求使用的协议
     */
    function protocol()
    {
        static $protocol;

        if (is_null($protocol))
        {
            list ($protocol) = explode('/', $_SERVER['SERVER_PROTOCOL']);
        }
        return strtolower($protocol);
    }

    /**
     * 设置 REQUEST_URI
     *
     * 这项修改不会影响 $_SERVER 中值，但是修改后 QContext::requestUri() 将返回新值。
     * 同时还影响 QContext::baseUri() 和 QContext::baseDir() 的返回结果。
     *
     * @param string $request_uri 新的 REQUEST_URI 值
     *
     * @return QContext 返回 QContext 对象本身，实现连贯接口
     */
    function changeRequestUri($request_uri)
    {
        self::$_request_uri = $request_uri;
        self::$_base_uri = self::$_base_dir = null;
        return $this;
    }

    /**
     * 确定请求的完整 URL
     *
     * 几个示例：
     *
     * <ul>
     *   <li>请求 http://www.example.com/index.php?controller=posts&action=create</li>
     *   <li>返回 /index.php?controller=posts&action=create</li>
     * </ul>
     * <ul>
     *   <li>请求 http://www.example.com/news/index.php?controller=posts&action=create</li>
     *   <li>返回 /news/index.php?controller=posts&action=create</li>
     * </ul>
     * <ul>
     *   <li>请求 http://www.example.com/index.php/posts/create</li>
     *   <li>返回 /index.php/posts/create</li>
     * </ul>
     * <ul>
     *   <li>请求 http://www.example.com/news/show/id/1</li>
     *   <li>返回 /news/show/id/1</li>
     * </ul>
     *
     * 此方法参考 Zend Framework 实现。
     *
     * @return string 请求的完整 URL
     */
    function requestUri()
    {
        if (self::$_request_uri) return self::$_request_uri;

        if (isset($_SERVER['HTTP_X_REWRITE_URL']))
        {
            $uri = $_SERVER['HTTP_X_REWRITE_URL'];
        }
        elseif (isset($_SERVER['REQUEST_URI']))
        {
            $uri = $_SERVER['REQUEST_URI'];
        }
        elseif (isset($_SERVER['ORIG_PATH_INFO']))
        {
            $uri = $_SERVER['ORIG_PATH_INFO'];
            if (! empty($_SERVER['QUERY_STRING']))
            {
                $uri .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
        else
        {
            $uri = '';
        }

        self::$_request_uri = $uri;
        return $uri;
    }

    /**
     * 返回不包含任何查询参数的 URI（但包含脚本名称）
     *
     * 几个示例：
     *
     * <ul>
     *   <li>请求 http://www.example.com/index.php?controller=posts&action=create</li>
     *   <li>返回 /index.php</li>
     * </ul>
     * <ul>
     *   <li>请求 http://www.example.com/news/index.php?controller=posts&action=create</li>
     *   <li>返回 /news/index.php</li>
     * </ul>
     * <ul>
     *   <li>请求 http://www.example.com/index.php/posts/create</li>
     *   <li>返回 /index.php</li>
     * </ul>
     * <ul>
     *   <li>请求 http://www.example.com/news/show/id/1</li>
     *   <li>返回 /news/show/id/1</li>
     *   <li>假设使用了 URL 重写，并且 index.php 位于根目录</li>
     * </ul>
     *
     * 此方法参考 Zend Framework 实现。
     *
     * @return string 请求 URL 中不包含查询参数的部分
     */
    function baseUri()
    {
        if (self::$_base_uri) return self::$_base_uri;

        $filename = basename($_SERVER['SCRIPT_FILENAME']);

        if (basename($_SERVER['SCRIPT_NAME']) === $filename)
        {
            $url = $_SERVER['SCRIPT_NAME'];
        }
        elseif (basename($_SERVER['PHP_SELF']) === $filename)
        {
            $url = $_SERVER['PHP_SELF'];
        }
        elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename)
        {
            $url = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
        }
        else
        {
            // Backtrack up the script_filename to find the portion matching
            // php_self
            $path = $_SERVER['PHP_SELF'];
            $segs = explode('/', trim($_SERVER['SCRIPT_FILENAME'], '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $last = count($segs);
            $url = '';
            do
            {
                $seg = $segs[$index];
                $url = '/' . $seg . $url;
                ++ $index;
            } while (($last > $index) && (false !== ($pos = strpos($path, $url))) && (0 != $pos));
        }

        // Does the baseUrl have anything in common with the request_uri?
        $request_uri = $this->requestUri();

        if (0 === strpos($request_uri, $url))
        {
            // full $url matches
            self::$_base_uri = $url;
            return self::$_base_uri;
        }

        if (0 === strpos($request_uri, dirname($url)))
        {
            // directory portion of $url matches
            self::$_base_uri = rtrim(dirname($url), '/') . '/';
            return self::$_base_uri;
        }

        if (! strpos($request_uri, basename($url)))
        {
            // no match whatsoever; set it blank
            return '';
        }

        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of baseUrl. $pos !== 0 makes sure it is not matching a value
        // from PATH_INFO or QUERY_STRING
        if ((strlen($request_uri) >= strlen($url))
            && ((false !== ($pos = strpos($request_uri, $url)))
            && ($pos !== 0)))
        {
            $url = substr($request_uri, 0, $pos + strlen($url));
        }

        self::$_base_uri = rtrim($url, '/') . '/';
        return self::$_base_uri;
    }

    /**
     * 返回请求 URL 中的基础路径（不包含脚本名称）
     *
     * 几个示例：
     *
     * <ul>
     *   <li>请求 http://www.example.com/index.php?controller=posts&action=create</li>
     *   <li>返回 /</li>
     * </ul>
     * <ul>
     *   <li>请求 http://www.example.com/news/index.php?controller=posts&action=create</li>
     *   <li>返回 /news/</li>
     * </ul>
     * <ul>
     *   <li>请求 http://www.example.com/index.php/posts/create</li>
     *   <li>返回 /</li>
     * </ul>
     * <ul>
     *   <li>请求 http://www.example.com/news/show/id/1</li>
     *   <li>返回 /</li>
     * </ul>
     *
     * @return string 请求 URL 中的基础路径
     */
    function baseDir()
    {
        if (self::$_base_dir) return self::$_base_dir;

        $base_uri = $this->baseUri();
        if (substr($base_uri, - 1, 1) == '/')
        {
            $base_dir = $base_uri;
        }
        else
        {
            $base_dir = dirname($base_uri);
        }

        self::$_base_dir = rtrim($base_dir, '/\\') . '/';
        return self::$_base_dir;
    }

    /**
     * 返回服务器响应请求使用的端口
     *
     * 通常服务器使用 80 端口与客户端通信，该方法可以获得服务器所使用的端口号。
     *
     * @return string 服务器响应请求使用的端口
     */
    function serverPort()
    {
        static $server_port = null;

        if ($server_port) return $server_port;

        if (isset($_SERVER['SERVER_PORT']))
        {
            $server_port = intval($_SERVER['SERVER_PORT']);
        }
        else
        {
            $server_port = 80;
        }

        if (isset($_SERVER['HTTP_HOST']))
        {
            $arr = explode(':', $_SERVER['HTTP_HOST']);
            $count = count($arr);
            if ($count > 1)
            {
                $port = intval($arr[$count - 1]);
                if ($port != $server_port)
                {
                    $server_port = $port;
                }
            }
        }

        return $server_port;
    }

    /**
     * 获得响应请求的脚本文件名
     *
     * @return string 响应请求的脚本文件名
     */
    function scriptName()
    {
        return basename($_SERVER['SCRIPT_FILENAME']);
    }

    /**
     * 返回 PATHINFO 信息
     *
     * <ul>
     *   <li>请求 http://www.example.com/index.php?controller=posts&action=create</li>
     *   <li>返回 /</li>
     * </ul>
     * <ul>
     *   <li>请求 http://www.example.com/news/index.php?controller=posts&action=create</li>
     *   <li>返回 /</li>
     * </ul>
     * <ul>
     *   <li>请求 http://www.example.com/index.php/posts/create</li>
     *   <li>返回 /</li>
     * </ul>
     * <ul>
     *   <li>请求 http://www.example.com/news/show/id/1</li>
     *   <li>返回 /news/show/id/1</li>
     *   <li>假设使用了 URL 重写，并且 index.php 位于根目录</li>
     * </ul>
     *
     * 此方法参考 Zend Framework 实现。
     *
     * @return string
     */
    function pathinfo()
    {
        if (!empty($_SERVER['PATH_INFO'])) return $_SERVER['PATH_INFO'];

        $base_url = $this->baseUri();

        if (null === ($request_uri = $this->requestUri())) return '';

        // Remove the query string from REQUEST_URI
        if (($pos = strpos($request_uri, '?')))
        {
            $request_uri = substr($request_uri, 0, $pos);
        }

        if ((null !== $base_url) && (false === ($pathinfo = substr($request_uri, strlen($base_url)))))
        {
            // If substr() returns false then PATH_INFO is set to an empty string
            $pathinfo = '';
        }
        elseif (null === $base_url)
        {
            $pathinfo = $request_uri;
        }

        return $pathinfo;
    }

    /**
     * 返回请求使用的方法
     *
     * @return string
     */
    function requestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 是否是 GET 请求
     *
     * @return boolean
     */
    function isGET()
    {
        return $this->requestMethod() == 'GET';
    }

    /**
     * 是否是 POST 请求
     *
     * @return boolean
     */
    function isPOST()
    {
        return $this->requestMethod() == 'POST';
    }

    /**
     * 是否是 PUT 请求
     *
     * @return boolean
     */
    function isPUT()
    {
        return $this->requestMethod() == 'PUT';
    }

    /**
     * 是否是 DELETE 请求
     *
     * @return boolean
     */
    function isDELETE()
    {
        return $this->requestMethod() == 'DELETE';
    }

    /**
     * 是否是 HEAD 请求
     *
     * @return boolean
     */
    function isHEAD()
    {
        return $this->requestMethod() == 'HEAD';
    }

    /**
     * 是否是 OPTIONS 请求
     *
     * @return boolean
     */
    function isOPTIONS()
    {
        return $this->requestMethod() == 'OPTIONS';
    }

    /**
     * 判断 HTTP 请求是否是通过 XMLHttp 发起的
     *
     * @return boolean
     */
    function isAJAX()
    {
        return strtolower($this->header('X_REQUESTED_WITH')) == 'xmlhttprequest';
    }

    /**
     * 判断 HTTP 请求是否是通过 Flash 发起的
     *
     * @return boolean
     */
    function isFlash()
    {
        $agent = strtolower($this->header('USER_AGENT'));
        return strpos($agent, 'shockwave flash') !== false || strpos($agent, 'adobeair') !== false;
    }

    /**
     * 返回请求的原始内容
     *
     * @return string
     */
    function requestRawBody()
    {
        $body = file_get_contents('php://input');
        return (strlen(trim($body)) > 0) ? $body : false;
    }

    /**
     * 返回 HTTP 请求头中的指定信息，如果没有指定参数则返回 false
     *
     * @param string $header 要查询的请求头参数
     *
     * @return string 参数值
     */
    function header($header)
    {
        $temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        if (!empty($_SERVER[$temp])) return $_SERVER[$temp];

        if (function_exists('apache_request_headers'))
        {
            $headers = apache_request_headers();
            if (!empty($headers[$header])) return $headers[$header];
        }

        return false;
    }

    /**
     * 返回当前请求的参照 URL
     *
     * @return string 当前请求的参照 URL
     */
    function referer()
    {
        return $this->header('REFERER');
    }

    /**
     * 返回当前使用的路由服务对象
     *
     * 如果没有启动路由，则返回 null。
     *
     * @return QRouter 当前使用的路由服务对象
     */
    function router()
    {
        return $this->_router;
    }

    /**
     * 构造 url
     *
     * 用法：
     *
     * @code php
     * url(UDI, [附加参数数组], [路由名])
     * @endcode
     *
     * UDI 是统一目的地标识符（Uniform Destination Identifier）的缩写。
     * UDI 由控制器、动作、名字空间以及模块名组成，采用如下的格式：
     *
     * @code php
     * namespace::controller/action@module
     * @endcode
     *
     * UDI 字符串中，每一个部分都是可选的。
     * 如果没有提供控制器和动作名，则使用当前的控制器和默认动作名（index）代替。
     * 同样，如果未提供模块名和名字空间，均使用当前值代替。
     *
     * UDI 字符串写法示例：
     *
     * @code php
     * 'controller'
     * 'controller/action'
     * '/action'
     * 'controller@module'
     * 'controller/action@module'
     * 'namespace::controller'
     * 'namespace::controller/action'
     * 'namespace::controller@module'
     * 'namespace::controller/action@module'
     * '@module'
     * 'namespace::@module'
     * @endcode
     *
     * 示例：
     * @code php
     * url('admin::posts/edit', array('id' => $post->id()));
     * @endcode
     *
     * $params 参数除了采用数组，还可以是以“/”符号分割的字符串：
     *
     * @code php
     * url('posts/index', 'page/3');
     * url('users/show', 'id/5/profile/yes');
     * @endcode
     *
     * 在使用 PATHINFO 和 URL 重写时，可以使用通过制定路由名来强制要求 QeePHP
     * 采用指定的路由规则来生成 URL。强制指定路由规则可以加快 URL 的生成，
     * 但在路由规则名称发生变化时，需要修改生成 URL 的代码。
     *
     * $opts 参数用于控制如何生成 URL。可用的选项有：
     *
     * -  base_uri: 指定 URL 前部要添加的路径（可以包括协议、域名和端口，以及路径）
     * -  script: 指定 URL 前部要使用的脚本名
     * -  mode: 指定 URL 生成模式，可以是 standard、pathinfo 和 rewrite
     *
     * @param string $udi UDI 字符串
     * @param array|string $params 附加参数数组
     * @param string $route_name 路由名
     * @param array $opts 控制如何生成 URL 的选项
     *
     * @return string 生成的 URL 地址
     */
    function url($udi, $params = null, $route_name = null, array $opts = null)
    {
        static $base_uri;

        if (is_null($base_uri))
        {
            if(strlen(Q::ini('base_uri')) > 0)
            {
                $base_uri = Q::ini('base_uri');
            }
            else
            {
                $base_uri = '/' . trim($this->baseDir(), '/');
                if ($base_uri != '/')
                {
                    $base_uri .= '/';
                }
            }
        }

        $udi = $this->normalizeUDI($udi);

        if (! is_array($params))
        {
            $arr = Q::normalize($params, '/');
            $params = array();
            while ($key = array_shift($arr))
            {
                $value = array_shift($arr);
                $params[$key] = $value;
            }
        }

        $params = array_filter($params, 'strlen');

        // 处理 $opts
        if (is_array($opts))
        {
            $mode   = !empty($opts['mode']) ? $opts['mode'] : self::$_url_mode;
            $script = !empty($opts['script'])
                    ? $opts['script']
                    : $this->scriptName();
            $url    = strlen($opts['base_uri']) > 0
                    ? rtrim($opts['base_uri'], '/') . '/'
                    : $base_uri;
        }
        else
        {
            $mode   = self::$_url_mode;
            $url    = $base_uri;
            $script = $this->scriptName();
        }

        if (!is_null($this->_router) && $mode != self::URL_MODE_STANDARD)
        {
            // 使用路由生成 URL
            $params = array_merge($params, $udi);
            $path = $this->_router->url($params, $route_name);
            if (self::$_url_mode == self::URL_MODE_PATHINFO && $path != '/')
            {
                $url .= $this->scriptName();
            }
            else
            {
                $url = rtrim($url, '/');
            }
            $url .= $path;
        }
        else
        {
            foreach (self::$_udi_defaults as $key => $value)
            {
                if ($udi[$key] == $value) unset($udi[$key]);
                unset($params[$key]);
            }

            $params = array_filter(array_merge($udi, $params), 'strlen');
            $url .= $script;
            if (!empty($params))
            {
                $url .= '?' . http_build_query($params, '', '&');
            }
        }

        return $url;
    }

    /**
     * 返回 UDI 的字符串表现形式
     *
     * @param array $udi 要处理的 UDI
     *
     * @return string
     */
    function UDIString(array $udi)
    {
        return "{$udi[self::UDI_NAMESPACE]}::{$udi[self::UDI_CONTROLLER]}/{$udi[self::UDI_ACTION]}@{$udi[self::UDI_MODULE]}";
    }

    /**
     * 返回规范化以后的 UDI 数组
     *
     * @code php
     * $udi = array(
     *     QContext::UDI_CONTROLLER => '',
     *     QContext::UDI_ACTION     => '',
     *     QContext::UDI_NAMESPACE  => '',
     *     QContext::UDI_MODULE     => '',
     * );
     *
     * // 输出
     * // array(
     * //     controller: default
     * //     action:     index
     * //     namespace:  default
     * //     module:     default
     * // )
     * dump($context->normalizeUDI($udi));
     *
     * $udi = 'admin::posts/edit';
     * // 输出
     * // array(
     * //     controller: posts
     * //     action:     edit
     * //     namespace:  admin
     * //     module:     default
     * // )
     * dump($context->normalizeUDI($udi));
     * @endcode
     *
     * 如果要返回字符串形式的 UDI，设置 $return_array 参数为 false。
     *
     * @param string|array $udi 要处理的 UDI
     * @param boolean $return_array 是否返回数组形式的 UDI
     *
     * @return array 处理后的 UDI
     */
    function normalizeUDI($udi, $return_array = true)
    {
        if (! is_array($udi))
        {
            // 特殊处理 "", ".", "/" UDI解析
            // "", "." 返回当前动作
            // "/" 返回当前控制器默认动作
            if(! is_string($udi) || $udi == '' || $udi == '.')
            {
                $namespace = $this->namespace;
                $module_name = $this->module_name;
                $controller = $this->controller;
                $action = $this->action;
            }
            elseif($udi == '/')
            {
                $namespace = $this->namespace;
                $module_name = $this->module_name;
                $controller = $this->controller;
                $action = self::$_udi_defaults[self::UDI_ACTION];
            }
            else
            {
                if (strpos($udi, '::') !== false)
                {
                    $arr = explode('::', $udi);
                    $namespace = array_shift($arr);
                    $udi = array_shift($arr);
                }
                else
                {
                    $namespace = $this->namespace;
                }

                if (strpos($udi, '@') !== false)
                {
                    $arr = explode('@', $udi);
                    $module_name = array_pop($arr);
                    $udi = array_pop($arr);
                }
                else
                {
                    $module_name = $this->module_name;
                }

                $arr = explode('/', $udi);
                $controller = array_shift($arr);
                $action = array_shift($arr);
            }

            $udi = array(
                self::UDI_MODULE     => $module_name,
                self::UDI_NAMESPACE  => $namespace,
                self::UDI_CONTROLLER => $controller,
                self::UDI_ACTION     => $action,
            );
        }

        if (empty($udi[self::UDI_MODULE]))
        {
            $udi[self::UDI_MODULE] = $this->module_name;
        }
        if (empty($udi[self::UDI_NAMESPACE]))
        {
            $udi[self::UDI_NAMESPACE] = $this->namespace;
        }
        if (empty($udi[self::UDI_CONTROLLER]))
        {
            $udi[self::UDI_CONTROLLER] = $this->controller_name;
        }
        if (empty($udi[self::UDI_ACTION]))
        {
            $udi[self::UDI_ACTION] = self::UDI_DEFAULT_ACTION;
        }
        foreach (self::$_udi_defaults as $key => $value)
        {
            if (empty($udi[$key]))
            {
                $udi[$key] = $value;
            }
            else
            {
                $udi[$key] = preg_replace('/[^a-z0-9]+/', '', strtolower($udi[$key]));
            }
        }

        if (!$return_array)
        {
            return "{$udi[self::UDI_NAMESPACE]}::{$udi[self::UDI_CONTROLLER]}/{$udi[self::UDI_ACTION]}@{$udi[self::UDI_MODULE]}";
        }
        else
        {
            return $udi;
        }
    }

    /**
     * 返回当前请求对应的 UDI
     *
     * 将当前请求中包含的控制器、动作、名字空间和模块名提取出来，构造为一个 UDI。
     *
     * @code php
     * dump($context->requestUDI());
     * @endcode
     *
     * @param boolean $return_array 是否返回数组形式的 UDI
     *
     * @return string|array 对应当前请求的 UDI
     */
    function requestUDI($return_array = true)
    {
        return $this->normalizeUDI("/{$this->action_name}", $return_array);
    }

    /**
     * 将 QContext 对象保存的请求参数设置为 UDI 指定的值
     *
     * @code php
     * $context->changeRequestUDI('posts/edit');
     * // 将输出 posts
     * echo $context->controller_name;
     * @endcode
     *
     * @param array|string $udi 要设置的 UDI
     *
     * @return QContext 返回 QContext 对象本身，实现连贯接口
     */
    function changeRequestUDI($udi)
    {
        $udi = $this->normalizeUDI($udi);

        $this->controller_name = $udi[self::UDI_CONTROLLER];
        $this->action_name     = $udi[self::UDI_ACTION];
        $this->module_name     = $udi[self::UDI_MODULE];
        $this->namespace       = $udi[self::UDI_NAMESPACE];

        if ($this->module_name == self::UDI_DEFAULT_MODULE)
        {
            $this->module_name = null;
        }
        if ($this->namespace   == self::UDI_DEFAULT_NAMESPACE)
        {
            $this->namespace = null;
        }
        return $this;
    }
}


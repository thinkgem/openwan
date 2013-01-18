<?php
// $Id: router.php 2653 2009-09-07 02:57:57Z dualface $

/**
 * 定义 QRouter 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: router.php 2653 2009-09-07 02:57:57Z dualface $
 * @package mvc
 */

/**
 * QRouter 实现了自定义路由解析
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: router.php 2653 2009-09-07 02:57:57Z dualface $
 * @package mvc
 */
class QRouter
{
    /**
     * 匹配模式各部分的类型
     */
    const PARTTYPE_VAR      = 'var';
    const PARTTYPE_STATIC   = 'static';
    const PARTTYPE_WILDCARD = 'wildcard';

    /**
     * 路由的类型
     */
    const ROUTE_SIMPLE  = 'simple';
    const ROUTE_REGEX   = 'regex';

    /**
     * 当前匹配成功的路由名字
     *
     * @var string
     */
    protected $_matched_route_name;

    /**
     * 最后一次反向匹配成功的路由名称
     *
     * @var string
     */
    protected $_reverse_matched_route_name;

    /**
     * 路由
     *
     * @var array
     */
    protected $_routes = array();

    /**
     * 用于识别变量的前缀符号
     *
     * @var string
     */
    protected $_var_prefix = ':';

    /**
     * 用于分割匹配模式多个部分的定界符
     *
     * @var string
     */
    protected $_part_delimiter = '/';

    /**
     * 匹配变量的正则表达式
     *
     * @var string
     */
    protected $_default_var_regex = '.+';

    /**
     * 保留变量的默认比对规则
     *
     * @var string
     */
    protected $_udi_var_regex = '([a-z][a-z0-9]*)*';

    /**
     * 保留的变量
     *
     * @var array
     */
    protected $_udi_parts = array(
        QContext::UDI_MODULE     => QContext::UDI_DEFAULT_MODULE,
        QContext::UDI_NAMESPACE  => QContext::UDI_DEFAULT_NAMESPACE,
        QContext::UDI_CONTROLLER => QContext::UDI_DEFAULT_CONTROLLER,
        QContext::UDI_ACTION     => QContext::UDI_DEFAULT_ACTION,
    );

    /**
     * 导入路由规则
     *
     * 如果指定了 $cache_id 参数，则首先尝试从缓存载入解析好的路由规则。
     *
     * @param array $rules
     * @param string $cache_id
     *
     * @return QRouter
     */
    function import(array $rules, $cache_id = null)
    {
        if ($cache_id)
        {
            $backend = Q::ini('runtime_cache_backend');
            $routes = Q::cache($cache_id, null, $backend);
        }

        if (!$cache_id || !$routes)
        {
            $routes = array();
            foreach ($rules as $route_name => $rule)
            {
                $routes[$route_name] = $this->prepareRoute($route_name, $rule);
            }
            if ($cache_id)
            {
                Q::writeCache($cache_id, $routes,
                    array('life_time' => Q::ini('routes_cache_lifetime')),
                    $backend);
            }
        }

        $this->_routes = array_merge($this->_routes, $routes);
        return $this;
    }

    /**
     * 添加一条路由规则
     *
     * @param string $route_name
     * @param array $rule
     *
     * @return QRouter
     */
    function add($route_name, array $rule)
    {
        $this->_routes[$route_name] = $this->prepareRoute($route_name, $rule);
        return $this;
    }

    /**
     * 准备指定的路由
     *
     * @param string $route_name
     * @param array $rule
     *
     * @return array
     */
    function prepareRoute($route_name, array $rule)
    {
        if (isset($rule['regex']))
        {
            return $this->_prepareRegexRoute($route_name, $rule);
        }
        else
        {
            return $this->_prepareSimpleRoute($route_name, $rule);
        }
    }

    /**
     * 移除指定的路由规则
     *
     * @param string $route_name
     *
     * @return QRouter
     */
    function remove($route_name)
    {
        unset($this->_routes[$route_name]);
        return $this;
    }

    /**
     * 取得指定名称的路由规则
     *
     * @param string $route_name
     *
     * @return array
     */
    function get($route_name)
    {
        return $this->_routes[$route_name];
    }

    /**
     * 匹配路由规则，成功返回匹配结果，失败返回 false
     *
     * @param string $path
     *
     * @return array|boolean
     */
    function match($path)
    {
        // 分割 URL
        $path_parts = explode($this->_part_delimiter, trim($path, $this->_part_delimiter));

        $this->_matched_route_name = null;
        foreach ($this->_routes as $route_name => $route)
        {
            if ($route['type'] == self::ROUTE_SIMPLE)
            {
                $result = $this->_matchSimpleRoute($route, $path_parts);
            }
            else
            {
                $result = $this->_matchRegexRoute($route, $path);
            }

            if ($result !== false)
            {
                $this->_matched_route_name = $route_name;
                foreach ($this->_udi_parts as $varname => $value)
                {
                    if (!isset($result[$varname])) $result[$varname] = $value;
                }
                return $result;
            }
        }

        return false;
    }

    /**
     * 返回最后一次匹配成功的路由名称
     *
     * @return string
     */
    function lastMatchedRouteName()
    {
        return $this->_matched_route_name;
    }

    /**
     * 通过反相匹配路由规则来生成 URL
     *
     * @param array $url_args
     * @param string $route_name
     *
     * @return string
     */
    function url(array $url_args, $route_name = null)
    {
        // 补齐 UDI 参数
        foreach ($this->_udi_parts as $varname => $default_value)
        {
            if (empty($url_args[$varname]))
            {
                $url_args[$varname] = $default_value;
            }
        }

        if ($route_name && isset($this->_routes[$route_name]))
        {
            // 用指定路由解析
            $url = $this->_reverseMatch($this->_routes[$route_name], $url_args);
            if ($url !== false)
            {
                $this->_reverse_matched_route_name = $route_name;
                return $url;
            }
        }
        else
        {
            foreach ($this->_routes as $route_name => $route)
            {
                $url = $this->_reverseMatch($route, $url_args);
                if ($url !== false)
                {
                    $this->_reverse_matched_route_name = $route_name;
                    return $url;
                }
            }
        }


        /**
         * 如果没有找到匹配的规则，则使用内置的规则
         */
        $this->_reverse_matched_route_name = null;
        if (empty($url_args)) return '';
        $url = '?' . http_build_query($url_args, '', '&');

        return $url;
    }

    /**
     * 返回最后一次反向匹配成功的路由名称
     *
     * @return string
     */
    function lastReverseMatchedRouteName()
    {
        return $this->_reverse_matched_route_name;
    }

    /**
     * 匹配基于正则的路由规则，成功返回匹配结果，失败返回 false
     *
     * @param array $route
     * @param array $path
     *
     * @return array|boolean
     */
    protected function _matchRegexRoute(array $route, $path)
    {
        $m = null;
        if (!preg_match($route['regex'], $path, $m)) return false;

        $result = array();
        foreach ($route['defaults'] as $varname => $has_default)
        {
            if ($has_default) $result[$varname] = $route['varnames'][$varname];
        }
        foreach ($route['vars'] as $varname => $offset)
        {
            if (!isset($m[$offset])) return false;
            $result[$varname] = $m[$offset];
        }

        return $result;
    }

    /**
     * 匹配路径，成功返回匹配结果，失败返回 false
     *
     * @param array $route
     * @param array $path
     *
     * @return array|boolean
     */
    protected function _matchSimpleRoute(array $route, array $path)
    {
        // 保存匹配成功后获得的变量名值对
        $values = array();

        // 遍历匹配模式的每一个部分，确认 URL 中包含需要的内容
        foreach ($route['pattern_parts'] as $pos => $part_type)
        {
            switch ($part_type)
            {
            case self::PARTTYPE_VAR:
                $varname = $route['vars'][$pos];
                if (isset($path[$pos]))
                {
                    // 用 URL 的相应部分和变量比对规则进行比对
                    $regex = "#^{$route['config'][$varname]}\$#i";
                    if (!preg_match($regex, $path[$pos]))
                    {
                        return false;
                    }
                    $value = $path[$pos];
                }
                elseif ($route['defaults'][$varname])
                {
                    // 如果该变量有默认值，则使用默认值
                    $value = $route['varnames'][$varname];
                }
                else
                {
                    // 如果 URL 没有对应部分，并且变量没有默认值，则视为比对失败
                    return false;
                }
                $values[$varname] = $value;
                break;

            case self::PARTTYPE_STATIC:
                if (!isset($path[$pos]) || strlen($path[$pos]) == 0)
                {
                    if ($route['static_optional'][$pos])
                    {
                        // 对于可选的静态部分，允许不提供
                        continue;
                    }
                    return false;
                }

                $value = $path[$pos];
                if (strcasecmp($route['static_parts'][$pos], $value) != 0)
                {
                    // 对于静态部分，如果 URL 没有提供该部分，或者与预期的不符，则比对失败
                    return false;
                }
                break;

            case self::PARTTYPE_WILDCARD:
                /**
                 * 对于通配符，获得剩余的所有部分，并停止匹配
                 *
                 * 剩余的所有参数都按照 /name/value 的形式解析，并存入 $values。
                 */
                while (isset($path[$pos]))
                {
                    $varname = urldecode($path[$pos]);
                    // 如果路由中明确定义了一个变量，则不应该让通配符匹配的变量覆盖已定义变量的值
                    if (strlen($varname) && !isset($route['varnames'][$varname]))
                    {
                        if (isset($path[$pos + 1]))
                        {
                            $values[$varname] = urldecode($path[$pos + 1]);
                        }
                        else
                        {
                            $values[$varname] = '';
                        }
                    }

                    $pos += 2;
                }
                break;
            }
        }

        // 如果 URL 还包含更多的部分，则比对失败
        if (isset($path[$pos + 1]))
        {
            return false;
        }

        foreach ($route['varnames'] as $varname => $default_value)
        {
            if (!isset($values[$varname]))
            {
                if (!$route['defaults'][$varname])
                {
                    // 如果某个变量没有在 URL 中提供，而该变量又没有默认值，则比对失败
                    return false;
                }
                $values[$varname] = $default_value;
            }
        }

        return $values;
    }

    /**
     * 根据参数创建匹配该路由的 URL，成功返回 URL 字符串，失败返回 FALSE
     *
     * @param array $route
     * @param array $url_args
     *
     * @return string|boolean
     */
    protected function _reverseMatch(array $route, array $url_args)
    {
        if ($route['type'] == self::ROUTE_SIMPLE)
        {
            return $this->_reverseMatchSimpleRoute($route, $url_args);
        }
        else
        {
            return $this->_reverseMatchRegexRoute($route, $url_args);
        }

    }

    /**
     * 根据参数创建匹配该路由的 URL，成功返回 URL 字符串，失败返回 FALSE
     *
     * @param array $route
     * @param array $url_args
     *
     * @return string|boolean
     */
    protected function _reverseMatchRegexRoute(array $route, array $url_args)
    {
        $valid = $this->_reverseMatchVars($route, $url_args);
        if ($valid === false) return false;

        if (!empty($url_args))
        {
            // 存在多余的变量，比对失败
            return false;
        }

        $path = '';
        $offset = 1;
        $vars = array_flip($route['vars']);
        foreach ($route['pattern'] as $part)
        {
            $path .= $part;
            if (isset($vars[$offset]))
            {
                $varname = $vars[$offset];
                $path .= (string)$valid[$varname];
            }
            $offset++;
        }

        return $path;
    }

    /**
     * 根据参数创建匹配该路由的 URL，成功返回 URL 字符串，失败返回 FALSE
     *
     * @param array $route
     * @param array $url_args
     *
     * @return string|boolean
     */
    protected function _reverseMatchSimpleRoute(array $route, array $url_args)
    {
        $valid = $this->_reverseMatchVars($route, $url_args);
        if ($valid === false) return false;

        // 构造 URL
        $path = array();
        $query_args = array();
        $use_wildcard = false;

        foreach ($route['pattern_parts'] as $pos => $part_type)
        {
            switch ($part_type)
            {
            case self::PARTTYPE_VAR:
                $varname = $route['vars'][$pos];
                $path[$pos] = $valid[$varname];
                break;

            case self::PARTTYPE_STATIC:
                $path[$pos] = $route['static_parts'][$pos];
                break;

            case self::PARTTYPE_WILDCARD:
                // 处理通配符时，将 $url_args 中剩余的变量都用掉
                if ($route['static_parts'][$pos] == '*')
                {
                    $use_wildcard = !empty($url_args);
                    foreach ($url_args as $varname => $value)
                    {
                        $path[] = $varname;
                        $path[] = $value;
                    }
                }
                else
                {
                    $query_args = $url_args;
                }
                $url_args = array();
                break;
            }
        }

        // 如果构造完 URL 后 $url_args 不为空，则说明 $url_args 存在该路由无法匹配的参数
        if (!empty($url_args))
        {
            return false;
        }

        // 在没有使用通配符的情况下尝试消除 URL 中不必要的部分
        if (!$use_wildcard)
        {
            $count = count($path);
            for ($pos = $count - 1; $pos >= 0; $pos--)
            {
                switch ($route['pattern_parts'][$pos])
                {
                case self::PARTTYPE_STATIC:
                    // 一旦该部分是不可选的静态内容，则停止消除
                    if (!$route['static_optional'][$pos])
                    {
                        $pos = -1;
                    }
                    else
                    {
                        unset($path[$pos]);
                    }
                    break;

                default:
                    // 如果该部分是变量，同时 $path 中的值又和路由中指定的默认值一样，则该部分可以消除
                    $varname = $route['vars'][$pos];
                    $default_value = $route['varnames'][$varname];
                    if ((string)$path[$pos] == (string)$default_value && $route['defaults'][$varname])
                    {
                        unset($path[$pos]);
                    }
                    else
                    {
                        // 否则终止消除
                        $pos = -1;
                    }
                }
            }
        }

        // 构造 URL
        foreach ($path as $offset => $path_part)
        {
            if ((string)$path_part !== '')
            {
                $path[$offset] = rawurlencode($path_part);
            }
            else
            {
                unset($path[$offset]);
            }
        }
        $url = $this->_part_delimiter;
        if (!empty($path))
        {
            $url .= implode($this->_part_delimiter, $path);
        }

        if (!empty($query_args))
        {
            $url .= '?' . http_build_query($query_args, '', '&');
        }
        return $url;
    }

    /**
     * 反向比对变量
     *
     * @param array $route
     * @param array $url_args
     *
     * @return array
     */
    protected function _reverseMatchVars(array $route, array & $url_args)
    {
        // 比对所有的变量
        $valid = array();
        foreach ($route['varnames'] as $varname => $default_value)
        {
            /**
             * 1. 对于指定了验证规则的变量，首先检查 $url_args 中是否包含该变量
             *   1.1 如果不包含，则检查路由中是否指定了默认值
             *     1.1.1 如果没有指定默认值，则比对失败
             *     1.1.2 如果提供了默认值，则将变量及默认值添加到 $url_args 中
             *   1.2 如果包含，则用变量的验证规则进行比对
             * 2. 对于没有指定验证规则的变量，首先检查 $url_args 中是否包含该变量
             *   2.1 如果不包含，则检查是否有默认值
             *     2.1.1 如果有默认值，则使用默认值
             *     2.1.2 如果没有默认值，则比对失败
             *   2.2 如果包含但不相等，或者没有提供默认值，则比对失败
             */
            $has_default = $route['defaults'][$varname];
            if (isset($route['config'][$varname]))
            {
                // 1. 对于指定了验证规则的变量，首先检查 $url_args 中是否包含该变量
                if (!isset($url_args[$varname]))
                {
                    // 1.1 如果不包含，则检查路由中是否指定了默认值
                    if (!$has_default)
                    {
                        // 1.1.1 如果没有指定默认值，则比对失败
                        return false;
                    }
                    // 1.1.2 如果提供了默认值，则将变量及默认值添加到 $url_args 中
                    $valid[$varname] = $default_value;
                    continue;
                }

                // 1.2 如果包含，则用变量的验证规则进行比对
                $regex = "#^{$route['config'][$varname]}\$#i";
                if (!preg_match($regex, $url_args[$varname]))
                {
                    return false;
                }
                $valid[$varname] = $url_args[$varname];
            }
            else
            {
                // 2. 对于没有指定验证规则的变量，检查 $url_args 中是否包含该变量
                if (!isset($url_args[$varname]))
                {
                    // 2.1 如果不包含，则检查是否有默认值
                    if ($has_default)
                    {
                        // 2.1.1 如果有默认值，则使用默认值
                        $valid[$varname] = $default_value;
                    }
                    else
                    {
                        // 2.1.2 如果没有默认值，则比对失败
                        return false;
                    }
                }
                else
                {
                    if ((string)$url_args[$varname] != (string)$default_value || !$has_default)
                    {
                        // 2.2 如果包含但与默认值不同，或者没有提供默认值，则比对失败
                        return false;
                    }

                    $valid[$varname] = $url_args[$varname];
                }

            }

            unset($url_args[$varname]);
        }

        return $valid;
    }

    /**
     * 准备基于正则的路由
     *
     * @param string $route_name
     * @param array $rule
     *
     * @return array
     */
    protected function _prepareRegexRoute($route_name, array $rule)
    {
        static $defaults = array(
            'config'    => array(),
            'defaults'  => array(),
        );

        foreach ($defaults as $key => $value)
        {
            if (!isset($rule[$key])) $rule[$key] = $value;
        }

        $route = array
        (
            'type' => self::ROUTE_REGEX,
            // 路由的名字
            'name' => $route_name,
            // 正则表达式
            'regex' => '',
            // 用于构造 URL 的模式信息
            'pattern' => array(),
            // 变量的比对规则
            'config' => array(),
            // 变量在匹配模式中的位置及其名称
            'vars' => $rule['config'],
            // 所有已定义的变量及默认值
            'varnames' => array(),
            // 指示所有变量是否设置了默认值
            'defaults' => array(),
        );

        $regex = $rule['regex'];
        $route['regex'] = "#^{$regex}\$#i";
        if (preg_match($route['regex'], '') === false)
        {
            // LC_MSG: 无效的路由规则 "%s"，因为这个基于正则的路由在指定了无效的规则 "%s".
            $msg = '无效的路由规则 "%s"，因为这个基于正则的路由指定了无效的比对规则 "%s".';
            throw new QRouter_InvalidRouteException($route_name, __($msg, $route_name, $rule['regex']));
        }

        # contents:
        #   regex: contents\-([a-z0-9]+)\-([0-9]+)(\.html)?
        #   config:
        #     category: 1
        #     id: 2
        #     format: 3
        #   defaults:
        #     module: cms
        #     controller: contents
        #     action: view
        #     format: .html
        #

        // 从 regex 中分离出每个变量的比对规则
        $m = null;
        if (preg_match_all('/\((.+?)\)/i', $regex, $m, PREG_OFFSET_CAPTURE) === false)
        {
            // LC_MSG: 无效的路由规则 "%s"，因为这个基于正则的路由在指定了无效的规则 "%s".
            $msg = '无效的路由规则 "%s"，因为这个基于正则的路由指定了无效的比对规则 "%s".';
            throw new QRouter_InvalidRouteException($route_name, __($msg, $route_name, $rule['regex']));
        }

        if (count($m[1]) != count($rule['config']))
        {
            // LC_MSG: 无效的路由规则 "%s"，因为这个基于正则的路由在比对规则中指定的变量个数与 config 设置中指定的个数不同.
            $msg = '无效的路由规则 "%s"，因为这个基于正则的路由在比对规则中指定的变量个数 (%d) 与 config 设置中指定的个数 (%d) 不同.';
            throw new QRouter_InvalidRouteException($route_name, __($msg, $route_name, count($m[1]), count($rule['config'])));
        }

        $index2vars = array_flip($route['vars']);
        $pattern = array();
        $pos = 0;
        foreach ($index2vars as $index => $varname)
        {
            --$index;
            $route['config'][$varname] = $m[1][$index][0];
            $route['varnames'][$varname] = '';
            $route['defaults'][$varname] = false;
            $offset = $m[0][$index][1];
            $len    = strlen($m[0][$index][0]);
            $pattern[] = substr($regex, $pos, $offset - $pos);
            $pos = $offset + $len;
        }
        $pattern[] = substr($regex, $pos);

        // . \ + * ? [ ^ ] $ ( ) { } = ! < > |
        static $exp_chars = array(
            'search' => array(
                '\\.', '\\\\', '\\+', '\\*', '\\?', '\\[', '\\^', '\\]', '\\$',
                '\\(', '\\)', '\\{', '\\}', '\\=', '\\!', '\\<', '\\>', '\\|',
                '\\-',
            ),
            'replace' => array(
                '.', '\\', '+', '*', '?', '[', '^', ']', '$',
                '(', ')', '{', '}', '=', '!', '<', '>', '|',
                '-',
            ),
        );

        foreach ($pattern as $offset => $part)
        {
            if (strlen($part) == 0)
            {
                unset($pattern[$offset]);
            }
            else
            {
                $part = str_replace($exp_chars['search'], $exp_chars['replace'], $part);
                $pattern[$offset] = $part;
            }
        }
        $route['pattern'] = $pattern;

        // 将规则中指定了默认值的变量添加到变量列表中
        foreach ($rule['defaults'] as $varname => $value)
        {
            $route['varnames'][$varname] = $value;
            $route['defaults'][$varname] = true;
        }

        // 将 UDI 中的变量添加到列表
        foreach ($this->_udi_parts as $varname => $value)
        {
            if (!isset($route['varnames'][$varname]) || strlen($route['varnames'][$varname]) == 0)
            {
                $route['varnames'][$varname] = $value;
                $route['defaults'][$varname] = true;
            }
        }

        return $route;
    }

    /**
     * 准备简单路由规则
     *
     * @param string $route_name
     * @param array $rule
     *
     * @return array
     */
    protected function _prepareSimpleRoute($route_name, array $rule)
    {
        static $defaults = array(
            'pattern'   => '',
            'config'    => array(),
            'defaults'  => array(),
        );

        foreach ($defaults as $key => $value)
        {
            if (!isset($rule[$key])) $rule[$key] = $value;
        }

        $route = array
        (
            'type' => self::ROUTE_SIMPLE,
            // 路由的名字
            'name' => $route_name,
            // 匹配模式
            'pattern' => ltrim($rule['pattern'], $this->_part_delimiter),
            // 匹配模式各部分的类型
            'pattern_parts' => array(),
            // 变量的比对规则
            'config' => $rule['config'],
            // 变量在匹配模式中的位置及其名称
            'vars' => array(),
            // 所有已定义的变量及默认值
            'varnames' => array(),
            // 指示所有变量是否设置了默认值
            'defaults' => array(),
            // 静态文本部分
            'static_parts' => array(),
            // 静态部分的可选状态
            'static_optional' => array(),
        );

        // 将规则中指定了默认值的变量添加到变量列表中
        foreach ($rule['defaults'] as $varname => $value)
        {
            $route['varnames'][$varname] = $value;
            $route['defaults'][$varname] = true;
        }

        // 将 UDI 中的变量添加到列表
        foreach ($this->_udi_parts as $varname => $value)
        {
            if (empty($route['varnames'][$varname]))
            {
                $route['varnames'][$varname] = $value;
                $route['defaults'][$varname] = true;
            }
        }

        // 将匹配模式 pattern 按照“/”进行分割
        $parts = explode($this->_part_delimiter, $route['pattern']);
        $use_static_optional = false;
        foreach ($parts as $pos => $part)
        {
            if ($part{0} == $this->_var_prefix)
            {
                // 从分割后的组成部分中提取出变量名
                $varname = substr($part, 1);

                if ($use_static_optional && !isset($rule['defaults'][$varname]))
                {
                    // LC_MSG: 无效的路由规则 "%s"，因为在指定不带默认值的变量 "%s" 时，该变量左侧已经出现了可选的静态部分.
                    $msg = '无效的路由规则 "%s"，因为在指定不带默认值的变量 "%s" 时，该变量左侧已经出现了可选的静态部分.';
                    throw new QRouter_InvalidRouteException($route_name, __($msg, $route_name, $varname));
                }

                if (isset($this->_udi_parts[$varname]))
                {
                    // 如果是 UDI 保留变量，则使用默认的比对规则
                    $route['config'][$varname] = $this->_udi_var_regex;
                }
                elseif (!isset($route['config'][$varname]))
                {
                    $route['config'][$varname] = $this->_default_var_regex;
                }

                // 记录下变量名及其位置
                $route['vars'][$pos] = $varname;
                if (!isset($route['varnames'][$varname]))
                {
                    // 如果还未为这个变量指定默认值，则指定默认值
                    $route['varnames'][$varname] = '';
                    $route['defaults'][$varname] = false;
                }
                $route['pattern_parts'][$pos] = self::PARTTYPE_VAR;
            }
            else
            {
                // 如果静态部分是 [text] 这样的形式，则转换为变量
                if (substr($part, 0, 1) == '[' && substr($part, -1) == ']')
                {
                    $part = substr($part, 1, -1);
                    $use_static_optional = $optional = true;
                }
                else
                {
                    if ($use_static_optional)
                    {
                        // LC_MSG: 无效的路由规则 "%s"，因为在指定静态部分 "%s" 时，左侧已经出现了可选的静态部分.
                        $msg = '无效的路由规则 "%s"，因为在指定静态部分 "%s" 时，左侧已经出现了可选的静态部分.';
                        throw new QRouter_InvalidRouteException($route_name, __($msg, $route_name, $part));
                    }

                    // 如果要在静态部分的首位使用 [] 符号，必须使用 \[ \] 的形式
                    $part = str_replace(array('\\[', '\\]'), array('[', ']'), $part);
                    $optional = false;
                }

                // 保存静态部分位置和可选状态
                $route['static_parts'][$pos] = $part;
                $route['static_optional'][$pos] = $optional;
                if ($part != '*' && $part != '?')
                {
                    $route['pattern_parts'][$pos] = self::PARTTYPE_STATIC;
                }
                else
                {
                    $route['pattern_parts'][$pos] = self::PARTTYPE_WILDCARD;
                    if (isset($parts[$pos + 1]))
                    {
                        // LC_MSG: 无效的路由规则 "%s"，因为通配符 "%s" 没有出现在匹配模式的最后.
                        $msg = '无效的路由规则 "%s"，因为通配符 "%s" 只能放置在匹配模式的最后.';
                        throw new QRouter_InvalidRouteException($route_name, __($msg, $route_name, $part));
                    }
                }
            }
        }

        return $route;
    }

}


<?php
// $Id: filter.php 2017 2009-01-08 19:09:51Z dualface $

/**
 * 定义 QFilter 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: filter.php 2017 2009-01-08 19:09:51Z dualface $
 * @package helper
 */

/**
 * 类 QFilter 实现了过滤器接口，以及一些常用的过滤器方法
 *
 * QFilter 的所有方法都是静态方法，可以采用三种方式使用 QFilter：
 *
 * -   直接调用特定的过滤方法对值进行过滤
 *
 *     @code php
 *     $value = QFilter::filter_alpha($value);
 *     @endcode
 *
 * -   通过 QFilter::filter() 调用过滤器
 *     @code php
 *     $value = QFilter::filter($value, 'alpha');
 *     @endcode
 *
 * -   通过 QFilter::filterBatch() 批量调用过滤器
 *     @code php
 *     $value = QFilter::filterBatch($value, $filters);
 *     @endcode
 *
 * 上述三种方式适合不同的情况，可以酌情使用。
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: filter.php 2017 2009-01-08 19:09:51Z dualface $
 * @package helper
 */
abstract class QFilter
{
    /**
     * 调用单个过滤器过滤值，并返回过滤结果
     *
     * @code php
     * $value = QFilter::filter($value, 'alpha');
     * @endcode
     *
     * 过滤器可以是下列类型的函数或方法：
     *
     * -   QFilter 内置的过滤器，例如 alpha；
     * -   PHP 内置的函数，例如 intval 等；
     * -   开发者自己编写的全局函数；
     * -   开发者自己编写的类静态方法，例如 MyClass::myFilter()；
     * -   callback 类型的回调函数，例如 array($obj, $method)。
     *
     * 如果出现同名的过滤器方法，按照上述顺序来检查。
     *
     * QFilter 内置的过滤器，实际上是 QFilter 类中以“filter_”开头的静态方法。
     * 例如：filter_alpha、filter_digits。
     *
     * 大多数过滤器只需要一个参数 ，及要过滤的值。但部分过滤器要求更多的参数。
     * 此时在调用 QFilter::filter() 时，可以将额外的参数一起提供：
     *
     * @code php
     * // 等同于 $value = substr($value, 0, -3)
     * $value = QFilter::filter($value, 'substr', 0, -3);
     * @endcode
     *
     * 不过对于要求多个参数的过滤器，其第一个参数必须是被过滤的值。
     *
     * @param mixed $value 要过滤的值
     * @param mixed $filter 过滤器
     *
     * @return mixed 过滤后的值
     */
    static function filter($value, $filter)
    {
        $args = func_get_args();
        array_shift($args);
        return self::filterByArgs($value, $args);
    }

    /**
     * 用一组过滤器过滤值，返回过滤结果
     *
     * QFilter::filterBatch() 可以很方便对一个值使用一组过滤器，并返回最终结果。
     *
     * @code php
     * $filters = array(
     *     array('alpha'),
     *     array('MyClass::myFilter'),
     *     array('substr', 0, -4),
     * );
     * $value = QFilter::filterBatch($value, $filters);
     * @endcode
     *
     * 传递给 filterBatch() 方法的第二个参数包含了多个过滤器及其附加参数（如果有的话）。
     *
     * @param mixed $value 要过滤的值
     * @param array $filters 包含多个过滤器和过滤器参数的数组
     *
     * @return mixed 过滤后的值
     */
    static function filterBatch($value, array $filters)
    {
        foreach ($filters as $filter)
        {
            if (!is_array($filter)) $filter = array($filter);
            $value = self::filterByArgs($value, $filter);
        }
        return $value;
    }

    /**
     * 以数组参数的形式调用一个过滤器
     *
     * @param array $value 要过滤的值
     * @param array $args 要调用的过滤器及参数
     *
     * @return mixed 过滤后的值
     */
    static function filterByArgs($value, array $args)
    {
        static $internal_funcs;

        if (is_null($internal_funcs))
        {
            $internal_funcs = array('alnum', 'alpha', 'digits');
            $internal_funcs = array_flip($internal_funcs);
        }

        $filter = array_shift($args);
        array_unshift($args, $value);
        if (!is_array($filter) && isset($internal_funcs[$filter]))
        {
            return call_user_func_array(array(__CLASS__, 'filter_' . $filter), $args);
        }

        if (is_array($filter) || function_exists($filter))
        {
            return call_user_func_array($filter, $args);
        }

        if (strpos($filter, '::'))
        {
            return call_user_func_array(explode('::', $filter), $args);
        }

        throw new Q_NotImplementedException(__($filter));
    }

    /**
     * 过滤掉非字母和数字
     *
     * @code php
     * $value = 'abcd."1234"';
     * $value = QFilter::filter_alnum($value);
     * // 过滤后的值为 abcd1234
     * @endcode
     *
     * 除了直接调用该方法，还可以通过 QFilter::filter() 等方法来调用这个过滤器。
     *
     * @param string $value 要过滤的值
     *
     * @return string 过滤后的值
     */
    static function filter_alnum($value)
    {
        if (self::_unicodeEnabled())
        {
            $pattern = '/[^a-zA-Z0-9]/';
        }
        else
        {
            $pattern = '/[^a-zA-Z0-9]/u';
        }
        return preg_replace($pattern, '', (string)$value);
    }

    /**
     * 过滤掉非字母
     *
     * 使用方法同 filter_alnum()。
     *
     * @param string $value 要过滤的值
     *
     * @return string 过滤后的值
     */
    static function filter_alpha($value)
    {
        if (self::_unicodeEnabled())
        {
            $pattern = '/[^a-zA-Z]/';
        }
        else
        {
            $pattern = '/[^a-zA-Z]/u';
        }

        return preg_replace($pattern, '', (string)$value);
    }

    /**
     * 过滤掉非数字
     *
     * 使用方法同 filter_alnum()。
     *
     * @param string $value 要过滤的值
     *
     * @return string 过滤后的值
     */
    static function filter_digits($value)
    {
        if (self::_unicodeEnabled())
        {
            $pattern = '/[^0-9]/';
        }
        else if (extension_loaded('mbstring'))
        {
            $pattern = '/[^[:digit:]]/';
        }
        else
        {
            $pattern = '/[\p{^N}]/';
        }

        return preg_replace($pattern, '', (string)$value);
    }

    /**
     * 确认 PCRE 是否支持 utf8 和 unicode
     *
     * @return boolean
     */
    static protected function _unicodeEnabled()
    {
        static $enabled = null;
        if (is_null($enabled))
        {
            $enabled = (@preg_match('/\pL/u', 'a')) ? true : false;
        }
        return $enabled;
    }
}


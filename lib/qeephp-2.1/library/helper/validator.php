<?php
// $Id: validator.php 2680 2010-01-26 15:14:28Z dualface $

/**
 * 定义 QVlidator 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: validator.php 2680 2010-01-26 15:14:28Z dualface $
 * @package helper
 */

/**
 * QValidator 提供了一组验证方法，以及调用验证方法的接口
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: validator.php 2680 2010-01-26 15:14:28Z dualface $
 * @package helper
 */
abstract class QValidator
{
    // 当有规则失败时，跳过余下的规则
    const SKIP_ON_FAILED = 'skip_on_failed';
    // 跳过其他规则
    const SKIP_OTHERS    = 'skip_others';
    // 验证通过
    const PASSED         = true;
    // 验证失败
    const FAILED         = false;
    // 检查所有规则
    const CHECK_ALL      = true;

    /**
     * 本地化变量
     *
     * @var array
     */
    static protected $_locale;

    /**
     * 用单个规则验证值
     *
     * 验证通过返回 true，失败返回 false。
     *
     * 用法：
     *
     * @code php
     * if (!QValidator::validate($value, 'max', 5))
     * {
     *     echo 'value 不能大于 5';
     * }
     * @endcode
     *
     * $validation 参数是验证规则。
     * 如果使用 QValidator 自带的验证规则，可以使用如下的写法：
     *
     * @code php
     * QValidator::validate($value, 'between', 1, 5);
     * @endcode
     *
     * 'between' 是验证规则的名字，对应于 QValidator::validate_between() 方法。
     * 因此要使用 QValidator 的其他验证方法，将方法名中的“validate_”去掉即为验证规则名。
     *
     * 如果要使用自定义的验证规则，有三种写法：
     *
     * @code php
     * // 使用某个类的静态方法做验证方法
     * QValidator::validate($value, array('MyClass', 'myMethod'), $args);
     *
     * // 功能同上
     * QValidator::validate($value, 'MyClass::myMethod', $args);
     *
     * // 使用某个对象的方法做验证方法
     * QValidator::validate($value, array($my_obj, 'myMethod), $args);
     * @endcode
     *
     * validate() 的第一个参数是要验证的值，而第二个方法是验证规则。
     * 如果验证规则需要更多的参数，则跟在第二个参数后面提供。
     *
     * @param mixed $value 要验证的值
     * @param mixed $validation 验证规则及参数
     *
     * @return boolean 验证结果
     */
    static function validate($value, $validation)
    {
        $args = func_get_args();
        unset($args[1]);
        $result = self::validateByArgs($validation, $args);
        return (bool)$result;
    }

    /**
     * 用一组规则验证值
     *
     * validateBatch() 方法对一个值应用一组验证规则，并返回最终的结果。
     * 这一组验证规则中只要有一个验证失败，都会返回 false。
     * 只有当所有规则都通过时，validateBatch() 方法才会返回 true。
     *
     * 用法：
     *
     * @code php
     * $ret = QValidator::validateBatch($value, array(
     *         array('is_int'),
     *         array('between', 2, 6),
     * ));
     * @endcode
     *
     * $validations 参数必须是一个数组，包含多个规则，及验证规则需要的参数。
     * 每个规则及参数都是一个单独的数组。
     *
     * 如果提供了 $failed 参数，则验证失败的规则会存储在 $failed 参数中：
     *
     * @code php
     * $failed = null;
     * $ret = QValidator::validateBatch($value, $validations, true, $failed);
     *
     * dump($failed, '所有没有验证通过的规则');
     * @endcode
     *
     * @param mixed $value 要验证的值
     * @param array $validations 由多个验证规则及参数组成的数组
     * @param boolean $check_all 是否检查所有规则
     * @param mixed $failed 保存验证失败的规则名
     *
     * @return boolean 验证结果
     */
    static function validateBatch($value, array $validations, $check_all = true, & $failed = null)
    {
        $result = true;
        $failed = array();
        foreach ($validations as $v)
        {
            $vf = $v[0];
            $v[0] = $value;
            $ret = self::validateByArgs($vf, $v);

            // 跳过余下的验证规则
            if ($ret === self::SKIP_OTHERS)
            {
                return $result;
            }

            if ($ret === self::SKIP_ON_FAILED)
            {
                $check_all = false;
                continue;
            }

            if ($ret) continue;

            $failed[] = $vf;
            $result = $result && $ret;

            if (!$result && !$check_all) return false;
        }

        return (bool)$result;
    }

    /**
     * 用单个规则及附加参数验证值
     *
     * validateByArgs() 方法与 validate() 方法功能相同，只是参数格式不同。
     *
     * validateByArgs() 方法的第一个参数是验证规则。
     * 第二个参数则是包括被验证值在内，要传递给验证规则的参数。
     *
     * 例如：
     *
     * @code php
     * // validate() 的写法
     * QValidator::validate($value, 'max', 6);
     *
     * // validateByArgs() 的写法
     * QValidator::validateByArgs('max', array($value, 6));
     * @endcode
     *
     * @param mixed $validation 验证规则
     * @param array $args 要传递给验证规则的参数
     *
     * @return boolean 验证结果
     */
    static function validateByArgs($validation, array $args)
    {
        static $internal_funcs;

        if (is_null($internal_funcs))
        {
            $internal_funcs = array('between', 'equal', 'greater_or_equal',
                'greater_than', 'is_alnum', 'is_alnumu', 'is_alpha', 'is_ascii',
                'is_binary', 'is_cntrl', 'is_date', 'is_datetime', 'is_digits',
                'is_domain', 'is_email', 'is_float', 'is_graph', 'is_int',
                'is_ipv4', 'is_lower', 'is_octal', 'is_print', 'is_punct',
                'is_time', 'is_type', 'is_upper', 'is_whitespace', 'is_xdigits',
                'less_or_equal', 'less_than', 'max', 'strlen', 'max_length', 'min',
                'min_length', 'not_empty', 'not_equal', 'not_null', 'not_same',
                'regex', 'same', 'skip_empty', 'skip_null', 'skip_on_failed', 'skip_by');
            $internal_funcs = array_flip($internal_funcs);
        }

        // QValidator 类的验证方法
        if (!is_array($validation) && isset($internal_funcs[$validation]))
        {
            $result = call_user_func_array(array(__CLASS__, 'validate_' . $validation), $args);
        }
        elseif (is_array($validation) || function_exists($validation))
        {
            $result = call_user_func_array($validation, $args);
        }
        elseif (strpos($validation, '::'))
        {
            $result = call_user_func_array(explode('::', $validation), $args);
        }
        else
        {
            throw new Q_NotImplementedException(__($validation));
        }
        return $result;
    }

    /**
     * 如果为空（空字符串或者 null），则跳过余下的验证
     *
     * @return mixed $value
     *
     * @return boolean
     */
    static function validate_skip_empty($value)
    {
        return (strlen($value) == 0) ? self::SKIP_OTHERS : true;
    }

    /**
     * 自定义条件，跳过余下的验证
     *
     * @return mixed $value
     *
     * @return boolean
     */
    static function validate_skip_by($value, $cond)
    {
        if(! is_array($cond)) $cond = array($cond);
        return in_array($value, $cond) ? self::SKIP_OTHERS : true;
    }

    /**
     * 如果值为 NULL，则跳过余下的验证
     *
     * @return mixed $value
     *
     * @return boolean
     */
    static function validate_skip_null($value)
    {
        return (is_null($value)) ? self::SKIP_OTHERS : true;
    }

    /**
     * 如果接下来的验证规则出错，则跳过后续的验证
     *
     * @return boolean
     */
    static function validate_skip_on_failed()
    {
        return self::SKIP_ON_FAILED;
    }

    /**
     * 使用正则表达式进行验证
     *
     * @param mixed $value
     * @param string $regxp
     *
     * @return boolean
     */
    static function validate_regex($value, $regxp)
    {
        return preg_match($regxp, $value) > 0;
    }

    /**
     * 是否等于指定值
     *
     * @param mixed $value
     * @param mixed $test
     *
     * @return boolean
     */
    static function validate_equal($value, $test)
    {
        return $value == $test && strlen($value) == strlen($test);
    }

    /**
     * 不等于指定值
     *
     * @param mixed $value
     * @param mixed $test
     *
     * @return boolean
     */
    static function validate_not_equal($value, $test)
    {
        return $value != $test || strlen($value) != strlen($test);
    }

    /**
     * 是否与指定值完全一致
     *
     * @param mixed $value
     * @param mixed $test
     *
     * @return boolean
     */
    static function validate_same($value, $test)
    {
        return $value === $test;
    }

    /**
     * 是否与指定值不完全一致
     *
     * @param mixed $value
     * @param mixed $test
     *
     * @return boolean
     */
    static function validate_not_same($value, $test)
    {
        return $value !== $test;
    }

    /**
     * 验证字符串长度
     *
     * @param string $value
     * @param int $len
     *
     * @return boolean
     */
    static function validate_strlen($value, $len)
    {
        return strlen($value) == (int)$len;
    }

    /**
     * 最小长度
     *
     * @param mixed $value
     * @param int $len
     *
     * @return boolean
     */
    static function validate_min_length($value, $len)
    {
        return strlen($value) >= $len;
    }

    /**
     * 最大长度
     *
     * @param mixed $value
     * @param int $len
     *
     * @return boolean
     */
    static function validate_max_length($value, $len)
    {
        return strlen($value) <= $len;
    }

    /**
     * 最小值
     *
     * @param mixed $value
     * @param int|float $min
     *
     * @return boolean
     */
    static function validate_min($value, $min)
    {
        return $value >= $min;
    }

    /**
     * 最大值
     *
     * @param mixed $value
     * @param int|float $max
     *
     * @return boolean
     */
    static function validate_max($value, $max)
    {
        return $value <= $max;
    }

    /**
     * 在两个值之间
     *
     * @param mixed $value
     * @param int|float $min
     * @param int|float $max
     * @param boolean $inclusive 是否包含 min/max 在内
     *
     * @return boolean
     */
    static function validate_between($value, $min, $max, $inclusive = true)
    {
        if ($inclusive)
        {
            return $value >= $min && $value <= $max;
        }
        else
        {
            return $value > $min && $value < $max;
        }
    }

    /**
     * 大于指定值
     *
     * @param mixed $value
     * @param int|float $test
     *
     * @return boolean
     */
    static function validate_greater_than($value, $test)
    {
        return $value > $test;
    }

    /**
     * 大于等于指定值
     *
     * @param mixed $value
     * @param int|float $test
     *
     * @return boolean
     */
    static function validate_greater_or_equal($value, $test)
    {
        return $value >= $test;
    }

    /**
     * 小于指定值
     *
     * @param mixed $value
     * @param int|float $test
     *
     * @return boolean
     */
    static function validate_less_than($value, $test)
    {
        return $value < $test;
    }

    /**
     * 小于登录指定值
     *
     * @param mixed $value
     * @param int|float $test
     *
     * @return boolean
     */
    static function validate_less_or_equal($value, $test)
    {
        return $value <= $test;
    }

    /**
     * 不为 null
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_not_null($value)
    {
        return !is_null($value);
    }

    /**
     * 不为空
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_not_empty($value)
    {
        return strlen($value);
    }

    /**
     * 是否是特定类型
     *
     * @param mixed $value
     * @param string $type
     *
     * @return boolean
     */
    static function validate_is_type($value, $type)
    {
        return gettype($value) == $type;
    }

    /**
     * 是否是字母加数字
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_alnum($value)
    {
        return ctype_alnum($value);
    }

    /**
     * 是否是字母
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_alpha($value)
    {
        return ctype_alpha($value);
    }

    /**
     * 是否是字母、数字加下划线
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_alnumu($value)
    {
        return preg_match('/[^a-zA-Z0-9_]/', $value) == 0;
    }

    /**
     * 是否是控制字符
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_cntrl($value)
    {
        return ctype_cntrl($value);
    }

    /**
     * 是否是数字
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_digits($value)
    {
        return ctype_digit($value);
    }

    /**
     * 是否是可见的字符
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_graph($value)
    {
        return ctype_graph($value);
    }

    /**
     * 是否是全小写
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_lower($value)
    {
        return ctype_lower($value);
    }

    /**
     * 是否是可打印的字符
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_print($value)
    {
        return ctype_print($value);
    }

    /**
     * 是否是标点符号
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_punct($value)
    {
        return ctype_punct($value);
    }

    /**
     * 是否是空白字符
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_whitespace($value)
    {
        return ctype_space($value);
    }

    /**
     * 是否是全大写
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_upper($value)
    {
        return ctype_upper($value);
    }

    /**
     * 是否是十六进制数
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_xdigits($value)
    {
        return ctype_xdigit($value);
    }

    /**
     * 是否是 ASCII 字符
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_ascii($value)
    {
        return preg_match('/[^\x20-\x7f]/', $value) == 0;
    }

    /**
     * 是否是电子邮件地址
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_email($value)
    {
        //return preg_match('/^[a-z0-9]+[._\-\+]*@([a-z0-9]+[-a-z0-9]*\.)+[a-z0-9]+$/i', $value);
        return preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $value);
    }

    /**
     * 是否是日期（yyyy/mm/dd、yyyy-mm-dd）
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_date($value)
    {
        if (strpos($value, '-') !== false)
        {
            $p = '-';
        }
        elseif (strpos($value, '/') !== false)
        {
            $p = '\/';
        }
        else
        {
            return false;
        }

        if (preg_match('/^\d{4}' . $p . '\d{1,2}' . $p . '\d{1,2}$/', $value))
        {
            $arr = explode($p, $value);
            if (count($arr) < 3) return false;

            list($year, $month, $day) = $arr;
            return checkdate($month, $day, $year);
        }
        else
        {
            return false;
        }
    }

    /**
     * 是否是时间（hh:mm:ss）
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_time($value)
    {
        $parts = explode(':', $value);
        $count = count($parts);

        if ($count != 2 && $count != 3)
        {
            return false;
        }

        if ($count == 2)
        {
            $parts[2] = '00';
        }

        $test = @strtotime($parts[0] . ':' . $parts[1] . ':' . $parts[2]);

        if ($test === - 1 || $test === false || date('H:i:s', $test) != implode(':', $parts))
        {
            return false;
        }

        return true;
    }

    /**
     * 是否是日期 + 时间
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_datetime($value)
    {
        $test = @strtotime($value);
        if ($test === false || $test === - 1)
        {
            return false;
        }
        return true;
    }

    /**
     * 是否是整数
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_int($value)
    {
        if (is_null(self::$_locale))
        {
            self::$_locale = localeconv();
        }

        $value = str_replace(self::$_locale['decimal_point'], '.', $value);
        $value = str_replace(self::$_locale['thousands_sep'], '', $value);

        if (strval(intval($value)) != $value)
        {
            return false;
        }
        return true;
    }

    /**
     * 是否是浮点数
     *
     * @param mixed $value
     */
    static function validate_is_float($value)
    {
        if (is_null(self::$_locale))
        {
            self::$_locale = localeconv();
        }

        $value = str_replace(self::$_locale['decimal_point'], '.', $value);
        $value = str_replace(self::$_locale['thousands_sep'], '', $value);

        if (strval(floatval($value)) != $value)
        {
            return false;
        }
        return true;
    }

    /**
     * 是否是 IPv4 地址（格式为 a.b.c.h）
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_ipv4($value)
    {
        $test = @ip2long($value);
        return $test !== - 1 && $test !== false;
    }

    /**
     * 是否是八进制数值
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_octal($value)
    {
        return preg_match('/0[0-7]+/', $value);
    }

    /**
     * 是否是二进制数值
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_binary($value)
    {
        return preg_match('/[01]+/', $value);
    }

    /**
     * 是否是 Internet 域名
     *
     * @param mixed $value
     *
     * @return boolean
     */
    static function validate_is_domain($value)
    {
        $regular = "/^([0-9a-z\-]{1,}\.)?[0-9a-z\-]{2,}\.([0-9a-z\-]{2,}\.)?[a-z]{2,}$/i";
        return preg_match($regular, $value);
    }
}


<?php
// $Id: debug_firephp.php 2033 2009-01-09 18:32:14Z dualface $

/**
 * 定义 QDebug_FirePHP 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: debug_firephp.php 2033 2009-01-09 18:32:14Z dualface $
 * @package debug
 */

/**
 * QDebug_FirePHP 类提供对 FirePHP 的支持
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: debug_firephp.php 2033 2009-01-09 18:32:14Z dualface $
 * @package debug
 */
abstract class QDebug_FirePHP
{
    static protected $_firephp;
    static protected $_ver = '0.2';

    /**
     * 选择要使用的 FirePHP 扩展版本
     *
     * @param string $ver
     */
    static function ver($ver)
    {
        self::$_ver = $ver;
    }

    static function dump($vars, $label = null)
    {
        self::_firephp()->fb($vars, $label, FirePHP::LOG);
    }

    static function dumpTrace()
    {
    }

    static function assert($bool, $message = null)
    {
        if ($message)
        {
            $message = ' - ' . $message;
        }

        if ($bool)
        {
            self::_firephp()->fb('Assert TRUE' . $message, FirePHP::INFO);
        }
        else
        {
            self::_firephp()->fb('Assert FALSE' . $message, FirePHP::WARN);
        }
    }

    static function log($msg, $type = 'LOG')
    {
        self::_firephp()->fb($msg, $type);
    }

    /**
     * 返回  FirePHP 实例
     *
     * @return FirePHP
     */
    static protected function _firephp()
    {
        if (is_null(self::$_firephp))
        {
            $ver = self::$_ver;
            self::$_firephp = FirePHP::getInstance(true);
        }

        return self::$_firephp;
    }
}


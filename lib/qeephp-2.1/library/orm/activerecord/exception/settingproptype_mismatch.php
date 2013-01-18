<?php
// $Id: settingproptype_mismatch.php 2402 2009-04-07 03:50:49Z dualface $

/**
 * 定义 QDB_ActiveRecord_SettingPropTypeMismatchException 异常
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: settingproptype_mismatch.php 2402 2009-04-07 03:50:49Z dualface $
 * @package exception
 */

/**
 * QDB_ActiveRecord_SettingPropTypeMismatchException 异常指示指定给属性的值类型不匹配
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: settingproptype_mismatch.php 2402 2009-04-07 03:50:49Z dualface $
 * @package exception
 */
class QDB_ActiveRecord_SettingPropTypeMismatchException extends QDB_ActiveRecord_Exception
{
    public $prop_name;
    public $expected_type;
    public $actual_type;

    function __construct($class_name, $prop_name, $expected_type, $actual_type)
    {
        $this->prop_name = $prop_name;
        $this->expected_type = $expected_type;
        $this->actual_type = $actual_type;
        // LC_MSG: Setting property "%s" type mismatch on object "%s" instance. Expected type is "%s", actual is "%s".
        parent::__construct($class_name,
            __('Setting property "%s" type mismatch on object "%s" instance. Expected type is "%s", actual is "%s".',
                $prop_name, $class_name, $expected_type, $actual_type));
    }
}


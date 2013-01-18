<?php
// $Id: calltoundefinedmethod_exception.php 2003 2009-01-08 18:39:54Z dualface $

/**
 * 定义 QDB_ActiveRecord_CallToUndefinedMethodException 异常
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: calltoundefinedmethod_exception.php 2003 2009-01-08 18:39:54Z dualface $
 * @package exception
 */

/**
 * QDB_ActiveRecord_CallToUndefinedMethodException 异常指示未定义的方法
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: calltoundefinedmethod_exception.php 2003 2009-01-08 18:39:54Z dualface $
 * @package exception
 */
class QDB_ActiveRecord_CallToUndefinedMethodException extends QDB_ActiveRecord_Exception
{
    public $method_name;

    function __construct($class_name, $method_name)
    {
        $this->method_name = $method_name;
        // LC_MSG: Call to undefined method "%s" on object "%s" instance.
        parent::__construct($class_name, __('Call to undefined method "%s" on object "%s" instance.', $method_name, $class_name));
    }
}


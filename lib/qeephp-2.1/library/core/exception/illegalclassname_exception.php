<?php
// $Id: illegalclassname_exception.php 1990 2009-01-08 18:16:11Z dualface $

/**
 * 定义 Q_IllegalClassNameException 异常
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: illegalclassname_exception.php 1990 2009-01-08 18:16:11Z dualface $
 * @package exception
 */

/**
 * Q_IllegalClassNameException 异常指示无效的类名
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: illegalclassname_exception.php 1990 2009-01-08 18:16:11Z dualface $
 * @package exception
 */
class Q_IllegalClassNameException extends QException
{
    public $class_name;

    function __construct($class_name)
    {
        $this->class_name = $class_name;
        parent::__construct(__('Security check: Illegal character in class name "%s".', $class_name));
    }
}



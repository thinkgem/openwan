<?php
// $Id: association_notdefined_exception.php 2005 2009-01-08 18:43:17Z dualface $

/**
 * 定义 QDB_ActiveRecord_Association_NotDefinedException 异常
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: association_notdefined_exception.php 2005 2009-01-08 18:43:17Z dualface $
 * @package exception
 */

/**
 * QDB_ActiveRecord_Association_NotDefinedException 异常指示未定义的关联
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: association_notdefined_exception.php 2005 2009-01-08 18:43:17Z dualface $
 * @package exception
 */
class QDB_ActiveRecord_Association_NotDefinedException extends QException
{
    /**
     * 相关的 ActiveRecord 类名称
     *
     * @var string
     */
    public $class_name;

    /**
     * 关联属性名
     *
     * @var string
     */
    public $prop_name;

    function __construct($class_name, $prop_name)
    {
        $this->class_name = $class_name;
        $this->prop_name = $prop_name;
        // LC_MSG: ActiveRecord 类 "%s" 没有定义属性 "%s"，或者该属性不是关联对象.
        parent::__construct(__('ActiveRecord 类 "%s" 没有定义属性 "%s"，或者该属性不是关联对象.', $class_name, $prop_name));
    }
}


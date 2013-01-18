<?php
// $Id: changereadonlyprop_exception.php 2232 2009-02-10 20:45:52Z dualface $

/**
 * 定义 QDB_ActiveRecord_ChangingReadonlyPropException 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: changereadonlyprop_exception.php 2232 2009-02-10 20:45:52Z dualface $
 * @package exception
 */

/**
 * QDB_ActiveRecord_ChangingReadonlyPropException 指示某个属性是只读
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: changereadonlyprop_exception.php 2232 2009-02-10 20:45:52Z dualface $
 * @package exception
 */
class QDB_ActiveRecord_ChangingReadonlyPropException extends QDB_ActiveRecord_Exception
{
    public $prop_name;

    function __construct($class_name, $prop_name)
    {
        $this->prop_name = $prop_name;
        // LC_MSG: Setting readonly property "%s" on object "%s" instance.
        parent::__construct($class_name, __('Setting readonly property "%s" on object "%s" instance.', $prop_name, $class_name));
    }
}


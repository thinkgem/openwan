<?php
// $Id: validatefailed_exception.php 2003 2009-01-08 18:39:54Z dualface $

/**
 * 定义 QDB_ActiveRecord_ValidateFailedException 异常
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: validatefailed_exception.php 2003 2009-01-08 18:39:54Z dualface $
 * @package exception
 */

/**
 * QDB_ActiveRecord_ValidateFailedException 异常封装了 ActiveRecord 对象的验证失败事件
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: validatefailed_exception.php 2003 2009-01-08 18:39:54Z dualface $
 * @package exception
 */
class QDB_ActiveRecord_ValidateFailedException extends QValidator_ValidateFailedException
{
    /**
     * 被验证的对象
     *
     * @var QDB_ActiveRecord_Abstract
     */
    public $validate_obj;

    /**
     * 构造函数
     *
     * @param array $errors
     * @param QDB_ActiveRecord_Abstract $obj
     */
    function __construct(array $errors, QDB_ActiveRecord_Abstract $obj)
    {
        $this->validate_obj = $obj;
        parent::__construct($errors, $obj->toArray(0));
    }
}


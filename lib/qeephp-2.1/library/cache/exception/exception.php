<?php
// $Id: exception.php 2001 2009-01-08 18:23:44Z dualface $

/**
 * 定义 QCache_Exception 异常
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: exception.php 2001 2009-01-08 18:23:44Z dualface $
 * @package exception
 */

/**
 * QCache_Exception 异常封装所有的缓存错误
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: exception.php 2001 2009-01-08 18:23:44Z dualface $
 * @package exception
 */
class QCache_Exception extends QException
{
    public $filename;

    function __construct($msg, $filename = null)
    {
        $this->filename = $filename;
        parent::__construct(__($msg, $filename));
    }
}


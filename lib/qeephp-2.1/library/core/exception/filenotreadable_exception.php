<?php
// $Id: filenotreadable_exception.php 1990 2009-01-08 18:16:11Z dualface $

/**
 * 定义 Q_FileNotReadableException 异常
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: filenotreadable_exception.php 1990 2009-01-08 18:16:11Z dualface $
 * @package exception
 */

/**
 * Q_FileNotReadableException 异常指示文件无法读取错误
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: filenotreadable_exception.php 1990 2009-01-08 18:16:11Z dualface $
 * @package exception
 */
class Q_FileNotReadableException extends QException
{
    public $filename;

    function __construct($filename)
    {
        $this->filename = $filename;
        // LC_MSG: File "%s" not readable.
        parent::__construct(__('File "%s" not readable.', $filename));
    }
}


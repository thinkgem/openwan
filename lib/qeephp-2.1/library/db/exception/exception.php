<?php
// $Id: exception.php 1994 2009-01-08 18:19:50Z dualface $

/**
 * 定义 QDB_Exception 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: exception.php 1994 2009-01-08 18:19:50Z dualface $
 * @package exception
 */

/**
 * QDB_Exception 用于封装数据库操作相关的异常
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: exception.php 1994 2009-01-08 18:19:50Z dualface $
 * @package exception
 */
class QDB_Exception extends QException
{
	/**
	 * 引发异常的 SQL 语句
	 *
	 * @var string
	 */
	public $sql;

	function __construct($sql, $error, $errcode = 0)
	{
		$this->sql = $sql;
		parent::__construct($error, $errcode);
	}
}


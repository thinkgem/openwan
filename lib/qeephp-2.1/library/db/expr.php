<?php
// $Id: expr.php 1992 2009-01-08 18:18:20Z dualface $

/**
 * 定义 QDB_Expr 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: expr.php 1992 2009-01-08 18:18:20Z dualface $
 * @package database
 */

/**
 * QDB_Expr 封装一个表达式
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: expr.php 1992 2009-01-08 18:18:20Z dualface $
 * @package database
 */
class QDB_Expr
{
	/**
	 * 封装的表达式
	 *
	 * @var string
	 */
	protected $_expr;

	/**
	 * 构造函数
	 *
	 * @param string $expr
	 */
	function __construct($expr)
	{
		$this->_expr = $expr;
	}

	/**
	 * 返回表达式的字符串表示
	 *
	 * @return string
	 */
	function __toString()
	{
		return $this->_expr;
	}

	/**
	 * 格式化为字符串
	 *
	 * @param QDB_Adapter_Abstract $conn
	 * @param string $table_name
	 * @param array $mapping
	 * @param callback $callback
	 *
	 * @return string
	 */
	function formatToString($conn, $table_name = null, array $mapping = null, $callback = null)
	{
		if (!is_array($mapping)) {
			$mapping = array();
		}
		return $conn->qsql($this->_expr, $table_name, $mapping, $callback);
	}
}


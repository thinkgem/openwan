<?php
// $Id: mysql.php 1937 2009-01-05 19:09:40Z dualface $

/**
 * 定义 QDB_Result_Mysql 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: mysql.php 1937 2009-01-05 19:09:40Z dualface $
 * @package database
 */

/**
 * QDB_Result_Mysql 封装了一个 mysql 查询句柄，便于释放资源
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: mysql.php 1937 2009-01-05 19:09:40Z dualface $
 * @package database
 */
class QDB_Result_Mysql extends QDB_Result_Abstract
{
	function free()
	{
		if ($this->_handle) { mysql_free_result($this->_handle); }
		$this->_handle = null;
	}

	function fetchRow()
	{
		if ($this->fetch_mode == QDB::FETCH_MODE_ASSOC) {
			$row = mysql_fetch_assoc($this->_handle);
			if ($this->result_field_name_lower && $row)
			{
				return array_change_key_case($row, CASE_LOWER);
			} else {
				return $row;
			}
		} else {
			return mysql_fetch_array($this->_handle);
		}
	}
}


<?php
// $Id: abstract.php 2676 2009-12-16 07:32:58Z yangyi $

/**
 * 定义 QDB_Result_Abstract 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: abstract.php 2676 2009-12-16 07:32:58Z yangyi $
 * @package database
 */

/**
 * QDB_Result_Abstract 是封装查询结果对象的抽象基础类
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: abstract.php 2676 2009-12-16 07:32:58Z yangyi $
 * @package database
 */
abstract class QDB_Result_Abstract
{
	/**
	 * 指示返回结果集的形式
	 *
	 * @var const
	 */
	public $fetch_mode;

	/**
	 * 指示是否将查询结果中的字段名转换为全小写
	 *
	 * @var boolean
	 */
	public $result_field_name_lower = false;

	/**
	 * 查询句柄
	 *
	 * @var resource
	 */
	protected $_handle = null;

	/**
	 * 构造函数
	 *
	 * @param resource $handle
	 * @param const $fetch_mode
	 */
	function __construct($handle, $fetch_mode)
	{
		if (is_resource($handle) || is_object($handle)) {
			$this->_handle = $handle;
		}
		$this->fetch_mode = $fetch_mode;
	}

	/**
	 * 析构函数
	 */
	function __destruct()
	{
		$this->free();
	}

	/**
	 * 返回句柄
	 *
	 * @return resource
	 */
	function handle()
	{
		return $this->_handle;
	}

	/**
	 * 指示句柄是否有效
	 *
	 * @return boolean
	 */
	function valid()
	{
		return $this->_handle != null;
	}

	/**
	 * 释放句柄
	 */
	abstract function free();

	/**
	 * 从查询句柄提取一条记录
	 *
	 * @return array
	 */
	abstract function fetchRow();

	/**
	 * 从查询句柄中提取记录集
	 *
	 * @return array
	 */
	function fetchAll()
	{
		$rowset = array();
		while (($row = $this->fetchRow())) {
			$rowset[] = $row;
		}
		return $rowset;
	}

	/**
	 * 从查询句柄提取一条记录，并返回该记录的第一个字段
	 *
	 * @return mixed
	 */
	function fetchOne()
	{
		$row = $this->fetchRow();
		return $row ? reset($row) : null;
	}

	/**
	 * 从查询句柄提取记录集，并返回包含每行指定列数据的数组，如果 $col 为 0，则返回第一列
	 *
	 * @param int $col
	 *
	 * @return array
	 */
	function fetchCol($col = 0)
	{
		$mode = $this->fetch_mode;
		$this->fetch_mode = QDB::FETCH_MODE_ARRAY;
		$cols = array();
		while (($row = $this->fetchRow())) {
			$cols[] = $row[$col];
		}
		$this->fetch_mode = $mode;
		return $cols;
	}

	/**
	 * 返回记录集和指定字段的值集合，以及以该字段值作为索引的结果集
	 *
	 * 假设数据表 posts 有字段 post_id 和 title，并且包含下列数据：
	 *
	 * @code
	 * +---------+-----------------------+
	 * | post_id | title                 |
	 * +---------+-----------------------+
	 * |       1 | It's live             |
	 * +---------+-----------------------+
	 * |       2 | QeePHP Recipes        |
	 * +---------+-----------------------+
	 * |       7 | QeePHP User manual    |
	 * +---------+-----------------------+
	 * |      15 | QeePHP Quickstart     |
	 * +---------+-----------------------+
	 * @endcode
	 *
	 * 现在我们查询 posts 表的数据，并以 post_id 的值为结果集的索引值：
	 *
	 * 用法:
	 * @code
	 * $sql = "SELECT * FROM posts";
	 * $handle = $dbo->execute($sql);
	 *
	 * $fields_value = array();
	 * $ref = array();
	 * $rowset = $handle->fetchAllRefby(array('post_id'), $fields_value, $ref);
	 * @endcode
	 *
	 * 上述代码执行后，$rowset 包含 posts 表中的全部 4 条记录。
	 * 最后，$fields_value 和 $ref 是如下形式的数组：
	 *
	 * @code
	 * $fields_value = array(
	 *     'post_id' => array(1, 2, 7, 15),
	 * );
	 *
	 * $ref = array(
	 *     'post_id' => array(
	 *          1 => & array(array(...)),
	 *          2 => & array(array(...), array(...)),
	 *          7 => & array(array(...), array(...)),
	 *         15 => & array(array(...), array(...), array(...))
	 *     ),
	 * );
	 * @endcode
	 *
	 * $ref 用 post_id 字段值作为索引值，并且指向 $rowset 中 post_id 值相同的记录。
	 * 由于是以引用方式构造的 $ref 数组，因此并不会占用双倍内存。
	 *
	 * @param array $fields
	 * @param array $fields_value
	 * @param array $ref
	 * @param boolean $clean_up
	 *
	 * @return array
	 */
	function fetchAllRefby(array $fields, & $fields_value, & $ref, $clean_up)
	{
		$ref = $fields_value = $data = array();
		$offset = 0;

		if ($clean_up) {
			while (($row = $this->fetchRow())) {
				$data[$offset] = $row;
				foreach ($fields as $field) {
					$field_value = $row[$field];
					$fields_value[$field][$offset] = $field_value;
					$ref[$field][$field_value][] =& $data[$offset];
					unset($data[$offset][$field]);
				}
				$offset++;
			}
		} else {
			while (($row = $this->fetchRow())) {
				$data[$offset] = $row;
				foreach ($fields as $field) {
					$field_value = $row[$field];
					$fields_value[$field][$offset] = $field_value;
					$ref[$field][$field_value][] =& $data[$offset];
				}
				$offset++;
			}
		}

		return $data;
	}

    /**
     * 以对象方式返回数据
     * 如果设置了return_first为true，直接返回单个对象，否则返回对象集合
     * 更多讨论，参看：http://qeephp.com/bbs/thread-7551-1-1.html
     * 
     * @param string $class_name 
     * @param boolean $return_first 
     * @access public
     * @return mixed
     */
    function fetchObject($class_name, $return_first = false) {
        $objs = array();
        $is_ar = is_subclass_of($class_name, 'QDB_ActiveRecord_Abstract');

        while ($row = $this->fetchRow()) {
            $obj = $is_ar
                 ? new $class_name($row, QDB::FIELD, true)
                 : new $class_name($row);

            if ($return_first) return $obj;
            $objs[] = $obj;
        }

        return QColl::createFromArray($objs, $class_name);
    }
}


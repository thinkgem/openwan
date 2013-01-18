<?php
// $Id: memory.php 1987 2009-01-08 18:03:35Z dualface $

/**
 * 定义 QCache_Memory 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: memory.php 1987 2009-01-08 18:03:35Z dualface $
 * @package cache
 */

/**
 * QCache_Memory 在当次请求中使用内存来缓存数据
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: memory.php 1987 2009-01-08 18:03:35Z dualface $
 * @package cache
 */
class QCache_Memory
{
	/**
	 * 是否允许使用缓存
	 *
	 * @var boolean
	 */
	protected $_enabled = true;

    /**
     * 缓存数据
     *
     * @var array
     */
    static private $_cache = array();

	/**
	 * 写入缓存
	 *
	 * @param string $id
	 * @param mixed $data
	 */
	function set($id, $data)
	{
        if (!$this->_enabled) return;
        self::$_cache[md5($id)] = $data;
	}

	/**
	 * 读取缓存，失败或缓存撒失效时返回 false
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	function get($id)
	{
        if (!$this->_enabled) return false;
        $id = md5($id);
        return isset(self::$_cache[$id]) ? self::$_cache[$id] : false;
	}

	/**
	 * 删除指定的缓存
	 *
	 * @param string $id
	 */
	function remove($id)
    {
        unset(self::$_cache[md5($id)]);
	}
}


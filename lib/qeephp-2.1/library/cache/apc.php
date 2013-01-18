<?php
// $Id: apc.php 2289 2009-03-06 06:05:31Z dualface $

/**
 * 定义 QCache_APC 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: apc.php 2289 2009-03-06 06:05:31Z dualface $
 * @package cache
 */

/**
 * QCache_APC 类使用 APC 扩展来缓存数据
 *
 * 不过由于 APC 扩展本身的问题，频繁写入 APC 缓存可能导致 PHP 进程崩溃 :-(
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: apc.php 2289 2009-03-06 06:05:31Z dualface $
 * @package cache
 */
class QCache_APC
{
	/**
	 * 默认的缓存策略
	 *
	 * @var array
	 */
	protected $_default_policy = array(
		/**
		 * 缓存有效时间
		 *
		 * 如果设置为 0 表示缓存总是失效，设置为 null 则表示不检查缓存有效期。
		 */
		'life_time'         => 900,
	);

	/**
	 * 构造函数
	 *
	 * @param 默认的缓存策略 $default_policy
	 */
	function __construct(array $default_policy = null)
	{
		if (isset($default_policy['life_time']))
        {
			$this->_default_policy['life_time'] = (int)$default_policy['life_time'];
		}
	}

	/**
	 * 写入缓存
	 *
	 * @param string $id
	 * @param mixed $data
	 * @param array $policy
	 */
	function set($id, $data, array $policy = null)
	{
		$life_time = !isset($policy['life_time']) ? (int)$policy['life_time'] : $this->_default_policy['life_time'];
		apc_store($id, $data, $life_time);
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
		return apc_fetch($id);
	}

	/**
	 * 删除指定的缓存
	 *
	 * @param string $id
	 */
	function remove($id)
	{
		apc_delete($id);
	}
}


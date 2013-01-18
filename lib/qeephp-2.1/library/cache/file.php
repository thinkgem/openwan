<?php
// $Id: file.php 2543 2009-06-07 07:07:00Z dualface $

/**
 * 定义 QCache_File 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: file.php 2543 2009-06-07 07:07:00Z dualface $
 * @package cache
 */

/**
 * QCache_File 类提供以文件系统来缓存数据的服务
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: file.php 2543 2009-06-07 07:07:00Z dualface $
 * @package cache
 */
class QCache_File
{
	/**
	 * 是否允许使用缓存
	 *
	 * @var boolean
	 */
	protected $_enabled = true;

	/**
	 * 默认的缓存策略
	 *
	 * -  life_time: 缓存有效时间（秒），默认值 900
     *    如果设置为 0 表示缓存总是失效，设置为 null 则表示不检查缓存有效期。
	 *
     * -  serialize： 自动序列化数据后再写入缓存，默认为 true
     *    可以很方便的缓存 PHP 变量值（例如数组），但要慢一点。
	 *
	 * -  encoding_filename： 编码缓存文件名，默认为 true
	 *    如果缓存ID存在非文件名字符，那么必须对缓存文件名编码。
     *
	 * -  cache_dir_depth: 缓存目录深度，默认为 0
	 *    如果大于 1，则会在缓存目录下创建子目录保存缓存文件。
	 *    如果要写入的缓存文件超过 500 个，目录深度设置为 1 或者 2 较为合适。
	 *    如果有更多文件，可以采用更大的缓存目录深度。
     *
	 * -  cache_dir_umask: 创建缓存目录时的标志，默认为 0700
     *
	 * -  cache_dir: 缓存目录（必须指定）
     *
	 * -  test_validity: 是否在读取缓存内容时检验缓存内容完整性，默认为 true
     *
	 * -  test_method： 检验缓存内容完整性的方式，默认为 crc32
	 *    crc32 速度较快，而且安全。md5 速度最慢，但最可靠。strlen 速度最快，可靠性略差。
     *
	 * @var array
	 */
	protected $_default_policy = array
    (
		'life_time'         => 900,
		'serialize'         => true,
		'encoding_filename' => true,
		'cache_dir_depth'   => 0,
		'cache_dir_umask'   => 0700,
		'cache_dir'         => null,
		'test_validity'     => true,
		'test_method'       => 'crc32',
	);

	/**
	 * 固定要写入缓存文件头部的内容
	 *
	 * @var string
	 */
	static protected $_static_head = '<?php die(); ?>';

	/**
	 * 固定头部的长度
	 *
	 * @var int
	 */
	static protected $_static_head_len = 15;

	/**
	 * 缓存文件头部长度
	 *
	 * @var int
	 */
	static protected $_head_len = 64;

	/**
	 * 构造函数
	 *
	 * @param 默认的缓存策略 $default_policy
	 */
	function __construct(array $default_policy = null)
	{
		if (!is_null($default_policy))
        {
			$this->_default_policy = array_merge($this->_default_policy, $default_policy);
		}

		if (empty($this->_default_policy['cache_dir']))
		{
		    $this->_default_policy['cache_dir'] = Q::ini('runtime_cache_dir');
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
		if (!$this->_enabled) { return; }

		$policy = $this->_policy($policy);
		if ($policy['serialize'])
        {
			$data = serialize($data);
		}

		$path = $this->_path($id, $policy);

		// 构造缓存文件头部
		$head = self::$_static_head;
		$head .= pack('ISS', $policy['life_time'], $policy['serialize'], $policy['test_validity']);
		$head .= sprintf('% 8s', $policy['test_method']);
		$head .= str_repeat(' ', self::$_head_len - strlen($head));

		$content = $head;
		if ($policy['test_validity'])
        {
			// 接下来的 32 个字节写入用于验证数据完整性的验证码
			$content .= $this->_hash($data, $policy['test_method']);
		}
		$content .= $data;
		unset($data);

		// 写入缓存
		file_put_contents($path, $content, LOCK_EX);
	}

	/**
	 * 读取缓存，失败或缓存撒失效时返回 false
	 *
	 * @param string $id
	 * @param array $policy
	 *
	 * @return mixed
	 */
	function get($id, array $policy = null)
	{
		if (!$this->_enabled) { return false; }

		$policy = $this->_policy($policy);

		// 如果缓存策略 life_time 为 null，表示缓存数据永不过期
		if (is_null($policy['life_time']))
        {
			$refresh_time = null;
		}
        else
        {
			$refresh_time = time();
		}

		$path = $this->_path($id, $policy, false);
		clearstatcache();
		if (!file_exists($path)) { return false; }

		// 读取文件头部
		$fp = fopen($path, 'rb');
		if (!$fp) { return false; }
		flock($fp, LOCK_SH);

		$len = filesize($path);
		$mqr = get_magic_quotes_runtime();
		set_magic_quotes_runtime(0);

		// 头部的 32 个字节存储了该缓存的策略
		$head = fread($fp, self::$_head_len);
		$head = substr($head, self::$_static_head_len);
		$len -= self::$_head_len;
		$tmp = unpack('Il/Ss/St', substr($head, 0, 8));
		$policy['life_time'] = $tmp['l'];
		$policy['serialize'] = $tmp['s'];
		$policy['test_validity'] = $tmp['t'];
		$policy['test_method'] = trim(substr($head, 8, 8));

		do
        {
			// 检查缓存是否已经过期
			if (!is_null($refresh_time))
            {
				if (filemtime($path) <= $refresh_time - $policy['life_time'])
                {
					$hashtest = null;
					$data = false;
					break;
				}
			}

			// 检查缓存数据的完整性
			if ($policy['test_validity'])
            {
				$hashtest = fread($fp, 32);
				$len -= 32;
			}

			if ($len > 0)
            {
				$data = fread($fp, $len);
			}
            else
            {
				$data = false;
			}
			set_magic_quotes_runtime($mqr);
		} while (false);

		flock($fp, LOCK_UN);
		fclose($fp);
		if ($data === false)
        {
			return false;
		}

		if ($policy['test_validity'])
        {
			$hash = $this->_hash($data, $policy['test_method']);
			if ($hash != $hashtest)
            {
				if (is_null($refresh_time))
                {
					// 如果是永不过期的缓存文件没通过验证，则直接删除
					unlink($path);
				}
                else
                {
					// 否则设置文件时间为已经过期
					touch($path, time() - 2 * abs($policy['life_time']));
				}
				return false;
			}
		}

		if ($policy['serialize'])
        {
			$data = @unserialize($data);
		}

		return $data;
	}

	/**
	 * 删除指定的缓存
	 *
	 * @param string $id
	 * @param array $policy
	 */
	function remove($id, array $policy = null)
	{
		$path = $this->_path($id, $this->_policy($policy), false);
		if (is_file($path)) { unlink($path); }
	}

	/**
	 * 确定缓存文件名，并创建需要的次级缓存目录
	 *
	 * @param string $id
     * @param array $policy
     * @param boolean $mkdirs
	 *
	 * @return string
	 */
	protected function _path($id, array $policy, $mkdirs = true)
	{
		if ($policy['encoding_filename'])
        {
			$filename = 'cache_' . md5($id) . '.php';
		}
        else
        {
			$filename = 'cache_' . $id . '.php';
		}

		$root_dir = rtrim($policy['cache_dir'], '\\/');

        if (empty($root_dir))
        {
            throw new QCache_Exception(__('cache_dir must be a directory. please check seting "runtime_cache_dir".'));
        }

        $root_dir .= DIRECTORY_SEPARATOR;

		if ($policy['cache_dir_depth'] > 0 && $mkdirs)
        {
            $hash = md5($filename);
            $root_dir .= 'cache_';
            for ($i = 1; $i <= $policy['cache_dir_depth']; $i++)
            {
                $root_dir .= substr($hash, 0, $i) . DIRECTORY_SEPARATOR;
                if (is_dir($root_dir)) { continue; }
                mkdir($root_dir, $policy['cache_dir_umask']);
            }
        }

		return $root_dir . $filename;
	}

	/**
	 * 返回有效的策略选项
	 *
	 * @param array $policy
	 * @return array
	 */
	protected function _policy(array $policy = null)
	{
		return !is_null($policy) ? array_merge($this->_default_policy, $policy) : $this->_default_policy;
	}

	/**
	 * 获得数据的校验码
	 *
	 * @param string $data
	 * @param string $type
	 * @return string
	 */
	protected function _hash($data, $type)
	{
		switch ($type)
        {
		case 'md5':
			return md5($data);
		case 'crc32':
			return sprintf('% 32d', crc32($data));
		default:
			return sprintf('% 32d', strlen($data));
		}
	}
}


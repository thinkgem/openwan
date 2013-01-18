<?php
/**
 * 定义 QCache_PHPDataFile 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @package cache
 */

/**
 * QCache_PHPDataFile 类以 .php 文件来保存 PHP 的变量内容
 *
 * 与 QCache_File 相比，QCache_PHPDataFile 速度更快，但只能保存有效的 PHP 变量内容。
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @package cache
 */
class QCache_PHPDataFile
{
    /**
     * 默认的缓存策略
     *
     * -  life_time: 缓存有效时间（秒），默认值 900
     *    如果设置为 0 表示缓存总是失效，设置为 -1 或其他比 0 小的值则表示不检查缓存有效期。
     *
     * -  cache_dir: 缓存目录（必须指定）
     *
     * @var array
     */
    protected $_default_policy = array
    (
        'life_time'         => 900,
        'cache_dir'         => null,
    );

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
        $this->_default_policy['cache_dir'] = rtrim($this->_default_policy['cache_dir'], '/\\');
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
        $policy = $this->_policy($policy);
        $path = $this->_path($id, $policy);

        $content = array(
            'expired' => time() + $policy['life_time'],
            'data'    => $data,
        );
        $content = '<?php return ' . var_export($content, true) . ';';

        // 写入缓存，并去掉多余空格
        file_put_contents($path, $content, LOCK_EX);
        // file_put_contents($path, php_strip_whitespace($path), LOCK_EX);
        clearstatcache();
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
        $policy = $this->_policy($policy);
        $path = $this->_path($id, $policy);

        if (!is_file($path)) { return false; }

        $data = include($path);
        if (!is_array($data) || !isset($data['expired'])) return false;

        if ($policy['life_time'] < 0)
        {
            return $data['data'];
        }
        else
        {
            return ($data['expired'] > time()) ? $data['data'] : false;
        }
    }

    /**
     * 删除指定的缓存
     *
     * @param string $id
     * @param array $policy
     */
    function remove($id, array $policy = null)
    {
        $path = $this->_path($id, $this->_policy($policy));
        if (is_file($path)) { unlink($path); }
    }

    /**
     * 确定缓存文件名
     *
     * @param string $id
     * @param array $policy
     *
     * @return string
     */
    protected function _path($id, array $policy)
    {
        return $policy['cache_dir'] . DS . 'cache_' . md5($id) . '_data.php';
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
}


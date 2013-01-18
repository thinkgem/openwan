<?php
// $Id: yaml.php 2392 2009-04-05 13:54:14Z lonestone $

/**
 * 定义 Helper_YAML 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: yaml.php 2392 2009-04-05 13:54:14Z lonestone $
 * @package helper
 */

/**
 * Helper_YAML 提供 yaml 文档的解析和输出服务
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: yaml.php 2392 2009-04-05 13:54:14Z lonestone $
 * @package helper
 */
abstract class Helper_YAML
{
    /**
     * 载入缓存的 YAML 解析结果，如果缓存失效，则重新解析并生成缓存
     *
     * 用法：
     * @code php
     * $arr = Helper_YAML::loadCached($filename);
     * @endcode
     *
     * $replace 参数的用法参考 load() 方法。
     *
     * @param string $filename 要解析的 yaml 文件名
     * @param array $replace 对于 YAML 内容要进行自动替换的字符串对
     * @param string $cache_backend 要使用的缓存后端
     *
     * @return array 解析结果
     * @throw Q_FileNotFoundException
     */
    static function loadCached($filename, array $replace = null, $cache_backend = null)
    {
        static $cache_obj = null;

        if (!is_file($filename))
        {
            throw new Q_FileNotFoundException($filename);
        }

        $policy = array('lifetime' => 86400, 'serialize' => true);
        $mtime = filemtime($filename);
        $id = 'yaml_cache_' . md5($filename);

        if (is_null($cache_backend))
        {
            if (is_null($cache_obj))
            {
                $cache_obj = Q::singleton(Q::ini('runtime_cache_backend'));
            }
            $cache = $cache_obj;
        }
        else
        {
            $cache = Q::singleton($cache_backend);
        }

        /* @var $cache QCache_File */
        $data = $cache->get($id, $policy);
        if (!isset($data['yaml']) || empty($data['mtime']) || $data['mtime'] < $mtime)
        {
            // 缓存失效
            $data = array(
                'mtime' => $mtime,
                'yaml' => self::load($filename, $replace),
            );
            $cache->set($id, $data, $policy);
        }

        return $data['yaml'];
    }

    /**
     * 载入 YAML 文件，返回分析结果
     *
     * 关于 YAML 的详细信息,请参考 http://www.yaml.org 。
     *
     * @code php
     * $arr = Helper_YAML::load('my_data.yaml.php');
     * @endcode
     *
     * 如果指定了 $replace 参数，解析过程中会使用 $replace
     * 指定的内容去替换 YAML 文件的内容。
     *
     * @code php
     * $replace = array
     * (
     *     '%TITLE%' => 'application title',
     *     '%ADMIN_USER%' => 'administrator',
     * );
     * $data = Helper_YAML::load('my_data.yaml.php');
     * // my_data.yaml.php 中包含的
     * // %TITLE% 和 %ADMIN_USER% 字符串会被替换为指定的内容
     * @endcode
     *
     * > **注意：**
     * > 为了安全起见，不要将 yaml 文件置于浏览器能够访问的目录中。
     * >
     * > 如果无法满足前面的条件，应该将 YAML 文件的扩展名设置为 .yaml.php，
     * > 并且在每一个 YAML 文件开头添加“exit()”。
     * >
     * > 例如：
     * > @code yaml
     * > # <?php exit(); ?>
     * > invoice: 34843
     * > date   : 2001-01-23
     * > bill-to: &id001
     * > ......
     * > @endcode
     * > 这样可以确保即便浏览器直接访问该 .yaml.php 文件，也无法看到内容。
     *
     * 书写 yaml 文件时，不要插入多余的空行。
     * 完整的书写规范请参考 http://www.yaml.org 。
     *
     * @param string $filename 要解析的 yaml 文件名
     * @param array $replace 对于 YAML 内容要进行自动替换的字符串对
     *
     * @return array 解析结果
     */
    static function load($filename, array $replace = null)
    {
        return self::parse(file_get_contents($filename), $replace);
    }

    /**
     * 分析 YAML 字符串，返回分析结果
     *
     * @param string $input 要分析的 YAML 字符串
     * @param array $replace 对于 YAML 内容要进行自动替换的字符串对
     *
     * @return array 解析结果
     */
    static function parse($input, array $replace = null)
    {
        $parser = Q::singleton('sfYamlParser');
        $yaml = $parser->parse($input);

        if (!is_array($yaml)) $yaml = array();

        if (!empty($replace))
        {
            array_walk_recursive($yaml, array('Helper_YAML', '_replace'), $replace);
        }

        return $yaml;
    }

    /**
     * 将 PHP 数组（或者实现了 ArrayAccess 接口的对象）输出为字符串
     *
     * @param array $data 要输出的数组
     * @param int $indent 缩进空格数
     *
     * @return string 输出结果
     */
    static function dump($data, $indent = 2)
    {
        $dumper = Q::singleton('sfYamlDumper');
        return "# <?php die(); ?>\n\n" . $dumper->dump($data, 1) . "\n";
    }

    /**
     * 替换辅助方法
     */
    static private function _replace(& $string, $key, array $replace)
    {
        foreach ($replace as $macro => $value)
        {
            if (strpos($string, $macro) !== false)
            {
                $string = str_replace($macro, $value, $string);
                break;
            }
        }
    }
}


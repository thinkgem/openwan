<?php

abstract class QTest_Helper
{
    /**
     * 类搜索路径
     *
     * @var array
     */
    private static $_class_path = array();

    /**
     * 缓存已经载入过的配置文件
     *
     * @var array
     */
    private static $_loaded_config_files = array();

    static function loadConfig($filename, $name)
    {
        $path = dirname(dirname(__FILE__)) . '/_configs/' . $filename;
        if (is_file($path))
        {
            $path = realpath($path);

            Helper_YAML::load();
        }

    }

    static function import($dir)
    {
        $real_dir = realpath($dir);
        if ($real_dir)
        {
            $dir = rtrim($real_dir, '/\\');
            if (!isset(self::$_class_path[$dir]))
            {
                self::$_class_path[$dir] = $dir;
            }
        }
        else
        {
            trigger_error('invalid dir: ' . $dir);
        }
    }

    static function autoload($class_name)
    {
        if (class_exists($class_name, false) || interface_exists($class_name, false))
        {
            return $class_name;
        }

        $filename = str_replace('_', DIRECTORY_SEPARATOR, strtolower($class_name)) . '.php';
        foreach (self::$_class_path as $dir)
        {
            $path = $dir . DIRECTORY_SEPARATOR . $filename;
            if (is_file($path))
            {
                return require($path);
            }
        }

        return false;
    }
}

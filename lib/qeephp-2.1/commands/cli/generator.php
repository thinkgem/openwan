<?php
/**
 * 定义 CliGenerator 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: generator.php 1937 2009-01-05 19:09:40Z dualface $
 * @package core
 */

/**
 * 类 CliGenerator 实现基于命令行的代码生成器
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: generator.php 1937 2009-01-05 19:09:40Z dualface $
 * @package core
 */
class CliGenerator
{
    /**
     * 被管理应用的配置
     *
     * @var array
     */
    protected $_managed_app_config;

    /**
     * 运行时参数
     *
     * @var array
     */
    protected $_argv;

    /**
     * 构造函数
     *
     * @param array $managed_app_config
     * @param array $argv
     */
    function __construct(array $managed_app_config, array $argv)
    {
        $this->_managed_app_config = $managed_app_config;
        $this->_argv = $argv;
    }

    /**
     * 执行代码生成器
     */
    function generating()
    {
        if (count($this->_argv) < 2)
        {
            return self::help();
        }

        $type = array_shift($this->_argv);
        $method_name = 'generate' . $type;
        if (method_exists($this, $method_name))
        {
            Q::import(dirname(dirname(dirname(__FILE__))) . '/extended');
            return call_user_func(array($this, $method_name), $this->_argv);
        }
        else
        {
            echo <<<EOT

[ERROR] Invalid generate type.

EOT;
            return self::help();
        }
    }

    /**
     * 创建控制器
     */
    function generateController(array $args)
    {
        $controller_name = array_shift($args);
        try
        {
            $managed_app = new QReflection_Application($this->_managed_app_config);
            $log = $managed_app->generateController($controller_name)->log();
            $ret = 1;
        }
        catch (QException $ex)
        {
            $error = $this->_getLastError();
            $log = array($ex->getMessage(), $error);
            $ret = 0;
        }

        echo implode("\n", $log);
        echo "\n\n";
        return $ret;
    }

    /**
     * 创建模型
     */
    function generateModel(array $args)
    {
        $model_name = array_shift($args);
        if (empty($args)) return self::help();

        $table_name = array_shift($args);
        try
        {
            $managed_app = new QReflection_Application($this->_managed_app_config);
            $log = $managed_app->generateModel($model_name, $table_name)->log();
            $ret = 1;
        }
        catch (QException $ex)
        {
            $error = $this->_getLastError();
            $log = array($ex->getMessage(), $error);
            $ret = 0;
        }

        echo implode("\n", $log);
        echo "\n\n";

        return $ret;
    }

    /**
     * 显示帮助信息
     */
    static function help()
    {
        echo <<<EOT

php scripts/generate.php <type> <....>

syntax:
    php scripts/generate.php controller <controller_name>
    php scripts/generate.php model <model_name> <database_table_name>

examples:
	php scripts/generate.php controller posts
	php scripts/generate.php controller admin::posts
	php scripts/generate.php controller posts@cms
	php scripts/generate.php controller admin::posts@cms

	php scripts/generate.php model post posts
	php scripts/generate.php model post@cms posts



EOT;

        return 0;
    }

    /**
     * 返回最后一次出错的错误信息
     *
     * @return string
     */
    protected function _getLastError()
    {
        if (function_exists('error_get_last'))
        {
            $error = error_get_last();
            if (!empty($error['message']))
            {
                $error = strip_tags($error['message']);
            }
        }
        else
        {
            $error = '';
        }
        return $error;
    }
}


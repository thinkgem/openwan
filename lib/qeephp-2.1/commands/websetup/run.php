<?php
// $Id: run.php 1937 2009-01-05 19:09:40Z dualface $

/**
 * WebSetup 封装了应用程序的基本启动流程和初始化操作，并为应用程序提供一些公共服务。
 *
 * 主要完成下列任务：
 * - 初始化运行环境
 * - 提供应用程序入口
 * - 为应用程序提供公共服务
 * - 处理访问控制和用户信息在 session 中的存储
 */
class WebSetup
{
    /**
     * 应用程序的基本设置
     *
     * @var array
     */
    protected $_app_config;

    /**
     * 构造函数
     *
     * @param array $managed_app_config
     * @param array $managed_app_ini
     *
     * 构造应用程序对象
     */
    protected function __construct(array $managed_app_config, array $managed_app_ini)
    {
        set_exception_handler(array($this, 'exception_handler'));
        $dir = dirname(__FILE__);
        Q::import($dir . '/app');
        Q::import($dir . '/app/model');
        Q::import($managed_app_config['QEEPHP_DIR'] . '/extended');

        Q::replaceIni('managed_app_config', $managed_app_config);
        Q::replaceIni('managed_app_ini',    $managed_app_ini);
    }

    /**
     * 返回应用程序类的唯一实例
     *
     * @param array $managed_app_config
     * @param array $managed_app_ini
     *
     * @return WebSetup
     */
    static function instance(array $managed_app_config = null, array $managed_app_ini = null)
    {
        static $instance;
        if (is_null($instance))
        {
            if (empty($managed_app_config)) die('INVALID CONSTRUCT APP');
            $instance = new WebSetup($managed_app_config, $managed_app_ini);
        }
        return $instance;
    }

    /**
     * 根据运行时上下文对象，调用相应的控制器动作方法
     *
     * @param array $args
     *
     * @return mixed
     */
    function run(array $args = array())
    {
        $context = QContext::instance();
        $udi = $context->requestUDI('array');
        $dir = dirname(__FILE__) . '/app/controller';
        $class_name = 'controller_';
        $controller_name = strtolower($udi[QContext::UDI_CONTROLLER]);
        $class_name .= $controller_name;
        $filename = "{$controller_name}_controller.php";

        // 载入控制器文件
        if (!class_exists($class_name, false))
        {
            Q::loadClassFile($filename, array($dir), $class_name);
        }

        // 构造控制器对象
        $controller = new $class_name($this);
        $action_name = $udi[QContext::UDI_ACTION];
        $response = $controller->execute($action_name, $args);

        if (is_object($response) && method_exists($response, 'execute'))
        {
            // 如果返回结果是一个对象，并且该对象有 execute() 方法，则调用
            $response = $response->execute();
        }
        elseif ($response instanceof QController_Forward)
        {
            // 如果是一个 QController_Forward 对象，则将请求进行转发
            $response = $this->run($response->args);
        }

        // 其他情况则返回执行结果
        return $response;
    }

    /**
     * 默认的异常处理
     */
    function exception_handler(Exception $ex)
    {
        QException::dump($ex);
    }
}


/**
 * WebSetupException 封装应用程序运行过程中产生的异常
 *
 * @package app
 */
class WebSetupException extends QException
{

}


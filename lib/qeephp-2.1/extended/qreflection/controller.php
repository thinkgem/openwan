<?php

class QReflection_Controller
{
    /**
     * 控制器名称
     *
     * @var string
     */
    protected $_controller_name;

    /**
     * 控制器名字空间
     *
     * @var string
     */
    protected $_namespace;

    /**
     * 控制器的 UDI
     *
     * @var string
     */
    protected $_udi;

    /**
     * 控制器的类名称
     *
     * @var string
     */
    protected $_controller_class_name;

    /**
     * 控制器文件完整路径
     *
     * @var string
     */
    protected $_controller_file_path;

    /**
     * 控制器所属模块的反射
     *
     * @var QReflection_Module
     */
    protected $_module;

    /**
     * 构造函数
     *
     * @param QReflection_Module $module
     * @param string $controller_name
     * @param string $namespace
     */
    function __construct(QReflection_Module $module, $controller_name, $namespace = null)
    {
        $names = explode('::', $controller_name);
        if (isset($names[1]))
        {
            $namespace = $names[0];
            $controller_name = $names[1];
        }

        $this->_module = $module;
        $this->_controller_name = $controller_name;
        $this->_namespace = $namespace;

        // 确定控制器对应的文件
        $controller_name = strtolower($controller_name);
        $dir = rtrim($module->moduleDir(), '/\\') . '/controller';
        if ($namespace)
        {
            $this->_controller_file_path = "{$dir}/{$namespace}/{$controller_name}_controller.php";
        }
        else
        {
            $this->_controller_file_path = "{$dir}/{$controller_name}_controller.php";
        }

        // 确定控制器的类名称
        if (!$module->isDefaultModule())
        {
            $class = ucfirst($module->moduleName()) . '_';
        }
        else
        {
            $class = '';
        }
        $class .= 'Controller_';
        if ($namespace) $class .= ucfirst($namespace) . '_';
        $class .= ucfirst($controller_name);
        $this->_controller_class_name = $class;

        // 确定控制器的 UDI
        if ($namespace)
        {
            $udi = $namespace . '::' . $controller_name;
        }
        else
        {
            $udi = $controller_name;
        }

        if (!$module->isDefaultModule())
        {
            $udi .= '@' . $module->moduleName();
        }
        $this->_udi = $udi;
    }

    /**
     * 返回该控制器所属模块的反射
     *
     * @return QReflection_Module
     */
    function module()
    {
        return $this->_module;
    }

    /**
     * 返回该控制器所属应用的反射
     *
     * @return QReflection_Application
     */
    function app()
    {
        return $this->_module->app();
    }

    /**
     * 返回控制器名称
     *
     * @return string
     */
    function controllerName()
    {
        return $this->_controller_name;
    }

    /**
     * 返回控制器文件的完整路径
     *
     * @return string
     */
    function filePath()
    {
        return $this->_controller_file_path;
    }

    /**
     * 返回控制器所属的名字空间
     *
     * @return string
     */
    function namespace()
    {
        return $this->_namespace;
    }

    /**
     * 返回控制器所述模块的名字
     *
     * @return string
     */
    function moduleName()
    {
        return $this->module()->moduleName();
    }

    /**
     * 返回控制器的类名称
     *
     * @return string
     */
    function className()
    {
        return $this->_controller_class_name;
    }

    /**
     * 返回控制器的 UDI 名称
     *
     * @return string
     */
    function UDI()
    {
        return $this->_udi;
    }
}



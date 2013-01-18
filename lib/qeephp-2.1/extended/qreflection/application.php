<?php

class QReflection_Application
{
    /**
     * 默认模块名
     */
    const DEFAULT_MODULE_NAME = '#app#';

    /**
     * 应用程序配置
     *
     * @var array
     */
    protected $_app_config;

    /**
     * 应用程序ID
     *
     * @var string
     */
    protected $_appid;

    /**
     * 应用程序的默认模块
     *
     * @var QReflection_Module
     */
    protected $_default_module;

    /**
     * 应用所有模块的反射
     *
     * @var QColl
     */
    protected $_modules;

    /**
     * 应用所有模块的名字
     *
     * @var array of module name
     */
    protected $_modules_name;

    /**
     * 构造函数
     *
     * @param array $app_config
     */
    function __construct(array $app_config)
    {
        if (empty($app_config['MODULE_DIR']))
        {
            $app_config['MODULE_DIR'] = rtrim($app_config['ROOT_DIR'], '/\\') . DS . 'modules';
        }
        else
        {
            $app_config['MODULE_DIR']  = rtrim($app_config['MODULE_DIR'], '/\\');
        }

        if (empty($app_config['APP_DIR']))
        {
            $app_config['APP_DIR'] = rtrim($app_config['ROOT_DIR'], '/\\') . DS . 'app';
        }
        else
        {
            $app_config['APP_DIR'] = rtrim($app_config['APP_DIR'], '/\\');
        }

        $this->_appid = $app_config['APPID'];
        $this->_app_config = $app_config;
    }

    /**
     * 返回应用程序 ID
     *
     * @return string
     */
    function APPID()
    {
        return $this->_appid;
    }

    /**
     * 返回应用程序配置
     *
     * @return array
     */
    function config()
    {
        return $this->_app_config;
    }

    /**
     * 返回应用程序配置中指定项目的值
     *
     * @param string $item
     *
     * @return mixed
     */
    function configItem($item)
    {
        return isset($this->_app_config[$item]) ? $this->_app_config[$item] : null;
    }

    /**
     * 返回应用程序在 config 目录中的所有配置文件的文件名
     *
     * @return array
     */
    function configFiles()
    {
        $dir = rtrim(realpath($this->_app_config['CONFIG_DIR']), '/\\');
        $ext = $this->_app_config['CONFIG_FILE_EXTNAME'];
        $files = Helper_FileSys::recursionGlob($dir, "*.{$ext}");
        $return = array();
        $l = strlen(rtrim($dir, '/\\') . DS);
        foreach ($files as $path)
        {
            $return[] = substr(realpath($path), $l);
        }
        return $return;
    }

    /**
     * 返回该应用所有模块的名字
     *
     * @return array of module name
     */
    function modulesName()
    {
        if (is_null($this->_modules_name))
        {
            $this->_modules_name = array();
            foreach ((array)glob($this->configItem('MODULE_DIR') . '/*') as $path)
            {
                if (!is_dir($path)) continue;
                $basename = basename($path);
                if (!preg_match('/^[a-z]+[a-z0-9]*$/i', $basename)) continue;
                $this->_modules_name[] = $basename;
            }
            sort($this->_modules_name, SORT_STRING);
        }

        return $this->_modules_name;
    }

    /**
     * 获得应用所有模块的反射
     *
     * @return array of QReflection_Module
     */
    function modules()
    {
        if (is_null($this->_modules))
        {
            $this->_modules = new QColl('QReflection_Module');
            $this->_modules[self::DEFAULT_MODULE_NAME] = new QReflection_Module($this, null, $this->configItem('APP_DIR'));
            foreach ($this->modulesName() as $module_name)
            {
                $this->_modules[$module_name] = new QReflection_Module($this, 
                        $module_name, $this->configItem('MODULE_DIR') . DS . $module_name);
            }
        }

        return $this->_modules;
    }

    /**
     * 获得指定名称模块的反射
     *
     * @param string $module_name
     *
     * @return QReflection_Module
     */
    function module($module_name)
    {
        if (empty($module_name)) $module_name = self::DEFAULT_MODULE_NAME;
        $modules = $this->modules();
        return $modules[$module_name];
    }

    /**
     * 返回应用程序的默认模块的反射
     *
     * @return QReflection_Module
     */
    function defaultModule()
    {
        $modules = $this->modules();
        return $modules[self::DEFAULT_MODULE_NAME];
    }

    /**
     * 检查是否存在指定的模块
     *
     * @param string $module_name
     *
     * @return boolean
     */
    function hasModule($module_name)
    {
        $modules_name = $this->modulesName();
        return in_array($module_name, $modules_name);
    }

    /**
     * 取得应用程序的设置信息
     *
     * @param string $path
     *
     * @return mixed
     */
    function ini($path)
    {
    }

    /**
     * 为应用程序生成一个控制器的代码
     *
     * @param string $controller_name
     *
     * @return QGenerator_Controller
     */
    function generateController($controller_name)
    {
        $module = null;
        if (strpos($controller_name, '@') !== false)
        {
            list($controller_name, $module) = explode('@', $controller_name);
        }
        return $this->module($module)->generateController($controller_name);
    }

    /**
     * 为应用程序生成一个模型的代码
     *
     * @param string $model_name
     * @param string $table_name
     * @param QDB_Adapter_Abstract $dbo
     *
     * @return QGenerator_Model
     */
    function generateModel($model_name, $table_name, $dbo = null)
    {
        $module = null;
        if (strpos($model_name, '@') !== false)
        {
            list($model_name, $module) = explode('@', $model_name);
        }
        return $this->module($module)->generateModel($model_name, $table_name, $dbo);
    }
}


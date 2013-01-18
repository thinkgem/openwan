<?php

/**
 * 指定模块的反射
 */
class QReflection_Module
{
    /**
     * 该模块对应的应用程序反射
     *
     * @var QReflection_Application
     */
    protected $_app;

    /**
     * 模块所在目录
     *
     * @var string
     */
    protected $_module_dir;

    /**
     * 模块名称
     *
     * @var string
     */
    protected $_module_name;

    /**
     * 模块所有控制器的反射
     *
     * @var QColl
     */
    protected $_controllers;

    /**
     * 模块所有控制器的名字
     *
     * @var array of controller name
     */
    protected $_controllers_name;

    /**
     * 该模块的控制器生成器
     *
     * @var QGenerator_Controller
     */
    protected $_controller_generator;

    /**
     * 模块所有模型的反射
     *
     * @var QColl
     */
    protected $_models;

    /**
     * 模块所有模型的名字
     *
     * @var array of model name
     */
    protected $_models_name;

    /**
     * 该模块的模型生成器
     *
     * @var QGenerator_Model
     */
    protected $_model_generator;

    /**
     * 构造函数
     *
     * @param QReflection_Application $app
     * @param string $module_name
     * @param string $module_dir
     */
    function __construct(QReflection_Application $app, $module_name, $module_dir)
    {
        if (!is_dir($module_dir))
        {
            throw new QReflection_ModuleNotExistsException($app, $module_name);
        }

        if (empty($module_name))
        {
            $module_name = QReflection_Application::DEFAULT_MODULE_NAME;
        }

        $this->_app = $app;
        $this->_module_dir = $module_dir;
        $this->_module_name = $module_name;
    }

    /**
     * 返回该模块反射所属的应用程序反射
     *
     * @return QReflection_Application
     */
    function app()
    {
        return $this->_app;
    }

    /**
     * 返回该模块的模块名
     *
     * @return string
     */
    function moduleName()
    {
        return $this->_module_name;
    }

    /**
     * 返回该模亏所在目录
     *
     * @return string
     */
    function moduleDir()
    {
        return $this->_module_dir;
    }

    /**
     * 指示该模块是否是默认模块
     *
     * @return boolean
     */
    function isDefaultModule()
    {
        return $this->_module_name == QReflection_Application::DEFAULT_MODULE_NAME;
    }

    /**
     * 获得该模块所有控制器的名字
     *
     * @return array of controller name
     */
    function controllersName()
    {
        if (is_null($this->_controllers_name))
        {
            $dir = rtrim($this->_module_dir, '/\\') . DS . 'controller';
            $this->_controllers_name = array();
            $files = Helper_FileSys::recursionGlob($dir, '*_controller.php');
            sort($files, SORT_STRING);

            $dir = rtrim(realpath($dir), '/\\') . DS;
            $offset = strlen($dir);

            foreach ($files as $file)
            {
                $file = realpath($file);
                $name = substr(substr($file, $offset), 0, -15);

                $names = explode(DS, $name);
                $namespace = isset($names[1]) ? "{$names[0]}::" : '';
                $controller_name = array_pop($names);
                $this->_controllers_name[] = "{$namespace}{$controller_name}";
            }
        }

        return $this->_controllers_name;
    }

    /**
     * 获得该模块所有控制器的反射
     *
     * @return array of QReflection_Controller
     */
    function controllers()
    {
        if (is_null($this->_controllers))
        {
            $this->_controllers = new QColl('QReflection_Controller');
            foreach ($this->controllersName() as $name)
            {
                $this->_controllers[$name] = new QReflection_Controller($this, $name);
            }
        }

        return $this->_controllers;
    }

    /**
     * 获得指定名字的控制器反射
     *
     * @param string $udi
     *
     * @return QReflection_Controller
     */
    function controller($udi)
    {
        $controllers = $this->controllers();
        if (isset($controllers[$udi]))
        {
            return $controllers[$udi];
        }
        throw new QReflection_ModuleNotExistsException($this, $controller_name);
    }

    /**
     * 检查是否存在指定的控制器
     *
     * @param string $controller_name
     * @param string $namespace
     *
     * @return boolean
     */
    function hasController($controller_name, $namespace = null)
    {
        $udi = strtolower($controller_name);
        $names = explode('::', $controller_name);
        if (!isset($names[1]))
        {
            if ($namespace) $udi = strtolower($namespace) . '::' . $udi;
        }

        $names = $this->controllersName();
        return in_array($udi, $names);
    }

    /**
     * 获得该模块所有模型的名字
     *
     * @return array of model name
     */
    function modelsName()
    {
        if (is_null($this->_models_name))
        {
            $dir = rtrim($this->_module_dir, '/\\') . DS . 'model';
            $files = Helper_FileSys::recursionGlob($dir, '*.php');
            $this->_models_name = array();
            foreach ($files as $file)
            {
                $info = QReflection_Model::testModelFile($file);
                if ($info == false) continue;
                $this->_models_name[$file] = $info['class'];
            }
            asort($this->_models_name, SORT_STRING);
        }

        return $this->_models_name;
    }

    /**
     * 获得该模块所有模型的反射
     *
     * @return QColl
     */
    function models()
    {
        if (is_null($this->_models))
        {
            $this->_models = new QColl('QReflection_Model');
            foreach ($this->modelsName() as $path => $class)
            {
                $this->_models[$class] = new QReflection_Model($this, $class, $path);
            }
        }
        return $this->_models;
    }

    /**
     * 获得指定名字模型的反射
     *
     * @param string $model_name
     *
     * @retrun QReflection_Model
     */
    function model($model_name)
    {
        return $this->models[$model_name];
    }

    /**
     * 检查是否存在指定的模型
     *
     * @param string $model_name
     *
     * @return boolean
     */
    function hasModel($model_name)
    {
        $names = $this->modelsName();
        return in_array($model_name, $names);
    }

    /**
     * 生成属于该模块的控制器，返回包含创建信息的数组
     *
     * @param string $controller_name
     * @param string $namespace
     *
     * @return QGenerator_Controller
     */
    function generateController($controller_name, $namespace = null)
    {
        return $this->controllerGenerator()->generate($controller_name, $namespace);
    }

    /**
     * 返回对应该模块的控制器生成器
     *
     * @return QGenerator_Controller
     */
    function controllerGenerator()
    {
        if (is_null($this->_controller_generator))
        {
            $this->_controller_generator = new QGenerator_Controller($this);
        }
        return $this->_controller_generator;
    }

    /**
     * 生成属于该模块的模型，返回包含创建信息的数组
     *
     * @param string $model_name
     * @param string $table_name
     * @param QDB_Adapter_Abstract $dbo
     *
     * @return QGenerator_Controller
     */
    function generateModel($model_name, $table_name, $dbo = null)
    {
        return $this->modelGenerator()->generate($model_name, $table_name, $dbo);
    }

    /**
     * 返回对应该模块的模型生成器
     *
     * @return QGenerator_Model
     */
    function modelGenerator()
    {
        if (is_null($this->_model_generator))
        {
            $this->_model_generator = new QGenerator_Model($this);
        }
        return $this->_model_generator;
    }

}



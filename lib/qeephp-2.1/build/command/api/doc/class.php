<?php

class API_Doc_Class extends API_Doc_Abstract
{
    /**
     * 类所属的包
     *
     * @var API_Doc_Package
     */
    public $package;

    /**
     * 介绍信息
     *
     * @var string
     */
    public $summary;

    /**
     * 详细描述信息
     *
     * @var string
     */
    public $description;

    /**
     * 作者信息
     *
     * @var string
     */
    public $author;

    /**
     * 版本信息
     *
     * @var string
     */
    public $version;

    /**
     * 类名称
     *
     * @var string
     */
    public $name;

    /**
     * 该类原生方法的数量
     *
     * @var int
     */
    public $native_methods_count = 0;

    /**
     * 类的所有方法
     *
     * @var array of API_Doc_Method
     */
    public $methods;

    /**
     * 该类原生属性的数量
     *
     * @var int
     */
    public $native_properties_count = 0;

    /**
     * 类的所有属性
     *
     * @var array of API_Doc_Property
     */
    public $properties;

    /**
     * 类的所有常量
     *
     * @var array of API_Doc_Constant
     */
    public $constants;

    /**
     * 类实现的接口
     *
     * @var array of API_Doc_Class
     */
    public $interfaces;

    /**
     * 父类
     *
     * @var API_Doc_Class
     */
    public $parent;

    /**
     * 是否是接口
     *
     * @var boolean
     */
    public $is_interface;

    /**
     * 是否是抽象类
     *
     * @var boolean
     */
    public $is_abstract;

    /**
     * 是否是最终类
     *
     * @var boolean
     */
    public $is_final;

    /**
     * 是否可迭代
     *
     * @var boolean
     */
    public $is_iterateable;

    /**
     * 类定义文件名
     *
     * @var string
     */
    public $filename;

    /**
     * 起始行数
     *
     * @var int
     */
    public $start_line;

    /**
     * 结束行数
     *
     * @var int
     */
    public $end_line;

    private $_reflection_class;

    /**
     * 构造函数
     *
     * @param mixed $class
     */
    private function __construct($class)
    {
        if (!($class instanceof ReflectionClass))
        {
            $class = new ReflectionClass($class);
        }

        $this->name           = $class->getName();
        $this->filename       = $class->getFilename();
        $this->is_interface   = $class->isInterface();
        $this->is_abstract    = $class->isAbstract();
        $this->is_final       = $class->isFinal();
        $this->is_iterateable = $class->isIterateable();
        $this->start_line     = $class->getStartLine();
        $this->end_line       = $class->getEndLine();

        $this->_reflection_class = $class;
    }

    private function _processing()
    {
        $this->methods = array();
        $class = $this->_reflection_class;
        foreach ($class->getMethods() as $method_r)
        {
            $method = new API_Doc_Method($method_r);
            $method->is_inherited = ($method_r->getDeclaringClass() != $class);
            if (!$method->is_inherited) $this->native_methods_count++;
            $this->methods[] = $method;
        }

        $this->properties = array();
        foreach ($class->getProperties() as $property_r)
        {
            $property = new API_Doc_Property($property_r);
            $property->is_inherited = ($property_r->getDeclaringClass() != $class);
            if (!$property->is_inherited) $this->native_properties_count++;
            $this->properties[] = $property;
        }

        $this->constants = array();
        foreach ($class->getConstants() as $name => $value)
        {
            $this->constants[] = new API_Doc_Constant($this, $name, $value);
        }

        foreach ($class->getInterfaces() as $interface)
        {
            $this->interfaces[] = new API_Doc_Class($interface);
        }

        list($comment, $meta) = $this->_docComment($class->getDocComment());
        $this->_processDescription($comment);
        $this->_processMeta($meta);

        $parent = $class->getParentClass();
        if ($parent)
        {
            $this->parent = API_Doc_Class::instance($parent);
        }
    }

    static function instance($class)
    {
        static $instances = array();

        if (is_object($class))
        {
            $name = $class->getName();
        }
        else
        {
            $name = $class;
        }
        if (!isset($instances[$name]))
        {
            $instances[$name] = new API_Doc_Class($class);
            $instances[$name]->_processing();
        }
        return $instances[$name];
    }
}


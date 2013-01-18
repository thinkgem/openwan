<?php

class API_Doc_Method extends API_Doc_Abstract
{
    /**
     * 名称
     *
     * @var string
     */
    public $name;

    /**
     * 方法的签名
     *
     * @var string
     */
    public $signature;

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
     * 所属的类
     *
     * @var API_Doc_Class
     */
    public $declaring_class;

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
     * 是否是公开方法
     *
     * @var boolean
     */
    public $is_public;

    /**
     * 是否是私有方法
     *
     * @var boolean
     */
    public $is_private;

    /**
     * 是否是保护方法
     *
     * @var boolean
     */
    public $is_protected;

    /**
     * 是否是静态方法
     *
     * @var boolean
     */
    public $is_static;

    /**
     * 是否是构造函数
     *
     * @var boolean
     */
    public $is_constructor;

    /**
     * 是否是析构函数
     *
     * @var boolean
     */
    public $is_destructor;

    /**
     * 是否是从父类继承的方法
     *
     * @var boolean
     */
    public $is_innherited;

    /**
     * 所在文件
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

    /**
     * 方法参数
     *
     * @var array of API_Doc_Parameter
     */
    public $parameters;

    /**
     * 返回类型
     *
     * @var string
     */
    public $return_type;

    /**
     * 返回值说明
     *
     * @var string
     */
    public $return_comment;

    /**
     * 元信息
     *
     * @var array
     */
    public $meta;

    /**
     * 该方法可能抛出的异常
     *
     * @var array
     */
    public $throws = array();

    /**
     * 构造函数
     *
     * @param ReflectionMethod $method
     */
    function __construct(ReflectionMethod $method)
    {
        $this->name = $method->getName();
        $this->declaring_class = API_Doc_Class::instance($method->getDeclaringClass());
        $this->is_abstract    = $method->isAbstract();
        $this->is_final       = $method->isFinal();
        $this->is_public      = $method->isPublic();
        $this->is_private     = $method->isPrivate();
        $this->is_protected   = $method->isProtected();
        $this->is_static      = $method->isStatic();
        $this->is_constructor = $method->isConstructor();
        $this->is_destructor  = $method->isDestructor();
        $this->filename       = $method->getFilename();
        $this->start_line     = $method->getStartLine();
        $this->end_line       = $method->getEndLine();

        $this->parameters = array();
        foreach ($method->getParameters() as $parameter)
        {
            $this->parameters[$parameter->getName()] = new API_Doc_Parameter($this, $parameter);
        }

        list($comment, $meta) = $this->_docComment($method->getDocComment());
        $this->_processDescription($comment);
        $this->_processMeta($meta);

        $this->signature = '{{' . $method->getDeclaringClass()->getName() . '::' . $this->name . '|<strong>' . $this->name . '</strong>}}';
        $this->signature .= '(';
        $paras = array();
        foreach (array_keys($this->parameters) as $parname)
        {
            $paras[] = '$' . $parname;
        }
        $this->signature .= implode(', ', $paras) . ')';
        
        if ($this->return_type)
        {
            $this->signature = $this->return_type . ' ' . $this->signature;
        }
        else
        {
            $this->signature = 'void ' . $this->signature;
        }

        $modifier = implode(' ', Reflection::getModifierNames($method->getModifiers()));
        if ($modifier) $this->signature = $modifier . ' ' . $this->signature;
    }

}


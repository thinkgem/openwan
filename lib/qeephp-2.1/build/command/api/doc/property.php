<?php

class API_Doc_Property extends API_Doc_Abstract
{
    /**
     * 名称
     *
     * @var string
     */
    public $name;

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

    public $type_hint;

    /**
     * 所属的类
     *
     * @var API_Doc_Class
     */
    public $declaring_class;

    /**
     * 是否是公开属性
     *
     * @var boolean
     */
    public $is_public;

    /**
     * 是否是私有属性
     *
     * @var boolean
     */
    public $is_private;

    /**
     * 是否是保护属性
     *
     * @var boolean
     */
    public $is_protected;

    /**
     * 是否是静态属性
     *
     * @var boolean
     */
    public $is_static;

    /**
     * 是否是从父类继承的属性
     *
     * @var boolean
     */
    public $is_inherited;

    /**
     * 元信息
     *
     * @var array
     */
    public $meta;

    function __construct(ReflectionProperty $property)
    {
        $this->name         = $property->getName();
        $this->is_public    = $property->isPublic();
        $this->is_private   = $property->isPrivate();
        $this->is_protected = $property->isProtected();
        $this->is_static    = $property->isStatic();
        $this->declaring_class = API_Doc_Class::instance($property->getDeclaringClass());

        list($comment, $meta) = $this->_docComment($property->getDocComment());
        $this->_processDescription($comment);
        $this->_processMeta($meta);
    }
}



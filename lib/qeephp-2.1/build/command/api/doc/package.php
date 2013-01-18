<?php

class API_Doc_Package extends API_Doc_Abstract
{
    /**
     * 包的名字
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

    /**
     * 这个包里面的类
     *
     * @var array of API_Doc_Class
     */
    public $classes = array();


    private function __construct($name)
    {
        $this->name = $name;
    }

    static function instance($name)
    {
        static $packages = array();

        if (!isset($packages[$name]))
        {
            $packages[$name] = new API_Doc_Package($name);
        }

        return $packages[$name];
    }

    function __toString()
    {
        return $this->name;
    }
}


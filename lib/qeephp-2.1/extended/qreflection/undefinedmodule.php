<?php

class QReflection_UndefinedModule extends QException
{
    /**
     * 所属应用的反射
     *
     * @var QReflection_Application
     */
    public $reflection_app;

    /**
     * 模块名
     *
     * @var string
     */
    public $reflection_module_name;

    function __construct(QReflection_Application $app, $module_name)
    {
        $this->reflection_app = $app;
        $this->reflection_module_name = $module_name;

        // LC_MSG: Undefined module "%s" on application "%s".
        parent::__construct(__('Undefined module "%s" on application "%s".', $module_name, $app->APPID()));
    }
}


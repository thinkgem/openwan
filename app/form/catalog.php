<?php
// $Id: catalog.php 895 2010-03-23 05:36:29Z thinkgem $

/**
 * 编目属性设置表单
 * @author WangZhen <thinkgem@163.com>
 */
class Form_Catalog extends QForm {

    function __construct($action) {
        // 调用父类的构造函数
        parent::__construct("form1", $action);
        // 从配置文件载入表单
        $filename = rtrim(dirname(__FILE__), '/\\') . DS . 'catalog_form.yaml';
        $this->loadFromConfig(Helper_YAML::loadCached($filename));
        // 添加表单验证
        //$this->addValidations(Catalog::meta());
    }
}

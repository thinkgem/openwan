<?php
// $Id: changepassword.php 895 2010-03-23 05:36:29Z thinkgem $

/**
 * 修改密码表单
 * @author WangZhen <thinkgem@163.com>
 */
class Form_ChangePassword extends QForm {

    function __construct($action) {
    // 调用父类的构造函数
        parent::__construct("form_changepassword", $action);

        // 从配置文件载入表单
        $filename = rtrim(dirname(__FILE__), '/\\') . DS . 'changepassword_form.yaml';
        $this->loadFromConfig(Helper_YAML::loadCached($filename));
        $this['old_password']->addValidations(array($this, 'checkPasswordLen'), '密码长度只能在4-32位之间');
        $this['new_password']->addValidations(array($this, 'checkPasswordLen'), '密码长度只能在4-32位之间');
        $this['new_password2']->addValidations(array($this, 'checkSecPasswd'), '两次输入的密码必须一致');
        //$this->addValidations(Users::meta());
    }
    /**
     * 检查两次输入的密码是否一致
     */
    function checkPasswordLen($password) {
        return (strlen($password) >= 4 && strlen($password) <= 32);
    }
    /**
     * 检查两次输入的密码是否一致
     */
    function checkSecPasswd($new_password) {
        return ($this['new_password2']->value == $this['new_password']->value);
    }
}

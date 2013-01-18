<?php
// $Id: default_controller.php 895 2010-03-23 05:36:29Z thinkgem $

/**
 * 默认控制器
 * @author WangZhen <thinkgem@163.com>
 */
class Controller_Default extends Controller_Abstract
{
    protected function _before_execute(){
        parent::_before_execute();
        $this->_pathway->addStep('首页');        
    }
    function actionIndex(){
        $this->_pathway->addStep('首页');
        return $this->_redirect(url("admin::default/index"));
    }
    
}


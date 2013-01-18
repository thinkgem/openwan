<?php
// $Id: abstract.php 895 2010-03-23 05:36:29Z thinkgem $

/**
 * 应用程序的公共控制器基础类
 * @author WangZhen <thinkgem@163.com>
 */
abstract class Controller_Admin_Abstract extends Controller_Abstract
{
        protected function _before_execute(){
            parent::_before_execute();
            //$this->_pathway->addStep('管理',url('admin::default/index'));
        }
}


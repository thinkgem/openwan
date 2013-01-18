<?php
// $Id: default_controller.php 895 2010-03-23 05:36:29Z thinkgem $

/**
 * Controller_Admin_Default 控制器
 * @author WangZhen <thinkgem@163.com>
 */
class Controller_Admin_Default extends Controller_Admin_Abstract
{
        protected function _before_execute(){
            parent::_before_execute();
            $this->_pathway->addStep('管理首页');
        }

	function actionIndex(){
            $this->_pathway->addStep('管理首页');
            if ($this->_app->currentUserRoles()){          
//                return $this->_redirect(url("admin::fileUpload/index"));
            }else{
                return $this->_redirect(url("admin::default/login"));
            }
	}
        /**
         * 用户登录
         */
        function actionLogin(){
            $this->_pathway->addStep('登录');
            $username = $this->_context->username;
            $password = $this->_context->password;
            if(isset($username) && isset($password)){
                try{
                    $user = Users::meta()->validateLogin($username, $password);
                    $aclData = $user->aclData();
                    if(!$aclData['enabled']){
                        $this->_view['error'] = '用户被锁定，请与管理员联系。';
                    }else{
                        $this->_app->changeCurrentUser($aclData, $user->roles);
                        return $this->_redirect(url("admin::default/index"));
                    }
                }catch (AclUser_Exception $ex){
                    $this->_view['error'] = '用户名或密码错误，请重新输入。';
                }
            }
        }
        /**
         * 用户退出
         */
        function actionLogout(){
            $this->_pathway->addStep('退出');
            $this->_app->cleanCurrentUser();
            return $this->_redirect(url("default::default/index"));
        }
        
}



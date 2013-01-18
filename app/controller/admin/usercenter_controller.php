<?php
// $Id: usercenter_controller.php 895 2010-03-23 05:36:29Z thinkgem $

/**
 * Controller_Admin_UserCenter 控制器
 * @author WangZhen <thinkgem@163.com>
 */
class Controller_Admin_UserCenter extends Controller_Admin_Abstract
{
        protected function _before_execute(){
            parent::_before_execute();
            $this->_pathway->addStep('个人中心');
        }
	function actionIndex(){
            $this->_pathway->addStep('个人中心');
	}
        /**
         * 修改个人信息
         */
        function actionChangeInfo(){
            $this->_pathway->addStep('个人信息');
            $currentUser = $this->_app->currentUser();
            $user = Users::find()->getById($currentUser['id']);
            $form = new Form_User(url('admin::usercenter/changeInfo'));
            $form->element('username')->set('readonly', 'true');
            $form->remove('password');
            $form->element('group_id')->items = Groups::find('id=?',$user->group_id)->order('weight desc')->getAll()->toHashMap('id', 'name');
            $form->element('level_id')->items = Levels::find('weight=?',$user->level_id)->order('weight desc')->getAll()->toHashMap('weight', 'name');
            $form->remove('enabled');
            $form->add(QForm::ELEMENT, 'id', array('_ui' => 'hidden', 'value' => $currentUser['id']));
            if ($this->_context->isPOST() && $form->validate($_POST)){
                try {
                    $user->changeProps($form->values());
                    $user->save();
                    return "{msg:'编辑成功'}";
                }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                    $form->invalidate($ex);
                }
            }else{
                $form->import($user);
            }
            $form->add(QForm::ELEMENT, 'reg_at', array('_ui' => 'textbox', '_label' => '注册时间', 'value' => date('Y-m-d', $user->register_at), 'class' => 'txt w200', 'readonly' => 'true'));
            $form->add(QForm::ELEMENT, 'reg_ip', array('_ui' => 'textbox', '_label' => '注册IP', 'value' => $user->register_ip, 'class' => 'txt w200', 'readonly' => 'true'));
            $form->add(QForm::ELEMENT, 'log_at', array('_ui' => 'textbox', '_label' => '最后登录时间', 'value' => $user->login_at==0?'0000-00-00':date('Y-m-d', $user->login_at), 'class' => 'txt w200', 'readonly' => 'true'));
            $form->add(QForm::ELEMENT, 'log_ip', array('_ui' => 'textbox', '_label' => '最后登录IP', 'value' => $user->login_ip, 'class' => 'txt w200', 'readonly' => 'true'));
            $form->add(QForm::ELEMENT, 'log_count', array('_ui' => 'textbox', '_label' => '登录次数', 'value' => $user->login_count, 'class' => 'txt w200', 'readonly' => 'true'));
            $this->_view['form'] = $form;
        }
        /**
         * 修改密码
         */
        function actionChangePassword(){
            $this->_pathway->addStep('修改密码');
            $form = new Form_ChangePassword(url('admin::usercenter/changepassword'));
            if($this->_context->isPOST() && $form->validate($_POST)) {
                try {
                    $currentUser = $this->_app->currentUser();
                    Users::meta()->changePassword($currentUser['username'], $form['new_password']->value, $form['old_password']->value);
                    return '{msg:"您的登录密码已经成功修改，下次登录请使用新密码。"}';
                }catch (AclUser_WrongPasswordException $ex){
                    $form['old_password']->invalidate('您输入的旧密码不正确');
                }catch (QDB_ActiveRecord_ValidateFailedException $ex){
                    $form->import($ex);
                    //foreach ($ex->validate_errors as $key => $value) {
                    //    $form[$key]->invalidate(sprintf($value[$key], $ex->validate_obj[$key]));
                    //}
                }
            }
            $this->_view['form'] = $form;
        }
}



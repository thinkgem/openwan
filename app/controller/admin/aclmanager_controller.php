<?php
// $Id: aclmanager_controller.php 895 2010-03-23 05:36:29Z thinkgem $

/**
 * Controller_Admin_AclManager 访问控制管理控制器
 * @author WangZhen <thinkgem@163.com>
 */
class Controller_Admin_AclManager extends Controller_Admin_Abstract
{
        protected function _before_execute(){
            parent::_before_execute();
            $this->_pathway->addStep('访问控制管理');
        }
	function actionIndex(){
            $this->_pathway->addStep('访问控制管理');
	}
        /**
         * 用户列表
         */
        function actionUser(){
            $this->_pathway->addStep('用户管理');
            if ($this->_context->isPOST()){
                // 分页查询内容列表
                $page = intval($this->_context->page);
                if ($page < 1) $page = 1;
                $select = Users::find();
                $select->limitPage($page, 12);
                //设置查询条件
                $username = $this->_context->username;
                $nickname = $this->_context->nickname;
                $groups = $this->_context->groups;
                $levels = $this->_context->levels;
                if(isset($username)){
                    $username = str_replace('%', '', $username);
                    $username = str_replace('*', '%', $username);
                    $username = Q::normalize($username);
                    foreach ($username as $v){
                        $select->orWhere('username like ?',$v);
                    }
                }
                if(isset($nickname)){
                    $nickname = str_replace('%', '', $nickname);
                    $nickname = str_replace('*', '%', $nickname);
                    $nickname = Q::normalize($nickname);
                    foreach ($nickname as $v){
                        $select->orWhere('nickname like ?',$v);
                    }
                }
                if(isset($groups) && !in_array('all', $groups)){
                    $select->where('group_id in (?)', $groups);
                }
                if(isset($levels) && !in_array('all', $levels)){
                    $select->where('level_id in (?)', $levels);
                }
                // 将分页信息和查询到的数据传递到视图
                $this->_view['pagination'] = $select->getPagination();
                $this->_view['users'] = $select->getAll();
            }else{
                $this->_view['groups'] = Groups::find('enabled=1')->order('weight desc')->getAll();
                $this->_view['levels'] = Levels::find('enabled=1')->order('weight desc')->getAll();
            }
        }
        /**
         * 添加用户
         */
        function actionUserAdd(){
            $this->_pathway->addStep('添加用户');
            $form = new Form_User(url('admin::aclmanager/userAdd'));
            $form->element('group_id')->items = Groups::find('enabled=1')->order('weight desc')->getAll()->toHashMap('id', 'name');
            $form->element('level_id')->items = Levels::find('enabled=1')->order('weight desc')->getAll()->toHashMap('weight', 'name');
            if ($this->_context->isPOST() && $form->validate($_POST)){
                try{
                    $user = new Users($form->values());
                    $user->changePropForce('username', $form->element('username')->value());
                    $user->changePropForce('group_id', $form->element('group_id')->value());
                    $user->changePropForce('level_id', $form->element('level_id')->value());
                    $user->save();
                    return "{id:0,msg:'添加成功'}";
                }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                    $form->invalidate($ex);
                }catch (AclUser_DuplicateUsernameException $ex){
                    $form->element('username')->invalidate("您要添加的帐号 {$user->username} 已经存在了");
                }
            }
            $this->_view['form'] = $form;
        }
        /**
         * 添加用户
         */
        function actionUserEdit(){
            $this->_pathway->addStep('编辑用户');
            $id = $this->_context->id;
            $user = Users::find()->getById($id);
            $form = new Form_User(url('admin::aclmanager/userEdit'));
            $form->element('username')->set('readonly', 'true');
            $form->element('password')->set('_tips', '密码长度只能在3-32位之间，如果不更改密码此处请留空');
            $form->element('group_id')->items = Groups::find('enabled=1')->order('weight desc')->getAll()->toHashMap('id', 'name');
            $form->element('level_id')->items = Levels::find('enabled=1')->order('weight desc')->getAll()->toHashMap('weight', 'name');
            $form->add(QForm::ELEMENT, 'id', array('_ui' => 'hidden', 'value' => $id));            
            if ($this->_context->isPOST()){
                if ($_POST['password'] == '') $form->remove('password');
                if($form->validate($_POST)){
                    try {
                        $user->changePropForce('group_id', $form->element('group_id')->value());
                        $user->changePropForce('level_id', $form->element('level_id')->value());
                        $user->changeProps($form->values());
                        $user->save();
                        return "{id:{$id},msg:'编辑成功'}";
                    }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                        $form->invalidate($ex);
                    }
                }else{
                    $form->import($user);
                }
            }else{
                $form->import($user);
            }
            $form->element('password')->set('value', '');
            $form->add(QForm::ELEMENT, 'reg_at', array('_ui' => 'textbox', '_label' => '注册时间', 'value' => date('Y-m-d', $user->register_at), 'class' => 'txt w200', 'readonly' => 'true'));
            $form->add(QForm::ELEMENT, 'reg_ip', array('_ui' => 'textbox', '_label' => '注册IP', 'value' => $user->register_ip, 'class' => 'txt w200', 'readonly' => 'true'));
            $form->add(QForm::ELEMENT, 'log_at', array('_ui' => 'textbox', '_label' => '最后登录时间', 'value' => $user->login_at==0?'0000-00-00':date('Y-m-d', $user->login_at), 'class' => 'txt w200', 'readonly' => 'true'));
            $form->add(QForm::ELEMENT, 'log_ip', array('_ui' => 'textbox', '_label' => '最后登录IP', 'value' => $user->login_ip, 'class' => 'txt w200', 'readonly' => 'true'));
            $form->add(QForm::ELEMENT, 'log_count', array('_ui' => 'textbox', '_label' => '登录次数', 'value' => $user->login_count, 'class' => 'txt w200', 'readonly' => 'true'));
            $this->_view['form'] = $form;
        }
        /**
         * 删除用户
         */
        function actionUserDel(){
            $this->_pathway->addStep('删除用户');
            $id = $this->_context->id;
            $currentUser = $this->_app->currentUser();
            if ($id == $currentUser['id'] || $id == 1){
                return 'false';
            }
            Users::meta()->deleteWhere('id=?',$id);
            return 'true';
//            $user = Users::find()->getById($id);
//            if (!$user->isNewRecord()){
//                $user->destroy();
//                return 'true';
//            }
//            return 'false';
        }
        /**
         * 管理 用户组
         */
        function actionGroup(){
            $this->_pathway->addStep('用户组管理');
            if ($this->_context->isPOST()){ 
                foreach ($_POST['id'] as $key => $value) {
                    if (!isset($_POST['chk']) || !in_array($value, $_POST['chk'])){
                        $arr = array(
                            'id' => $value,
                            'name' => $_POST['name'][$key],
                            'description' => $_POST['description'][$key],
                            'quota' => $_POST['quota'][$key],
                            'weight' => $_POST['weight'][$key],
                            'enabled' => $_POST['enabled'][$key]
                        );
                        try {
                            $group = Groups::find()->getById($_POST['id'][$key]);
                            $group->changeProps($arr);
                            $group->save();
                        }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                        }
                    }
                }
                if (isset($_POST['chk'])){
                    //Groups::meta()->destroyWhere("id in (?) and id > 3",$_POST['chk']);
                    Groups::meta()->deleteWhere("id in (?) and id > 3",$_POST['chk']);
                    GroupsHasCategory::meta()->deleteWhere("group_id in (?) and group_id > 3",$_POST['chk']);
                    GroupsHasRoles::meta()->deleteWhere("group_id in (?) and group_id > 3",$_POST['chk']);
                }                
            }
            // 分页查询内容列表
            $page = intval($this->_context->page);
            if ($page < 1) $page = 1;
            $select = Groups::find()->order('weight desc');
            $select->limitPage($page, 10);
            // 将分页信息和查询到的数据传递到视图
            $this->_view['pagination'] = $select->getPagination();
            $this->_view['groups'] = $select->getAll();
        }
        /**
         * 详情 用户组
         */
        function actionGroupView(){
            $this->_pathway->addStep('用户组详情');
            $id = $this->_context->id;
            $group = Groups::find()->getById($id);
            $this->_view['group'] = $group;
        }
        /**
         * 绑定权限 用户组
         */
        function actionGroupBind(){
            $this->_pathway->addStep('用户组绑定权限');
            $id = $this->_context->id;
            $group = Groups::find()->getById($id);
            if ($this->_context->isPOST()){
                try{
                    //绑定角色
                    $role_ids = $this->_context->role_ids;
                    $role_ids = $role_ids != '' ? Q::normalize($role_ids) : '0';
                    $group->roles = Roles::find("id in (?)", $role_ids)->getAll();
                    //绑定分类
                    $category_ids = $this->_context->category_ids;
                    $category_ids = $category_ids != '' ? Q::normalize($category_ids) : '0';
                    $group->categorys = Category::find("id in (?)", $category_ids)->getAll();
                    //保存修改
                    $group->save();
                }catch (QValidator_ValidateFailedException $ex){
                }
            }
            //获得绑定的角色编号
            $role = Roles::find('enabled=1')->order('weight desc')->getAll();
            $role_ids = array();
            foreach ($group->roles as $v){
                $role_ids[] = $v->id;
            }
            //获得绑定的分类编号
            $category = Category::find('enabled=1')->order('weight desc')->getAll();
            $category_ids = array();
            foreach ($group->categorys as $v){
                $category_ids[] = $v->id;
            }
            //将需要数据发送到视图
            $this->_view['group'] = $group;            
            $this->_view['role'] = $role;
            $this->_view['role_ids'] = $role_ids;
            $this->_view['category'] = $category;
            $this->_view['category_ids'] = $category_ids;
        }
        /**
         * 管理 角色
         */
        function actionRole(){
            $this->_pathway->addStep('管理角色');
            if ($this->_context->isPOST()){
                foreach ($_POST['id'] as $key => $value) {
                    if (!isset($_POST['chk']) || !in_array($value, $_POST['chk'])){
                        $arr = array(
                            'id' => $value,
                            'name' => $_POST['name'][$key],
                            'description' => $_POST['description'][$key],
                            'weight' => $_POST['weight'][$key],
                            'enabled' => $_POST['enabled'][$key]
                        );
                        try {
                            $role = Roles::find()->getById($_POST['id'][$key]);
                            $role->changeProps($arr);
                            $role->save();
                        }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                        }
                    }
                }
                if (isset($_POST['chk'])){
                    //Roles::meta()->destroyWhere("id in (?) and id > 6",$_POST['chk']);
                    Roles::meta()->deleteWhere("id in (?) and id > 6",$_POST['chk']);
                    GroupsHasRoles::meta()->deleteWhere("role_id in (?) and role_id > 6",$_POST['chk']);
                }            
            }
            // 分页查询内容列表
            $page = intval($this->_context->page);
            if ($page < 1) $page = 1;
            $select = Roles::find()->order('weight desc');
            $select->limitPage($page, 10);
            // 将分页信息和查询到的数据传递到视图
            $this->_view['pagination'] = $select->getPagination();
            $this->_view['roles'] = $select->getAll();
        }
        /**
         * 详情 角色
         */
        function actionRoleView(){
            $this->_pathway->addStep('角色详情');
            $id = $this->_context->id;
            $role = Roles::find()->getById($id);
            $this->_view['role'] = $role;
        }
        /**
         * 角色绑定权限
         */
        function actionRoleBind(){
            $this->_pathway->addStep('角色绑定权限');
            $id = $this->_context->id;
            $role = Roles::find()->getById($id);
            if ($this->_context->isPOST()){                
                try{
                    $permission_ids = $this->_context->permission_ids;
                    $permission_ids = $permission_ids != '' ? Q::normalize($permission_ids) : '0';
                    $role->permissions = Permissions::find("id in (?)",$permission_ids)->getAll();
                    $role->save();
                }catch (QValidator_ValidateFailedException $ex){
                }
            }
            $permission = Helper_Permissions::getAllPermissions();
            $permission_ids = array();
            foreach ($role->permissions as $v){
                $permission_ids[] = $v->id;
            }
            $this->_view['role'] = $role;            
            $this->_view['permissions'] = $permission;
            $this->_view['permission_ids'] = $permission_ids;
        }
        /**
         * 权限列表
         */
        function actionPermission(){
            $this->_pathway->addStep('管理权限');
            if ($this->_context->isPOST() ){
                foreach ($this->_context->rbac as $key=>$value) {
                    try{
                        $rs = Permissions::find()->getById($key);
                        $rs->rbac = $value;
                        $rs->aliasname = $_POST['aliasname'][$key];
                        $rs->save();
                    }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                    }
                }
            }
            // 分页查询
            $page = intval($this->_context->page);
            if ($page < 1) $page = 1;
            // 构建查询条件
            $list_namespace  = $this->_context->list_namespace;
            $list_controller = $this->_context->list_controller;
            $condition = $url_args = array();
            if ( !empty($list_namespace)){
                $condition['namespace'] = $list_namespace;
                $url_args['list_namespace'] = $list_namespace;
            }else{
                $url_args['list_namespace'] = '';
            }
            if ( !empty($list_controller)){
                $condition['controller'] = $list_controller;
                $url_args['list_controller'] = $list_controller;
            }
            $this->_view['url_args'] = $url_args;
            // 构造查询对象
            $select = Permissions::find($condition);
            $select -> limitPage($page, 10);
            // 将分页信息和查询到的数据传递到视图
            $this->_view['pagination']  = $select->getPagination();
            $this->_view['permissions'] = $select->getAll();
            // 构建二级联动下拉框
            $this->_view['ncArr'] = Helper_Permissions::getNamespaceControllerArrs();
        }        
        /**
         * 刷新 权限列表
         */
        function actionPermissionRefresh(){
            $this->_pathway->addStep('更新权限列表');
            $this->md_perManager = new Helper_Permissions;
            return $this->md_perManager->updatePermissions(Q::ini('app_config/APP_DIR') . '/controller/') ? '更新成功' : '更新失败';
        }
        /**
         * 生成ACL文件 权限列表
         */
        function actionMakeAclFile(){
            $this->_pathway->addStep('更新权限文件');
            //指定管理员角色或者最高权限角色
            $root_role = 'ADMIN';
            //取得数据表中的所有权限节点的数据
            //这种方法不能取得相关的用户角色的数据，直接生成数组  $nca = Permissions::find()->asArray()->getAll();
            $nca = Permissions::find()->getAll();
            // 将取得的数据数组进行重新构建        // ACL_NULL ACL_EVERYONE ACL_HAS_ROLE ACL_NO_ROLE
            $acl_arr = array();
            //默认命名空间允许管理员访问，注意要小写
            $acl_arr['all_controllers']['allow'] = '"'.$root_role.'"';
            foreach ( $nca as $value){
                $value['namespace'] = strtolower($value['namespace']);
                $value['controller'] = strtolower($value['controller']);
                $value['action'] = strtolower($value['action']);
                $roles_arr = $value->roles->toArray();                
                if ( $value['namespace'] != 'default'){
                    $acl_arr[$value['namespace']]['all_controllers']['allow'] = '"'.$root_role.'"';
                }
                if ($value['rbac'] != 'ACL_NULL'){
                    if ( $value['namespace']=='default'){
                        $acl_arr[$value['controller']]['actions'][$value['action']]['allow'] = $value['rbac'];
                    }else{
                        $acl_arr[$value['namespace']][$value['controller']]['actions'][$value['action']]['allow'] = $value['rbac'];
                    }
                }elseif(!empty($roles_arr)){
                    $roles = array();
                    foreach( $roles_arr as $value2 )
                    {
                        if ( $value2['name'] != $root_role ){
                            $roles[] = $value2['name'];
                        }
                    }
                    $roles = implode(',', Q::normalize($roles));
                    if ( $value['namespace']=='default'){
                        $acl_arr[$value['controller']]['actions']['all_actions']['allow'] = '"'.$root_role.'"' ;
                        $acl_arr[$value['controller']]['actions'][$value['action']]['allow'] = '"'.$root_role.','.$roles.'"' ;
                    }else{
                        $acl_arr[$value['namespace']][$value['controller']]['actions']['all_actions']['allow'] = '"'.$root_role.'"' ;
                        $acl_arr[$value['namespace']][$value['controller']]['actions'][$value['action']]['allow'] = '"'.$root_role.','.$roles.'"' ;
                    }
                }
            }
            // 将数组转换成YAML格式，自定义了一个yamldump的方法
            $Helper_Spyc = new Helper_Spyc;
            $acl_yaml = <<<EOT
# <?php die(); ?>

## 注意：书写时，缩进不能使用 Tab，必须使用空格

#############################
# 访问规则
#############################

# ACL_EVERYONE : 任何用户
# ACL_HAS_ROLE : 具有任何角色的用户
# ACL_NO_ROLE : 不具有任何角色的用户
# ACL_NULL : 没有系统角色

EOT;
            $acl_yaml .= $Helper_Spyc->YAMLDump($acl_arr);
            //框架生成的方法，不过不能声场YAML格式，而是jeson格式
            //$acl_yaml2 = Helper_YAML::dump($acl_arr);
            //取得acl文件物理路径
            $acl_filename = rtrim(dirname(__FILE__), '/\\') . DS .'..' . DS .'..' . DS .'..' . DS . 'config' . DS . 'acl.yaml';
            //写入方式打开写入文件的路径
            $fp = fopen($acl_filename,"w+");
            //判断是否生成了文件，并返回结果
            if (fwrite($fp,$acl_yaml)){
                fclose($fp);
                return '更新权限文件成功！';
            } else {
                fclose ($fp);
                return '更新权限文件失败！';
            }            
        }
        /**
         * 管理 等级
         */
        function actionLevel(){
            $this->_pathway->addStep('浏览等级管理');
            if ($this->_context->isPOST()){
                foreach ($_POST['id'] as $key => $value) {
                    if (!isset($_POST['chk']) || !in_array($value, $_POST['chk'])){
                        $arr = array(
                            'id' => $value,
                            'name' => $_POST['name'][$key],
                            'description' => $_POST['description'][$key],
                            'weight' => $_POST['weight'][$key],
                            'enabled' => $_POST['enabled'][$key]
                        );
                        try {
                            $level = Levels::find()->getById($_POST['id'][$key]);
                            $level->changeProps($arr);
                            $level->save();
                        }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                        }
                    }
                }
                if (isset($_POST['chk'])){
                    //Levels::meta()->destroyWhere("id in (?) and id > 5",$_POST['chk']);
                    Levels::meta()->deleteWhere("id in (?) and id > 5",$_POST['chk']);
                }
            }
            // 分页查询内容列表
            $page = intval($this->_context->page);
            if ($page < 1) $page = 1;
            $select = Levels::find()->order('weight desc');
            $select->limitPage($page, 10);
            // 将分页信息和查询到的数据传递到视图
            $this->_view['pagination'] = $select->getPagination();
            $this->_view['levels'] = $select->getAll();
        }
        /**
         * 详情 等级
         */
        function actionLevelView(){
            $this->_pathway->addStep('浏览等级详情');
            $id = $this->_context->id;
            $level = Levels::find()->getById($id);
            $this->_view['level'] = $level;
        }
        
}



<?php
// $Id: acl.php 895 2010-03-23 05:36:29Z thinkgem $
/**
 * 用户权限处理类
**/
class Helper_Acl
{
    /* ******************************************************************** */
    /**
    * 判定用户的权限，并根据用户的状态进行角色转换
    * @param mixed $user_id 要获取的用户的ID
    */
    /* sp_role:
          ROOT: "ROOT"
          ADMIN: "ADMIN"
          NORMAL: "NORMAL"
          FREEZE: "FREEZE"
          REPEAL: "REPEAL"
          UNCHECKED: "UNCHECKED"
          Q::ini('appini/sp_role/UNCHECKED')
    */
    function UserAclRoles( $user_id = '' )
    {
        $show_box['title'] = '获取用户全部角色';
        $return_value = '';
        $roles_idname = array();
        $roles_id = array();
        $sp_roles = Q::ini('appini/sp_role');

        // 第一步：直接从中间表获得用户的全部角色ID
        $user_roles =  UsersHaveRoles::find('user_id = ?',intval($user_id) )->asArray()->getAll();
        //dump($user_roles);

        // 取出有用的ID，去除deny的ID
        foreach ($user_roles as $value){
            if ($value['is_include']){
                $roles_id[] = $value['role_id'];
            }
        }
        //dump ( $roles_id);

        $roles_arr = Roles::find('role_id in (?)',Q::normalize($roles_id,","))->asArray()->getAll();
        foreach ($roles_arr as $value){
            $roles_idname[$value['role_id']] = $value['rolename'];
        }
        //dump($roles_idname);

        if ( in_array($sp_roles['REPEAL'],$roles_idname) ){
            $return_value = array($value['role_id'] => $sp_roles['REPEAL']);
            return $return_value;
        }elseif( in_array($sp_roles['FREEZE'],$roles_idname) ){
            $return_value = array($value['role_id'] => $sp_roles['FREEZE']);
            return $return_value;
        }elseif( in_array($sp_roles['UNCHECKED'],$roles_idname) ){
            $return_value = array($value['role_id'] => $sp_roles['UNCHECKED']);
            return $return_value;
        }else{
            return $roles_idname;
        }
    }


    /* ******************************************************************** */
    /**
    * 获取用户的所有权限，并根据权限的状态进行分类
    * @param mixed $user_id 要获取的用户的ID
    */
    function GetAllRoles( $user_id = '' )
    {
        $show_box['title'] = '获取用户全部角色';
        $return_value = '';

        $user_roles =  Admin_UsersHaveRoles::find('user_id = ?',intval($user_id) )->asArray()->getAll();
        foreach ($user_roles as $value){
            $role_info = Admin_Roles::find('role_id = ?',$value['role_id'])->query();
            if ($value['is_include']){
                $return_value['allow'][$value['role_id']] = $role_info['rolename'];
            }else{
                $return_value['deny'][$value['role_id']] = $role_info['rolename'];
            }
        }
        return $return_value['allow'];
    }

    
}














































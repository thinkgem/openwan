<?php
// $Id: mtom.php 895 2010-03-23 05:36:29Z thinkgem $
/**
 * Many to Many 结构的数据表处理函数
**/
class Helper_MtoM
{
    /* ******************************************************************** */
    /**
     * 将m2m的某个关联属性进行转换，变成以某个字段值为数值的数组，可选择是否生成链接
     * @param mixed $m2m_object    传入的关联属性，为一个对象
     * @param var $url_base        url基础值，形式为 namespace::controller/action 如果有值，则生成链接形式，
     * @param mixed $url_parameter   url扩展值，可以为空或者是单个属性字段，也可以是做好了的url指定的数组
     * @param var $display_field   显示的字段名
     * 
     * @return Arr
    */
    static function MaekAasArr( $m2m_object = array(), $url_base='', $url_parameter='', $display_field='' )
    {
        $return_value = array();
        foreach( $m2m_object as $value )
        {//url('admin::users/bind')
                
            if (empty($url_base)){
                $a_left = $a_right = '';
            }else{
                if (empty($url_parameter)){
                    $url_parameter = array();
                }elseif(!is_array($url_parameter)){
                    $url_parameter = array($url_parameter=>$value[$url_parameter]);
                }
                $a_left  = '<a href="'.url($url_base,$url_parameter ).'" >';
                $a_right = '</a>';
            }
            $return_value[] = $a_left.$value[$display_field].$a_right;
        }
        return $return_value;
        //dump($return_value);
    }
    /* ******************************************************************** */
    function GetAllRoles( $user_id = '' )
    {
        $show_box['title'] = '获取用户全部角色';
        $roles = array();
        $user_roles =  Admin_UsersHaveRoles::find('user_id = ?',$user_id )->asArray()->getAll();
        foreach ($user_roles as $value){
            $role_name = Admin_Roles::find('role_id = ?',$value['role_id'])->query();
            if ($value['is_include']){
                $roles['allow'][$value['role_id']] = $role_name['rolename'];
            }else{
                $roles['deny'][$value['role_id']] = $role_name['rolename'];
            }
        }
        return $roles['allow'];
    }

}














































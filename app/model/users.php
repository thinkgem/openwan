<?php
// $Id: users.php 901 2010-07-22 05:49:33Z thinkgem $

/**
 * Users 封装来自 users 数据表的记录及领域逻辑
 * @author WangZhen <thinkgem@163.com>
 */
class Users extends QDB_ActiveRecord_Abstract
{

//    /**
//     * 取得用户角色id和角色名(array)
//     * @param array $tags
//     * @return String
//     */
//    static function getRoles($roles){
//        if (is_array($roles) || $roles instanceof Iterator){
//            $arr = array();
//            foreach ($roles as $role){
//                $arr[$role->id] = $role->name;
//            }
//            $roles = $arr;
//        }
//        //return trim(implode(' ', Q::normalize($roles, ' ')));
//        return $roles;
//    }
//    /**
//    * 设置用户的角色
//    * @param mixed $roles
//    *
//    $roles = array(
//            '0' => '1',
//            '1' => '3',
//            '2' => '4',
//            );
//            ///这个函数未完成
//    */
//    function setRoles($roles){
//        $return_value = array();
//        foreach ( $roles as $role ){
//            $return_value[] = Roles::find('id = ?', $role )->query();
//        }
//        $this->_props['roles'] = $return_value;
//        $this->willChanged('roles');
//    }

    /**
     * 获得拥有角色
     * @return <type> array
     */
    function getRoles(){
        $arr = array();
        if ($this->group_id==1){
            $arr[] = 'ADMIN';
        }else{
            $roles = $this->group->roles;
            if (empty ($roles)) return $arr;
            foreach ($roles as $role){
                $arr[] = $role->name;
            }
        }
        return $arr;
    }

    /**
     * 返回对象的定义
     *
     * @static
     *
     * @return array
     */
    static function __define()
    {
        return array
        (
            // 指定该 ActiveRecord 要使用的行为插件
            'behaviors' => 'acluser',

            // 指定行为插件的配置
            'behaviors_settings' => array
            (
                # '插件名' => array('选项' => 设置),
                'acluser' => array(
                        'encode_type' => 'crypt',			//encode_type: 密码的加密方式，默认值为 crypt

                        'username_prop'		=> 'username',		//指示在模型中使用哪一个属性来保存的用户名，默认值为 username。
                        'password_prop'		=> 'password',		//指示在模型中使用哪一个属性来保存用户密码，默认值为 password。

                        'roles_enabled'		=> true,		//是否启用对关联角色的支持，默认值为 false。
                        'roles_prop'		=> 'roles',		//角色信息映射到用户模型的哪一个属性之上。默认值为 roles。
                        'roles_name_prop'	=> 'rolename',		//指示角色模型使用哪一个属性保存角色名称，默认值为 name。

                        'acl_data_props'	=> 'id, group_id, level_id, username, password, nickname, sex, birthday, address, email, duty, office_phone, home_phone, mobile_phone, enabled, register_at, register_ip, login_at, login_ip, login_count',//指示在使用 aclData() 方法时，返回的数组中要包含哪些属性，默认值为 uesrname。

                        'unique_username'   	=> true,	 	//指示是否检查用户名的唯一性，默认值为 true

                        'update_login_auto' 	=> true,		//是否在成功调用 validateLogin() 后自动更新用户信息，默认值为 false
                        'update_login_at_prop'	=> 'login_at',          //记录登录时间的属性，不指定则不更新
                        'update_login_ip_prop'	=> 'login_ip',          //记录登录 IP 的属性，不指定则不更新
                        'update_login_count_prop' => 'login_count',	//记录登录次数的属性，不指定则不更新

                        'register_save_auto' => true,			//是否在新建用户对象时，自动保存下列信息，默认值为 false
                        'register_at_prop' => 'register_at',		//记录用户的创建时间
                        'register_ip_prop' => 'register_ip',		//记录用户的 IP 地址
                        
                ),
//                'uniqueness' => array(
//                    'check_props' => 'username, email',
//                    'error_messages' => array(
//                        'username'=>"此帐号已经存在",
//                        'email' => "此E-Mail已经存在"),
//                ),
            ),

            // 用什么数据表保存对象
            'table_name' => 'users',

            // 指定数据表记录字段与对象属性之间的映射关系
            // 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
            'props' => array
            (
                // 主键应该是只读，确保领域对象的“不变量”
                'id' => array('readonly' => true),

                /**
                 *  可以在此添加其他属性的设置
                 */
                # 'other_prop' => array('readonly' => true),

                'register_at' => array('readonly' => true),
                'register_ip' => array('readonly' => true),

                'roles' => array('getter' => 'getRoles'),

                /**
                 * 添加对象间的关联
                 */
                # 'other' => array('has_one' => 'Class'),

                //归属用户组
                'group' => array(QDB::BELONGS_TO => 'groups', 'source_key' => 'group_id'),
                //归属等级
                'level' => array(QDB::BELONGS_TO => 'levels', 'source_key' => 'level_id'),
//                //用户拥有一个或者多个角色
//                'roles' => array(
//                        QDB::MANY_TO_MANY=> 'admin_roles',
//                        'setter'	 => 'setRoles',
//                        'mid_source_key' => 'user_id',
//                        'mid_target_key' => 'role_id',
//                        'mid_table_name' => 'sys_acl_users_have_roles'
//                ),
//                //用户除了角色和分组之外还可以拥有多个额外的权限
//                'permissions' => array(
//                        QDB::MANY_TO_MANY=> 'admin_permissions',
//                        'mid_source_key' => 'user_id',
//                        'mid_target_key' => 'permission_id',
//                        'mid_table_name' => 'sys_acl_users_have_permissions'
//                ),

            ),

            /**
             * 允许使用 mass-assignment 方式赋值的属性
             *
             * 如果指定了 attr_accessible，则忽略 attr_protected 的设置。
             */
            'attr_accessible' => '',

            /**
             * 拒绝使用 mass-assignment 方式赋值的属性
             */
            'attr_protected' => 'id,username,group_id,level_id',

            /**
             * 指定在数据库中创建对象时，哪些属性的值不允许由外部提供
             *
             * 这里指定的属性会在创建记录时被过滤掉，从而让数据库自行填充值。
             */
            'create_reject' => '',

            /**
             * 指定更新数据库中的对象时，哪些属性的值不允许由外部提供
             */
            'update_reject' => '',

            /**
             * 指定在数据库中创建对象时，哪些属性的值由下面指定的内容进行覆盖
             *
             * 如果填充值为 self::AUTOFILL_TIMESTAMP 或 self::AUTOFILL_DATETIME，
             * 则会根据属性的类型来自动填充当前时间（整数或字符串）。
             *
             * 如果填充值为一个数组，则假定为 callback 方法。
             */
            'create_autofill' => array
            (
                # 属性名 => 填充值
                # 'is_locked' => 0,
            ),

            /**
             * 指定更新数据库中的对象时，哪些属性的值由下面指定的内容进行覆盖
             *
             * 填充值的指定规则同 create_autofill
             */
            'update_autofill' => array
            (
            ),

            /**
             * 在保存对象时，会按照下面指定的验证规则进行验证。验证失败会抛出异常。
             *
             * 除了在保存时自动验证，还可以通过对象的 ::meta()->validate() 方法对数组数据进行验证。
             *
             * 如果需要添加一个自定义验证，应该写成
             *
             * 'title' => array(
             *        array(array(__CLASS__, 'checkTitle'), '标题不能为空'),
             * )
             *
             * 然后在该类中添加 checkTitle() 方法。函数原型如下：
             *
             * static function checkTitle($title)
             *
             * 该方法返回 true 表示通过验证。
             */
            'validations' => array
            (
                'group_id' => array
                (
                    array('is_int', '用户组编号必须是一个整数'),

                ),

                'level_id' => array
                (
                    array('is_int', '阅读等级编号必须是一个整数'),

                ),

                'username' => array
                (
                    array('not_empty', '用户名不能为空'),
                    array('min_length', 3, '用户名不能少于 3 个字符'),
                    array('max_length', 32, '用户名不能超过 32 个字符'),
                    array('is_alnumu', '用户名只能有字母、数字和下划线组成'),

                ),

                'password' => array
                (
                    array('not_empty', '密码不能为空'),
                    array('min_length', 4, '密码不能少于 4 个字符'),
                    array('max_length', 32, '密码不能超过 32 个字符'),

                ),

                'nickname' => array
                (
                    array('not_empty', '昵称不能为空'),
                    array('max_length', 64, '昵称不能超过 64 个字符'),

                ),

                'sex' => array
                (
                    array('is_int', '性别（0：保密；1：男；2：女）必须是一个整数'),

                ),

                'birthday' => array
                (
                    //array('is_date', '生日必须是一个有效的日期'),
                    array('max_length', 64, '生日不能超过 64 个字符'),
                ),

                'address' => array
                (
                    array('max_length', 255, '地址不能超过 255 个字符'),

                ),

                'email' => array
                (
                    //array('is_email', '请输入正确的邮箱地址'),
                    array('max_length', 64, '电子邮箱不能超过 64 个字符'),

                ),

                'duty' => array
                (
                    array('max_length', 64, '职务不能超过 64 个字符'),

                ),

                'office_phone' => array
                (
                    array('max_length', 64, '办公电话不能超过 64 个字符'),

                ),

                'home_phone' => array
                (
                    array('max_length', 64, '家庭电话不能超过 64 个字符'),

                ),

                'mobile_phone' => array
                (
                    array('max_length', 64, '手机不能超过 64 个字符'),

                ),
                
                'description' => array
                (
                    array('max_length', 255, '个人简介不能超过 255 个字符'),

                ),

                'enabled' => array
                (
                    array('is_int', '可用性必须是一个整数'),

                ),

                'register_at' => array
                (
                    array('is_int', '注册时间必须是一个整数'),

                ),

                'register_ip' => array
                (
                    array('max_length', 15, '注册IP不能超过 15 个字符'),

                ),

                'login_count' => array
                (
                    array('is_int', '登录次数必须是一个整数'),

                ),

                'login_at' => array
                (
                    array('is_int', '登录时间必须是一个整数'),

                ),

                'login_ip' => array
                (
                    array('max_length', 15, '登录IP不能超过 15 个字符'),

                ),                


            ),
        );
    }


/* ------------------ 以下是自动生成的代码，不能修改 ------------------ */

    /**
     * 开启一个查询，查找符合条件的对象或对象集合
     *
     * @static
     *
     * @return QDB_Select
     */
    static function find()
    {
        $args = func_get_args();
        return QDB_ActiveRecord_Meta::instance(__CLASS__)->findByArgs($args);
    }

    /**
     * 返回当前 ActiveRecord 类的元数据对象
     *
     * @static
     *
     * @return QDB_ActiveRecord_Meta
     */
    static function meta()
    {
        return QDB_ActiveRecord_Meta::instance(__CLASS__);
    }


/* ------------------ 以上是自动生成的代码，不能修改 ------------------ */

}


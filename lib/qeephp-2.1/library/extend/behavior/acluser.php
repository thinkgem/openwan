<?php
// $Id: acluser.php 2423 2009-04-21 19:59:40Z dualface $

/**
 * 定义 Behavior_AclUser 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: acluser.php 2423 2009-04-21 19:59:40Z dualface $
 * @package behavior
 */

/**
 * Behavior_AclUser 实现基于 ACL 的用户访问控制
 *
 * acluser 行为插件支持下列设置选项：
 *
 * -  encode_type: 密码的加密方式，默认值为 crypt。
 *
 *    该选项可以设置为 cleartext, md5、crypt、sha1、sha2，或任何有效的 PHP 全局函数名。其中，cleartext 表示不加密密码。
 *
 *    如果要使用自定义的加密方法，可以指定 encode_type 设置为一个回调函数，例如：
 *
 *    @code php
 *    'encode_type' => array('MyClass', 'encryptString');
 *    @endcode
 *
 * -  username_prop: 指示在模型中使用哪一个属性来保存的用户名，默认值为 username。
 *
 *    由于 acluser 插件需要通过用户名来查询对象，因此需要指定正确的属性名称。
 *
 * -  password_prop: 指示在模型中使用哪一个属性来保存用户密码，默认值为 password。
 *
 *    acluser 插件在验证用户登录、更新密码时需要用到该设置指定的属性。
 *
 * -  roles_enabled: 是否启用对关联角色的支持，默认值为 false。
 *
 *    当启用关联角色支持后，可以通过 aclRoles() 方法来获得用户对象关联的角色信息。
 *    关联角色支持需要设置下列选项：
 *
 *    -  roles_prop: 角色信息映射到用户模型的哪一个属性之上。默认值为 roles。
 *
 *    -  roles_name_prop: 指示角色模型使用哪一个属性保存角色名称，默认值为 name。
 *
 * -  acl_data_props: 指示在使用 aclData() 方法时，返回的数组中要包含哪些属性。
 *    默认值为 uesrname。
 *
 *    要指定多个属性，用“,”分割，例如“username, email”。不过不管指定什么属性，
 *    aclData() 方法返回的数组中总是包含一个名为 id 的键，其键值是用户的 ID。
 *
 * -  update_login_auto: 是否在成功调用 validateLogin() 后自动更新用户信息，默认值为 false。
 *
 *    当 update_login_auto 为 true 时，可以指定下列选项：
 *
 *    -  update_login_count_prop: 记录登录次数的属性，不指定则不更新；
 *    -  update_login_at_prop: 记录登录时间的属性，不指定则不更新
 *    -  update_login_ip_prop: 记录登录 IP 的属性，不指定则不更新
 *
 * -  register_save_auto: 是否在新建用户对象时，自动保存下列信息，默认值为 false。
 *
 *    - register_ip_prop: 记录用户注册时的 IP 地址
 *    - register_at_prop: 记录注册时间
 *
 * -  unique_username: 指示是否检查用户名的唯一性，默认值为 true。
 *
 *    当该设置为 true 时，将不允许创建同名用户。
 *    并在尝试创建同名用户时抛出 AclUser_DuplicateUsernameException 异常。
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: acluser.php 2423 2009-04-21 19:59:40Z dualface $
 * @package behavior
 */
class Model_Behavior_AclUser extends QDB_ActiveRecord_Behavior_Abstract
{
    /**
     * 插件的设置信息
     *
     * @var array
     */
    protected $_settings = array
    (
        'encode_type'               => 'crypt',
        'username_prop'             => 'username',
        'password_prop'             => 'password',

        'roles_enabled'             => false,
        'roles_prop'                => 'roles',
        'roles_name_prop'           => 'name',

        'acl_data_props'            => 'username',

        'update_login_auto'         => false,
        'update_login_count_prop'   => null,
        'update_login_at_prop'      => null,
        'update_login_ip_prop'      => null,

        'register_save_auto'        => false,
        'register_ip_prop'          => null,
        'register_at_prop'          => null,

        'unique_username'           => true,
    );

    /**
     * 保存状态
     *
     * @var array
     */
    private $_saved_state = array();

    /**
     * 绑定行为插件
     */
    function bind()
    {
        $this->_addStaticMethod('validateLogin',     array($this, 'validateLogin'));
        $this->_addStaticMethod('validateUsername',  array($this, 'validateUsername'));
        $this->_addStaticMethod('validatePassword',  array($this, 'validatePassword'));
        $this->_addStaticMethod('changePassword',    array($this, 'changePassword'));

        $this->_addDynamicMethod('checkPassword',    array($this, 'checkPasswordDyn'));
        $this->_addDynamicMethod('changePassword',   array($this, 'changePasswordDyn'));
        $this->_addDynamicMethod('updateLogin',      array($this, 'updateLoginDyn'));

        if ($this->_settings['roles_enabled'])
        {
            $this->_addDynamicMethod('aclRoles',     array($this, 'aclRolesDyn'));
        }

        $this->_addDynamicMethod('aclData',          array($this, 'aclDataDyn'));

        $this->_addEventHandler(self::AFTER_VALIDATE_ON_CREATE, array($this, '_after_validate_on_create'));
        $this->_addEventHandler(self::AFTER_VALIDATE_ON_UPDATE, array($this, '_after_validate_on_update'));
    }

    /**
     * 验证用户登录并返回用户对象
     *
     * 如果 update_login_auto 设置为 true，验证通过时会同时更新用户的登录信息。
     *
     * 用法：
     * @code php
     * try
     * {
     *     $member = Member::meta()->validateLogin($username, $password);
     *     dump($member);
     * }
     * catch (AclUser_Exception $ex)
     * {
     *     echo $ex->getMessage();
     * }
     * @endcode
     *
     * 如果用户名不存在，将抛出 AclUser_UsernameNotFoundException 异常。
     * 密码不正确，则抛出 AclUser_WrongPasswordException 异常。
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param boolean $update_login 阻止自动更新
     *
     * @return QDB_ActiveRecord_Abstract 用户对象
     * @throw AclUser_UsernameNotFoundExceptio, AclUser_WrongPasswordException
     */
    function validateLogin($username, $password, $update_login = true)
    {
        $pn = $this->_settings['username_prop'];
        $member = $this->_meta->find(array($pn => $username))->query();

        if (!$member->id())
        {
            throw new AclUser_UsernameNotFoundException($username);
        }
        if (!$this->checkPasswordDyn($member, $password))
        {
            throw new AclUser_WrongPasswordException($username);
        }

        if ($this->_settings['update_login_auto'])
        {
            $this->updateLoginDyn($member);
        }
        return $member;
    }

    /**
     * 验证用户名
     *
     * 用法：
     * @code php
     * if (Member::meta()->validateUsername($username))
     * {
     *     echo '用户名验证通过';
     * }
     * @endcode
     *
     * @param string $username 用户名
     *
     * @return boolean
     */
    function validateUsername($username)
    {
        $pn = $this->_settings['username_prop'];
        return $this->_meta->find(array($pn => $username))->getCount() > 0;
    }

    /**
     * 验证用户名和密码
     *
     * 用法：
     * @code php
     * if (Member::meta()->validatePassword($username, $password))
     * {
     *     echo '用户名和密码验证通过';
     * }
     * @endcode
     *
     * @param string $username 用户名
     * @param string $password 密码
     *
     * @return boolean
     */
    function validatePassword($username, $password)
    {
        $pn = $this->_settings['username_prop'];
        $member = $this->_meta->find(array($pn => $username))->query();

        return ($member->id() && $this->checkPasswordDyn($member, $password));
    }

    /**
     * 修改指定用户的密码
     *
     * 用法：
     * @code php
     * Member::changePassword($username, $new_password, $old_password);
     * @endcode
     *
     * 如果用户名不存在，则抛出 AclUser_UsernameNotFoundException 异常。
     * 如果旧密码不正确，则抛出 AclUser_WrongPasswordException 异常。
     *
     * 可以通过指定 $ignore_old_password 为 true 来忽略对旧密码的检查：
     * @code php
     * Member::changePassword($username, $new_password, null, true);
     * @endcode
     *
     * @param string $username 用户名
     * @param string $new_password 新密码
     * @param string $old_password 旧密码
     * @param boolean $ignore_old_password 是否忽略对旧密码的检查，默认为 false
     *
     * @throw AclUser_UsernameNotFoundException, AclUser_WrongPasswordException
     */
    function changePassword($username, $new_password, $old_password, $ignore_old_password = false)
    {
        $pn = $this->_settings['username_prop'];
        $member = $this->_meta->find(array($pn => $username))->query();

        if (!$member->id())
        {
            throw new AclUser_UsernameNotFoundException($username);
        }

        $this->changePasswordDyn($member, $new_password, $old_password, $ignore_old_password);
    }

    /**
     * 检查指定的密码是否与当前用户的密码相符
     *
     * 用法：
     * @code php
     * if ($member->checkPassword($password)
     * {
     *     echo '指定的密码与用户现有的密码相符';
     * }
     * @endcode
     *
     * @param QDB_ActiveRecord_Abstract $member 要检查的用户对象
     * @param string $password 检查密码
     *
     * @return boolean 检查结果
     */
    function checkPasswordDyn(QDB_ActiveRecord_Abstract $member, $password)
    {
        return $this->_checkPassword($password, $member[$this->_settings['password_prop']]);
    }

    /**
     * 修改当前用户的密码
     *
     * 用法：
     * @code php
     * try
     * {
     *     $member->changePassword($new_password, $old_password);
     *     echo '修改密码成功';
     * }
     * catch (AclUser_Exception $ex)
     * {
     *     echo $ex->getMessage();
     * }
     * @endcode
     *
     * 如果旧密码不正确，则抛出 AclUser_WrongPasswordException 异常。
     *
     * 可以通过指定 $ignore_old_password 为 true 来忽略对旧密码的检查：
     * @code php
     * $member->changePassword($new_password, null, true);
     * @endcode
     *
     * @param QDB_ActiveRecord_Abstract $member 要更新密码的用户对象
     * @param string $new_password 新密码
     * @param string $old_password 旧密码
     * @param boolean $ignore_old_password 是否忽略对旧密码的检查，默认为 false
     *
     * @throw AclUser_WrongPasswordException
     */
    function changePasswordDyn(QDB_ActiveRecord_Abstract $member,
                               $new_password, $old_password, $ignore_old_password = false)
    {
        if (!$ignore_old_password)
        {
            if (!$this->checkPasswordDyn($member, $old_password))
            {
                throw new AclUser_WrongPasswordException($member[$this->_settings['username_prop']]);
            }
        }

        $member->changePropForce($this->_settings['password_prop'], $new_password);
        $member->save();
    }

    /**
     * 更新用户的登录信息
     *
     * 要更新的属性由 update_login_count_prop、update_login_at_prop 和 update_login_ip_prop
     * 设置指定。
     *
     * 用法：
     * @code php
     * $member->updateLogin();
     * @endcode
     *
     * updateLogin() 会尝试自行获取登录时间和 IP 信息。如果有必要也可自行指定：
     * @code php
     * $member->updateLogin(array(
     *     'login_at' => $time,
     *     'login_ip' => $ip,
     * ));
     * @endcode
     *
     * @param QDB_ActiveRecord_Abstract $member 要更新登录信息的用户对象
     * @param array $data 自行指定的属性值
     */
    function updateLoginDyn(QDB_ActiveRecord_Abstract $member, array $data = null)
    {
        $pn = $this->_settings['update_login_count_prop'];
        if ($pn)
        {
            $member->changePropForce($pn, $member[$pn] + 1);
        }
        $pn = $this->_settings['update_login_at_prop'];
        if ($pn)
        {
            $time = isset($data['login_at']) ? $data['login_at'] : CURRENT_TIMESTAMP;
            if (substr($this->_meta->props[$pn]['ptype'], 0, 3) != 'int')
            {
                $time = date('Y-m-d H:i:s', $time);
            }
            $member->changePropForce($pn, $time);
        }
        $pn = $this->_settings['update_login_ip_prop'];
        if ($pn)
        {
            $ip = isset($data['login_at']) ?
                  $data['login_at']
                  : isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            if (substr($this->_meta->props[$pn]['ptype'], 0, 3) == 'int')
            {
                $ip = ip2long($ip);
            }
            $member->changePropForce($pn, $ip);
        }

        $member->save();
    }

    /**
     * 获得包含用户所有角色名的数组
     *
     * 用法：
     * @code php
     * $roles = $member->aclRoles();
     * echo '用户具有下列角色： ' . implode(', ', $roles);
     * @endcode
     *
     * @param QDB_ActiveRecord_Abstract $member 用户对象
     *
     * @return array 包含角色名的数组
     */
    function aclRolesDyn(QDB_ActiveRecord_Abstract $member)
    {
        $roles = array();
        foreach ($member[$this->_settings['roles_prop']] as $role)
        {
            $roles[] = $role[$this->_settings['roles_name_prop']];
        }
        return $roles;
    }

    /**
     * 获得用户的 ACL 数据
     *
     * ACL 数据一般包含用户 ID 和用户名，通常用于配合 QACL 实现基于角色的访问控制。
     *
     * 用法：
     * @code php
     * $data = $member->aclData();
     * dump($data);
     * @endcode
     *
     * 要返回的数据由 acl_data_props 设置来指定。不过不管指定了哪些属性，aclData()
     * 的返回结果中总是为包含名为 id 的键。该键的值是用户对象的 ID。
     *
     * 也可以在获得 ACL 数据时通过 $props 参数指定要返回的属性值：
     * @code php
     * $data = $member->aclData('email, addr');
     * @endcode
     *
     * @param QDB_ActiveRecord_Abstract $member 用户对象
     * @param string $props 要返回的属性值
     *
     * @return array 包含指定属性值的数组
     */
    function aclDataDyn(QDB_ActiveRecord_Abstract $member, $props = null)
    {
        if (!$props)
        {
            $props = $this->_settings['acl_data_props'];
        }
        $props = Q::normalize($props);
        $data = array();
        foreach ($props as $pn)
        {
            $data[$pn] = $member[$pn];
        }
        $data['id'] = $member->id();
        return $data;
    }

    /**
     * 在新建的 ActiveRecord 保存到数据库前调用的事件
     *
     * @param QDB_ActiveRecord_Abstract $member 用户对象
     */
    function _after_validate_on_create(QDB_ActiveRecord_Abstract $member)
    {
        if ($this->_settings['unique_username'])
        {
            $pn = $this->_settings['username_prop'];
            if ($this->_meta->find(array($pn => $member[$pn]))->getCount() > 0)
            {
                throw new AclUser_DuplicateUsernameException($member[$pn]);
            }
        }

        // 加密密码
        $pn = $this->_settings['password_prop'];
        $password_cleartext = $member[$pn];
        $member->changePropForce($pn, $this->_encodePassword($password_cleartext));
        $this->_saved_state['password'] = $password_cleartext;
        $this->_meta->addExceptionTrap($member, self::CREATE_EXCEPTION, array($this, '_save_exception_handler'));

        if ($this->_settings['register_save_auto'])
        {
            $pn = $this->_settings['register_at_prop'];
            if ($pn)
            {
                $time = CURRENT_TIMESTAMP;
                if (substr($this->_meta->props[$pn]['ptype'], 0, 3) != 'int')
                {
                    $time = date('Y-m-d H:i:s', $time);
                }
                $member->changePropForce($pn, $time);
            }

            $pn = $this->_settings['register_ip_prop'];
            if ($pn)
            {
                $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                if (substr($this->_meta->props[$pn]['ptype'], 0, 3) == 'int')
                {
                    $ip = ip2long($ip);
                }
                $member->changePropForce($pn, $ip);
            }
        }
    }

    /**
     * 在更新 ActiveRecord 前调用的事件
     *
     * @param QDB_ActiveRecord_Abstract $member 用户对象
     */
    function _after_validate_on_update(QDB_ActiveRecord_Abstract $member)
    {
        $pn = $this->_settings['password_prop'];
        if ($member->changed($pn))
        {
            $password_cleartext = $member[$pn];
            $member[$pn] = $this->_encodePassword($password_cleartext);
            $this->_saved_state['password'] = $password_cleartext;
            $this->_meta->addExceptionTrap($member, self::CREATE_EXCEPTION, array($this, '_save_exception_handler'));
        }
    }

    /**
     * 在保存新建用户对象失败抛出异常时，还原用户的密码属性
     *
     * @param QDB_ActiveRecord_Abstract $member 保存出错的用户对象
     * @param Exception $ex 异常
     */
    function _save_exception_handler(QDB_ActiveRecord_Abstract $member, Exception $ex)
    {
        if (isset($this->_saved_state['password']))
        {
            $member->changePropForce($this->_settings['password_prop'], $this->_saved_state['password']);
            unset($this->_saved_state['password']);
        }
    }

    /**
     * 检查明文和加密后的密码是否相符
     *
     * @param string $cleartext 明文
     * @param string $cryptograph 加密后的密码
     *
     * @return boolean
     */
    private function _checkPassword($cleartext, $cryptograph)
    {
        $et = $this->_settings['encode_type'];
        if (is_array($et))
        {
            return call_user_func($et, $cleartext) == $cryptograph;
        }
        if ($et == 'cleartext') return $cleartext == $cryptograph;

        switch ($et)
        {
        case 'md5':
            return md5($cleartext) == $cryptograph;
        case 'crypt':
            return crypt($cleartext, $cryptograph) == $cryptograph;
        case 'sha1':
            return sha1($cleartext) == $cryptograph;
        case 'sha2':
            return hash('sha512', $cleartext) == $cryptograph;
        default:
            return $et($cleartext) == $cryptograph;
        }
    }

    /**
     * 获得加密后的密码
     *
     * @param string $password 要加密的密码明文
     *
     * @return string 加密后的密码
     */
    private function _encodePassword($password)
    {
        $et = $this->_settings['encode_type'];
        if (is_array($et))
        {
            return call_user_func($et, $password);
        }
        if ($et == 'cleartext') return $password;
        return $et($password);
    }

}


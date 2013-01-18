<?php
// $Id$

/**
 * 定义 AclUser_WrongPasswordException 异常
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: acluser.php 1979 2009-01-08 10:46:54Z dualface $
 * @package exception
 */

/**
 * AclUser_WrongPasswordException 异常指示密码不正确
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: acluser.php 1979 2009-01-08 10:46:54Z dualface $
 * @package exception
 */
class AclUser_WrongPasswordException extends AclUser_Exception
{
    function __construct($username)
    {
        parent::__construct($username, __('Wrong password for user "%s".', $username));
    }
}



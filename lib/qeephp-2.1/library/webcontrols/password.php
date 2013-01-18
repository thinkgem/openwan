<?php
// $Id: password.php 2014 2009-01-08 19:01:29Z dualface $

/**
 * 定义 Control_Password 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: password.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */

/**
 * 密码输入框
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: password.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */
class Control_Password extends Control_Input_Abstract
{
	function render()
	{
		return $this->_make('password');
	}
}


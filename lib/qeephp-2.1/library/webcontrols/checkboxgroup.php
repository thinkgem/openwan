<?php
// $Id: checkboxgroup.php 2014 2009-01-08 19:01:29Z dualface $

/**
 * 定义 Control_CheckboxGroup 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: checkboxgroup.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */

/**
 * 构造一个多选框组
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: checkboxgroup.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */
class Control_CheckboxGroup extends Control_CheckboxGroup_Abstract
{
	function render()
	{
		return $this->_make('checkbox', '[]');
	}
}


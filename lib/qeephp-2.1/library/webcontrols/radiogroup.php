<?php
// $Id: radiogroup.php 2014 2009-01-08 19:01:29Z dualface $

/**
 * 定义 Control_RadioGroup 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: radiogroup.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */

/**
 * 构造一组单选按钮
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: radiogroup.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */
class Control_RadioGroup extends Control_CheckboxGroup_Abstract
{
	function render()
	{
		return $this->_make('radio', '');
	}
}


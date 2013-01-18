<?php
// $Id: button.php 2014 2009-01-08 19:01:29Z dualface $

/**
 * 定义 Control_Button 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: button.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */

/**
 * Control_Button 封装各种类型的按钮
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: button.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */
class Control_Button extends Control_Input_Abstract
{
	function render($button_type = 'button')
	{
		$caption = $this->_extract('caption');
        if (!empty($caption))
        {
		    $this->set('value', $caption);
		}
		return $this->_make($button_type);
	}
}



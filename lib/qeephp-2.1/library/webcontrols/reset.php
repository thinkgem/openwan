<?php
// $Id: reset.php 2014 2009-01-08 19:01:29Z dualface $

/**
 * 定义 Control_Reset 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: reset.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */

/**
 * 构造一个表单重置按钮
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: reset.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */
class Control_Reset extends Control_Button
{
	function render()
	{
		$caption = $this->_extract('caption');
        if (!empty($caption))
        {
		    $this->set('value', $caption);
		}
		return $this->_make('reset');
	}
}


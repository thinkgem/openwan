<?php
// $Id: static.php 2014 2009-01-08 19:01:29Z dualface $

/**
 * 定义 Control_Static 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: static.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */

/**
 * 构造一个静态控件
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: static.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */
class Control_Static extends QUI_Control_Abstract
{
	function render()
	{
		$caption = $this->_extract('caption');
		$out = '<div ';
		$out .= $this->_printIdAndName();
		$out .= $this->_printAttrs();
		$out .= '>';
		$out .= htmlspecialchars($caption);
		$out .= '</div>';

        return $out;
	}
}

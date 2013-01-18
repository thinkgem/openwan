<?php
// $Id: memo.php 2014 2009-01-08 19:01:29Z dualface $

/**
 * 定义 Control_Memo 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: memo.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */

/**
 * 构造一个多行文本框
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: memo.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */
class Control_Memo extends QUI_Control_Abstract
{
	function render()
	{
		$value = $this->_extract('value');
		$out = '<textarea ';
		$out .= $this->_printIdAndName();
		$out .= $this->_printAttrs();
		$out .= $this->_printDisabled();
		$out .= '>';
		$out .= htmlspecialchars($value);
		$out .= '</textarea>';

        return $out;
	}
}


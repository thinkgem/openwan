<?php
// $Id: input_abstract.php 2014 2009-01-08 19:01:29Z dualface $

/**
 * 定义 Control_Input_Abstract 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: input_abstract.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */

/**
 * Control_Input_Abstract 类使所有输入框控件的基础类
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: input_abstract.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */
abstract class Control_Input_Abstract extends QUI_Control_Abstract
{
	protected function _make($type)
	{
		$out = "<input type=\"{$type}\" ";
        $out .= $this->_printIdAndName();
        $out .= $this->_printValue();
		$out .= $this->_printAttrs();
        $out .= $this->_printDisabled();
        $out .= '/>';

        return $out;
	}
}


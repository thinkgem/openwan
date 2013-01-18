<?php
// $Id: checkbox_abstract.php 2646 2009-08-11 06:12:31Z jerry $

/**
 * 定义 Control_Checkbox_Abstract 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: checkbox_abstract.php 2646 2009-08-11 06:12:31Z jerry $
 * @package webcontrols
 */

/**
 * Control_Checkbox_Abstract 是所有多选框的基础类类
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: checkbox_abstract.php 2646 2009-08-11 06:12:31Z jerry $
 * @package webcontrols
 */
abstract class Control_Checkbox_Abstract extends QUI_Control_Abstract
{
	protected function _make($type)
	{
        $caption = $this->_extract('caption');
        $caption_class = $this->_extract('caption_class');

		$out = "<input type=\"{$type}\" ";
		$out .= $this->_printIdAndName();
		$out .= $this->_printChecked();
        if (empty($this->value)) $this->value = 'checked';
		$out .= $this->_printAttrs('id, name, checked, checked_by_value');
		$out .= $this->_printDisabled();

		$out .= '/>';
        if ($caption)
        {
			$attribs = array('for' => $this->id(), 'caption' => $caption, 'class' => $caption_class);
			$label = Q::control('label', $this->id() . '_label', $attribs);
			$out .= "\n" . $label->render();
        }

        return $out;
	}
}


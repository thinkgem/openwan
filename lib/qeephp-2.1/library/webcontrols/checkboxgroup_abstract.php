<?php
// $Id: checkboxgroup_abstract.php 2646 2009-08-11 06:12:31Z jerry $

/**
 * 定义 Control_CheckboxGroup_Abstract 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: checkboxgroup_abstract.php 2646 2009-08-11 06:12:31Z jerry $
 * @package webcontrols
 */

/**
 * Control_CheckboxGroup_Abstract 是群组多选框的基础类
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: checkboxgroup_abstract.php 2646 2009-08-11 06:12:31Z jerry $
 * @package webcontrols
 */
abstract class Control_CheckboxGroup_Abstract extends QUI_Control_Abstract
{
	protected function _make($type, $suffix)
	{
        static $id_index = 1;

        $items = (array)$this->_extract('items');
		$max = count($items);
		if ($max == 0) return '';

		$selected = $this->_extract('value');
        $caption_class = $this->_extract('caption_class');

        $out = '';
        foreach ($items as $value => $caption)
        {
            $checked = false;
            if($type == 'checkbox')
            {
                 if (!is_array($selected))
                 {
                     $selected = array($selected);
                 }
                 if (in_array($value,$selected))
                 {
                     $checked = true;
                 }
            }
            else
            {
                if ($value == $selected && strlen($value) == strlen($selected) && strlen($selected) > 0)
                {
                    $checked = true;
                }
            }

			$out .= "<span style=\"white-space: nowrap;\"><input type=\"{$type}\" ";
			$name = $this->name() . $suffix;
			$id = $this->id() . "_{$id_index}";
			$out .= "name=\"{$name}\" ";
			$id_index++;
            $out .= "id=\"{$id}\" ";
            if (strlen($value) == 0) $value = 1;

			$out .= 'value="' . htmlspecialchars($value) . '" ';
			$out .= $this->_printAttrs();
			$out .= $this->_printChecked();
            $out .= $this->_printDisabled();
            if ($checked)
            {
				$out .= 'checked="checked" ';
            }

			$out .= '/> ';
            if ($caption)
            {
                $out .= Q::control('label', "{$id}_label", array(
                    'for'     => $id, 
                    'caption' => $caption, 
                    'class'   => $caption_class
                ))->render();
			}
            $out .= '</span> &nbsp; &nbsp; ';
		}

        return $out;
	}
}


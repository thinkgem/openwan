<?php
// $Id: listbox.php 2014 2009-01-08 19:01:29Z dualface $

/**
 * 定义 Control_Listbox  类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: listbox.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */

/**
 * 构造列表框
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: listbox.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */
class Control_Listbox extends QUI_Control_Abstract
{
	function render()
	{
		$selected   = $this->_extract('selected');
		$size       = $this->_extract('size');
		$items      = $this->_extract('items');
		$multiple   = $this->_extract('multiple');
		$key        = $this->_extract('key');
		$caption    = $this->_extract('caption');

        if (!is_array($selected) && substr($selected, 0, 1) == ':')
        {
			$selected = intval(substr($selected, 1));
			$selected_by_index = true;
        }
        else
        {
			$selected_by_index = false;
        }

		$out = '<select ';
		$out .= $this->_printIdAndName();
        if ($size <= 0)
        {
			$size = 4;
        }

		$out .= 'size="' . $size . '" ';
        if ($multiple)
        {
			$out .= 'multiple="multiple" ';
        }

		$out .= $this->_printDisabled();
		$out .= $this->_printAttrs();
		$out .= ">\n";

		$items = (array)$items;

        if ($key)
        {
            $this->_splitMultiDimArray($items, $key, $caption);
		}

		$ix = 0;
        foreach ($items as $caption => $value)
        {
			$out .= '<option value="' . htmlspecialchars($value) . '" ';
			$checked = false;
            if ($selected_by_index)
            {
                if (is_array($selected))
                {
                    if (in_array($ix, $selected))
                    {
						$checked = true;
					}
                }
                else if ($ix == $selected)
                {
					$checked = true;
				}
            }
            else
            {
                if (is_array($selected))
                {
                    if (in_array($value, $selected))
                    {
						$checked = true;
					}
                }
                else if ($value == $selected)
                {
					$checked = true;
				}
            }

            if ($checked)
            {
				$out .= 'selected="selected" ';
			}
			$out .= '>';
			$out .= htmlspecialchars($caption);
			$out .= "</option>\n";
			$ix++;
		}
		$out .= "</select>\n";

        return $out;
	}
}


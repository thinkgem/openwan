<?php
// $Id: formfilters.php 895 2010-03-23 05:36:29Z thinkgem $
/**
 * 表单过滤类
 * 表单中用到的各种过滤器
 */
class Helper_FormFilters
{
    // 将诸如 checkboxgroup 之类提交的数组转换为以逗号间隔的字符串
    static function implode_arr($value)
    {
        if (is_array($value)){
            return implode(",", $value);
        }
    }
}














































<?php
// $Id: json.php 895 2010-03-23 05:36:29Z thinkgem $

/**
 * JSON助手
 * @author WangZhen <thinkgem@163.com>
 */
class Helper_JSON {
    public static function encode($value) {
        $code = json_encode($value);
        return preg_replace("#\\\u([0-9a-f]+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $code);
    }
    public static function decode($json) {
        return json_decode($json,true);
    }
}
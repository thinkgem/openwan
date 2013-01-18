<?php
// $Id: nl2br.php 895 2010-03-23 05:36:29Z thinkgem $

class Model_Behavior_Formatter_Nl2br
{
    static function get($obj, $prop, $config, & $props)
    {
        $prop_name = $config['prop_name'];
        $value = $props[$prop_name];
        return nl2br($value);
    }

    static function set($obj, $value, $prop, $config, & $props)
    {
        $prop_name = $config['prop_name'];
        $props[$prop_name] = self::_br2nl($value);
        $obj->willChanged($prop_name);
    }


    static function _br2nl($string)
    {
        return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
    }
}
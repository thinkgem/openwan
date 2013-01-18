<?php
// $Id: date.php 895 2010-03-23 05:36:29Z thinkgem $

class Model_Behavior_Formatter_Date
{
    static function get($obj, $prop, $config, & $props)
    {
        $prop_name = $config['prop_name'];
        $value = $props[$prop_name];
        $format = $config['format'];
        if($value)
        {
            return date($format, $value);
        }
        elseif(! $obj->id() && isset($config['default_value']))
        {
            return date($format, $config['default_value']);
        }
        else
        {
            return null;
        }
    }

    static function set($obj, $value, $prop, $config, & $props)
    {
        $prop_name = $config['prop_name'];
        $time = strtotime($value);
        if(isset($config['safe']) && $config['safe'])
        {
            if($time === false) break;
        }
        $props[$prop_name] = $time;
        $obj->willChanged($prop_name);
    }
}
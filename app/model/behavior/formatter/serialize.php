<?php
// $Id: serialize.php 895 2010-03-23 05:36:29Z thinkgem $

class Model_Behavior_Formatter_Serialize
{
    static function get($obj, $prop, $config, & $props)
    {
        $prop_name = $config['prop_name'];
        $value = $props[$prop_name];
        if(strlen($value))
        {
            return @unserialize(
                preg_replace(
                    '!s:(\d+):"(.*?)";!se',
                    '"s:".strlen("$2").":\"$2\";"',
                    $value
                )
            );
        }
        else
        {
            return isset($config['default_value'])
                ? $config['default_value']
                : null;
        }
    }

    static function set($obj, $value, $prop, $config, & $props)
    {
        $prop_name = $config['prop_name'];
        $props[$prop_name] = serialize($value);
        $obj->willChanged($prop_name);
    }
}
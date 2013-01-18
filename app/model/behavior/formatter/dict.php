<?php
// $Id: dict.php 895 2010-03-23 05:36:29Z thinkgem $

class Model_Behavior_Formatter_Dict
{
    static function get($obj, $prop, $config, & $props)
    {
        $prop_name = $config['prop_name'];
        $value = $props[$prop_name];
        return $config['dict'][$value];
    }

    static function set($obj, $value, $prop, $config, & $props)
    {
        $prop_name = $config['prop_name'];
        $dict = array_flip($config['dict']);
        if(isset($dict[$value]))
        {
            $props[$prop_name] = $dict[$value];
            $obj->willChanged($prop_name);
        }
    }
}
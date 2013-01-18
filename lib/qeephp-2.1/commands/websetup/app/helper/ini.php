<?php

abstract class Helper_Ini
{
    static function value($value)
    {
        if (is_bool($value))
        {
            return $value ? 'true' : 'false';
        }

        if (!is_array($value))
        {
            return h($value);
        }

        $t = str_replace("\n\n", "\n", print_r($value, true));
        return nl2br(str_replace(' ', '&nbsp;', h($t)));
    }
}


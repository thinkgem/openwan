<?php

class MyItem
{
    public $index = 0;

    function __construct($i = 0)
    {
        $this->index = $i;
    }

    function toArray()
    {
        return array('index' => $this->index);
    }
}


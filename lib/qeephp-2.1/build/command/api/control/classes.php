<?php

class Control_Classes extends QUI_Control_Abstract
{
    function render()
    {
        $this->_view = $this->attrs();
        return $this->_fetchView(dirname(__FILE__) . '/classes_view.php');
    }
}



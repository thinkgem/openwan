<?php

class Control_Chapters extends QUI_Control_Abstract
{
    function render()
    {
        $this->_view = $this->attrs();
        return $this->_fetchView(dirname(__FILE__) . '/chapters_view.php');
    }

}


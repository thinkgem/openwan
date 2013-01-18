<?php
// $Id: submenu.php 1937 2009-01-05 19:09:40Z dualface $

class Control_SubMenu extends QUI_Control_Abstract
{
    function render()
    {
        $menu = Menu::instance();
        $this->_view['menu']    = $menu;
        $this->_view['main']    = $menu->getCurrentMainMenu();
        $this->_view['current'] = $menu->getCurrentSubMenu($this->_view['main']['items']);

        return $this->_fetchView(dirname(__FILE__) . '/submenu_view.php');
    }

}

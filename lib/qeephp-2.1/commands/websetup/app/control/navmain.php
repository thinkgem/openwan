<?php
// $Id: navmain.php 1937 2009-01-05 19:09:40Z dualface $

class Control_NavMain extends QUI_Control_Abstract
{
	function render()
    {
		$menu = Menu::instance();
		$this->_view['all_menu'] = $menu->getAll();
		$this->_view['current']  = $menu->getCurrentMainMenu();

		return $this->_fetchView(dirname(__FILE__) . '/navmain_view.php');
	}

}


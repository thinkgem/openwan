<?php
// $Id: category.php 895 2010-03-23 05:36:29Z thinkgem $

/**
 * 资源库分类控件
 * @author WangZhen <thinkgem@163.com>
 */
class Control_Category extends QUI_Control_Abstract
{
    function render()
    {
        $currentUser = MyApp::instance()->currentUser();
        $category = array();
        if ($currentUser['group_id']==1){
            $category = Category::find('enabled=1')->order('weight desc')->getAll();
        }else{
            $user = Users::find()->getById($currentUser['id']);
            $category = $user->group->categorys;
        }
        $this->_view['title'] = $this->title;
        $this->_view['category'] = $category;
        return $this->_fetchView(dirname(__FILE__) . '/category_view');
    }
}

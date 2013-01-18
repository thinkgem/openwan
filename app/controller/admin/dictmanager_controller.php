<?php
// $Id: dictmanager_controller.php 907 2010-08-11 07:44:06Z thinkgem $

/**
 * Controller_Admin_DictManager 字典管理控制器
 * @author WangZhen <thinkgem@163.com>
 */
class Controller_Admin_DictManager extends Controller_Admin_Abstract
{
        protected function _before_execute(){
            parent::_before_execute();
            $this->_pathway->addStep('字典管理');
        }
	function actionIndex(){
            $this->_pathway->addStep('字典管理');
	}
        /**
         * 管理 资源分类
         */
        function actionCategory(){
            $this->_pathway->addStep('资源库分类');
            $category = Category::find()->order('weight desc')->getAll();
            $this->_view['category'] = $category;
        }
        /**
         * 添加 资源分类
         */
        function actionCategoryAdd(){
            $this->_pathway->addStep('添加资源库分类');
            $id = $this->_context->id;
            $form = new Form_Category(url('admin::dictmanager/categoryAdd'));
            $form->add(QForm::ELEMENT, 'parent_id', array('_ui' => 'hidden', 'value' => $id));
            if ($this->_context->isPOST() && $form->validate($_POST)){
                try {
                    $category = new Category($form->values());
                    $category->save();
                    return "{id:'{$category->parent_id}', msg:'添加成功'}";
                }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                    $form->invalidate($ex);
                }
            }
            $this->_view['form'] = $form;
            $this->_viewname = 'categoryedit';
        }
        /**
         * 编辑 资源分类
         */
        function actionCategoryEdit(){
            $this->_pathway->addStep('编辑资源库分类');
            $id = $this->_context->id;
            $category = Category::find()->getById($id);
            if ($category->isNewRecord()){
                return "{msg:'该记录不存在'}";
            }
            $form = new Form_Category(url('admin::dictmanager/categoryEdit'));
            $form->add(QForm::ELEMENT, 'id', array('_ui' => 'hidden'));
            if ($this->_context->isPOST() && $form->validate($_POST)){
                try {//修改并保存数据
                    $category->changeProps($form->values());
                    $category->save();
                    return "{id:'$id', msg:'编辑成功'}";
                }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                    $form->invalidate($ex);
                }
            }else if (!$this->_context->isPOST()){
                $form->import($category);
            }
            $this->_view['form'] = $form;
        }
        /**
         * 删除 资源分类
         */
        function actionCategoryDel(){
            $this->_pathway->addStep('删除资源库分类');
            $id = $this->_context->id;
            if($id==1){return '不能删除';}
            $categorys = Category::find("id = $id or path like '%,$id,%'")->setColumns('id')->getAll();
            $ids = array();//'';
            foreach ($categorys as $key => $category){
                $ids[] = $category->id;//.',';
            }
            //if ($ids != '') $ids = substr($ids, 0, strlen($ids)-1);
            $count = Files::find("category_id in (?)", $ids)->getCount('id');
            if ($count > 0){
                return '该分类不为空，无法删除！';
            }
            $categorys->destroy();
            return 'true';
        }
        /**
         * 管理 文件编目
         */
        function actionCatalog(){
            $this->_pathway->addStep('编目信息');
            $catalog = Catalog::find()->order('weight desc')->getAll();
            $this->_view['catalog'] = $catalog;
        }
        /**
         * 添加 文件编目
         */
        function actionCatalogAdd(){
            $this->_pathway->addStep('添加文件编目信息');
            $id = $this->_context->id;
            if($id==1){return "{msg:'不能添加到根'}";}
            $form = new Form_Catalog(url('admin::dictmanager/catalogAdd'));
            $form->add(QForm::ELEMENT, 'parent_id', array('_ui' => 'hidden', 'value' => $id));
            if ($this->_context->isPOST() && $form->validate($_POST)){
                try {
                    $catalog = new Catalog($form->values());
                    $catalog->save();
                    return "{id:'{$catalog->parent_id}', msg:'添加成功'}";
                }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                    $form->invalidate($ex);
                }
            }
            $this->_view['form'] = $form;
            $this->_viewname = 'catalogedit';
        }
        /**
         * 编辑 文件编目
         */
        function actionCatalogEdit(){
            $this->_pathway->addStep('编辑文件编目信息');
            $id = $this->_context->id;
            $catalog = Catalog::find()->getById($id);
            if ($catalog->isNewRecord()){
                return "{msg:'该记录不存在'}";
            }
            $form = new Form_Catalog(url('admin::dictmanager/catalogEdit'));
            $form->add(QForm::ELEMENT, 'id', array('_ui' => 'hidden'));
            if ($this->_context->isPOST() && $form->validate($_POST)){
                try {//修改并保存数据
                    $catalog->changeProps($form->values());
                    $catalog->save();
                    return "{id:'$id', msg:'编辑成功'}";
                }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                    $form->invalidate($ex);
                }
            }else if (!$this->_context->isPOST()){
                $form->import($catalog);
            }
            $this->_view['form'] = $form;
        }
        /**
         * 删除 文件编目
         */
        function actionCatalogDel(){
            $this->_pathway->addStep('删除文件编目信息');
            $id = $this->_context->id;
            if($id<6){return '不能删除';}
            $catalogs = Catalog::find("id = $id or path like '%,$id,%'")->setColumns('id')->getAll();
            $catalogs->destroy();
            return 'true';
        }

}



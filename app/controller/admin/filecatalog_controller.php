<?php
// $Id: filecatalog_controller.php 907 2010-08-11 07:44:06Z thinkgem $

/**
 * Controller_Admin_FileCatalog 文件编目控制器
 * @author WangZhen <thinkgem@163.com>
 */
class Controller_Admin_FileCatalog extends Controller_Admin_Abstract
{
        protected function _before_execute(){
            parent::_before_execute();
//            $this->_pathway->addStep('素材管理');
        }
	function actionIndex(){
            $this->_pathway->addStep('素材管理');
            //return $this->_redirect(url('admin::fileCatalog/video'));
            $type = $this->_context->type;
            if (!isset($type) || $type <= 1 || $type > 4){
                $type = 1;
                $this->_pathway->addStep('视频编目');
            }else if($type==2){
                $this->_pathway->addStep('音频编目');
            }else if($type==3){
                $this->_pathway->addStep('图片编目');
            }else if($type==4){
                $this->_pathway->addStep('富媒体编目');
            }
            $this->_view['type'] = $type;
            $categoryId = $this->_context->category_id;
            $categoryId = isset($categoryId) ? $categoryId : 1;
            $category = Category::find()->getById($categoryId);
            $this->_view['category'] = $category;
            $categoryIds = Category::getChildrenIds($categoryId);
            if(count($categoryIds)){//所有编目文件
                // 分页查询内容列表
                $page = intval($this->_context->page);
                if ($page < 1) $page = 1;
                $select = Files::find('category_id in (?) and type=? and upload_username=? and (status=0 or status=3)', $categoryIds, $type, $this->_view['currentUser']['username'])->order('upload_at desc');
                $select->limitPage($page, 12);
                // 将分页信息和查询到的数据传递到视图
                $this->_view['pagination'] = $select->getPagination();
                $this->_view['files'] = $select->getAll();
            }
	}
        function actionCatalog(){
            $this->_pathway->addStep('媒资编目');            
            $id = $this->_context->id;
            $file = Files::find()->getById($id);
            if ($file->isNewRecord()){
                return '记录不存在或已删除';
            }
            if (!file_exists($file->path.$file->name.'.'.$file->ext)){
                return '文件不存在或已删除';
            }
            //保存编目信息
            if ($this->_context->isPOST()){
                //保存基本信息
                if (isset($_POST['title']) && $_POST['title']!='')
                    $file->title = $_POST['title'];
                if (isset($_POST['category_id']) && $_POST['category_id']!=''){
                    $category = Category::find()->getById($_POST['category_id']);
                    $file->category_id = $category->id;
                    $file->category_name = $category->name;
                }
                $file->catalogInfo = $this->_context->catalog;
                $file->status = 1;
                $file->catalog_username = $this->_view['currentUser']['username'];
                $file->catalog_at = time();
                try{
                    $file->save();
                }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                    return '编目失败！';
                }
                return '编目完成！';
            }
            $this->_view['file'] = $file;
            $this->_view['category'] = Category::getArrayTree();
        }        
        function actionPreview(){
            $this->_pathway->addStep('资料预览');
            $id = $this->_context->id;
            return Files::getPreviewFileStream($id);
        }        
}



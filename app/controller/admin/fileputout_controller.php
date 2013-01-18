<?php
// $Id: fileputout_controller.php 906 2010-08-06 09:52:59Z thinkgem $

/**
 * Controller_Admin_FilePutOut 文件审核发布控制器
 * @author WangZhen <thinkgem@163.com>
 */
class Controller_Admin_FilePutOut extends Controller_Admin_Abstract
{
        protected function _before_execute(){
            parent::_before_execute();
//            $this->_pathway->addStep('审核发布');
        }
	function actionIndex(){
            $this->_pathway->addStep('审核发布');
            //return $this->_redirect(url('admin::fileCatalog/video'));
            $type = $this->_context->type;
            if (!isset($type) || $type <= 1 || $type > 4){
                $type = 1;
                $this->_pathway->addStep('视频审核发布');
            }else if($type==2){
                $this->_pathway->addStep('音频审核发布');
            }else if($type==3){
                $this->_pathway->addStep('图片审核发布');
            }else if($type==4){
                $this->_pathway->addStep('富媒体审核发布');
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
                $select = Files::find('category_id in (?) and type=? and upload_username=? and status=1', $categoryIds, $type, $this->_view['currentUser']['username'])->order('upload_at desc');
                $select->limitPage($page, 12);
                // 将分页信息和查询到的数据传递到视图
                $this->_view['pagination'] = $select->getPagination();
                $this->_view['files'] = $select->getAll();
            }
	}
        function actionPutout(){
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
                $file->level = $this->_context->level;
                if (!isset($this->_context->groups) || in_array('all', $this->_context->groups)){
                    $file->groups = 'all';
                }else{
                    $file->groups = ',';
                    foreach($this->_context->groups as $v){
                        $file->groups .= $v.',';
                    }
                }
                $file->is_download = $this->_context->is_download;
                $file->status = $this->_context->status;
                $file->putout_username = $this->_view['currentUser']['username'];
                $file->putout_at = time();
                try{
                    $file->save();
                }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                    return '提交失败！';
                }
                //更新索引
                $filesCounter = FilesCounter::find()->getById(1);
                if ($filesCounter->isNewRecord()){
                    $filesCounter = new FilesCounter();
                    $filesCounter->id = 1;
                }                
                $filesCounter->file_id = $file->id();
                try{
                    $filesCounter->save();
                    @exec(Q::ini('appini/search/sphinxDelta'));
                }catch (QDB_ActiveRecord_ValidateFailedException $ex) {
                    return '更新索引失败！';
                }                
                return '提交成功！';
            }
            $this->_view['file'] = $file;
            $this->_view['levels'] = Levels::find('enabled=1')->getAll();
            $this->_view['groups'] = Groups::find('enabled=1')->getAll();
        }
}



<?php
// $Id: filesearch_controller.php 907 2010-08-11 07:44:06Z thinkgem $

/**
 * Controller_Admin_FileSearch 文件检索控制器
 * @author WangZhen <thinkgem@163.com>
 */
class Controller_Admin_FileSearch extends Controller_Admin_Abstract
{
        protected function _before_execute(){
            parent::_before_execute();
//            $this->_pathway->addStep('检索下载');
        }
	function actionIndex(){
            $this->_pathway->addStep('检索下载');
            $type = $this->_context->type;
            if ($type==1){
                $this->_pathway->addStep('视频资料');
            }else if($type==2){
                $this->_pathway->addStep('音频资料');
            }else if($type==3){
                $this->_pathway->addStep('图片资料');
            }else if($type==4){
                $this->_pathway->addStep('富媒体资料');
            }else{
                $type=0;
            }
            $this->_view['type'] = $type;

            require Q::ini('appini/search/sphinxApi');
            $host = Q::ini('appini/search/sphinxHost');
            $port = Q::ini('appini/search/sphinxPort');
            $limit = Q::ini('appini/search/sphinxLimit');

            $level = $this->_view['currentUser']['level_id'];
            $group_id = $this->_view['currentUser']['group_id'];
            $page = intval($this->_context->page);
            if ($page < 1) $page = 1;
            $query = $this->_view['query'] = $this->_context->query;
            
            $s = new SphinxClient();
            $s->SetServer($host, $port);
            $s->SetConnectTimeout(10);
            $s->SetWeights(array(100, 1));
            if ($type >=1 && $type <= 4){
                $s->SetFilter('type', array($type));
            }
            $s->SetFilter('status', array(2));//0:新节目;1:待审核;2:已发布;3:打回;4:删除.
            $s->SetFilterRange('level', 0, $level);
            $s->SetLimits(($page-1)*$limit, $limit, 1000);
            $s->SetArrayResult(true);
            $s->SetMatchMode(SPH_MATCH_EXTENDED);//设置匹配模式为Sphinx内部语言表达式
            $s->SetSortMode(SPH_SORT_EXPR, '@id');//设置排序模式
            $result = $s->Query("$query @groups '(,$group_id,)|(all)'");
            if($result){
                //获得文件
                if(isset($result['matches'])){
                    $ids = array();
                    foreach($result['matches'] as $v) {
                        $ids[] = $v['id'];
                    }
                    $files = Files::find('id in (?)',$ids)->order('id desc')->getAll();
                    $this->_view['files'] = $files;
                }
                $result['start'] = ($page-1)*$limit+1>$result['total']?$result['total']:($page-1)*$limit+1;
                $result['end'] = $result['start']+$limit-1>$result['total']?$result['total']:$result['start']+$limit-1;
                $this->_view['result'] = $result;
                //生成页码控制
                $pagination = array();
                $pagination['record_count'] = $result['total'];
                $pagination['page_count'] = ceil($result['total'] / $limit);
                $pagination['first'] = 1;
                $pagination['last'] = $pagination['page_count'];
                if ($pagination['last'] < $pagination['first']){
                    $pagination['last'] = $pagination['first'];
                }
                if ($page >= $pagination['page_count'] + 1){
                    $page = $pagination['last'];
                }
                if ($page < 1){
                    $page = $pagination['first'];
                }
                if ($page < $pagination['last'] - 1){
                    $pagination['next'] = $page + 1;
                }else{
                    $pagination['next'] = $pagination['last'];
                }
                if ($page > 1){
                    $pagination['prev'] = $page - 1;
                }else{
                    $pagination['prev'] = $pagination['first'];
                }
                $pagination['current'] =  $page;
                $pagination['page_size'] = $limit;
                $pagination['page_base'] = 1;
                $this->_view['pagination'] = $pagination;
            }            
//            $categoryId = $this->_context->category_id;
//            $categoryId = isset($categoryId) ? $categoryId : 1;
//            $category = Category::find()->getById($categoryId);
//            $this->_view['category'] = $category;
//            $categoryIds = Category::getChildrenIds($categoryId);
//            if(count($categoryIds)){//所有编目文件
//                // 分页查询内容列表
//                $page = intval($this->_context->page);
//                if ($page < 1) $page = 1;
//                $select = Files::find('category_id in (?) and type=? and upload_username=? and status=2 and (groups=\'\' or groups like \'%,?,%\') and level <= ?', $categoryIds, $type, $this->_view['currentUser']['username'], $this->_view['currentUser']['group_id'], $this->_view['currentUser']['level_id'])->order('upload_at desc');
//                $select->limitPage($page, 12);
//                // 将分页信息和查询到的数据传递到视图
//                $this->_view['pagination'] = $select->getPagination();
//                $this->_view['files'] = $select->getAll();
//            }
	}
        function actionView(){
            $this->_pathway->addStep('资料详情');
            $id = $this->_context->id;            
            $level = $this->_view['currentUser']['level_id'];
            $group_id = $this->_view['currentUser']['group_id'];
            $file = Files::find('id=? and level<=? and (groups like "%,?,%" or groups="all")', $id, $level, $group_id)->getOne();
            if ($file->isNewRecord()){
                return '您没有阅读权限';
            }
            $this->_view['file'] = $file;
        }
        function actionDownload(){
            $this->_pathway->addStep('资料下载');
            $id = $this->_context->id;
            return Files::getFileStream($id);
        }
}



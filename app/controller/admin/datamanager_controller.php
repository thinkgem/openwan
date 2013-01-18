<?php
// $Id$

/**
 * Controller_Admin_DataManager 数据管理控制器
 * @author WangZhen <thinkgem@163.com>
 */
class Controller_Admin_DataManager extends Controller_Abstract
{

	protected function _before_execute(){
            parent::_before_execute();
            $this->_pathway->addStep('数据管理');
        }
	function actionIndex(){
            $this->_pathway->addStep('数据管理');
	}
        function actionMigration(){
            $this->_pathway->addStep('数据迁移');
            $category = Category::find()->order('weight desc')->getAll();
            $this->_view['category'] = $category;
        }
        function actionMigrationOut(){
            $this->_pathway->addStep('数据迁出');
            QLog::log('开始数据迁出！');
            try{
                $db = QDB::getConn();
                $db->startTrans();
                $db->completeTrans();
            }catch(QException $ex){
                QLog::log($ex->getMessage(), QLog::ERR);
            }
            echo '正在迁移，请稍后...<br/>';
            for ($i=0; $i<100; $i++){
                QLog::log('正在迁出数据：'.$i);
                echo $i . '<br/>';
            }
            QLog::log('数据迁出完成！');
            return '迁出成功';
        }
        function actionMigrationIn(){
            $this->_pathway->addStep('数据迁入');
            return '迁入成功';
        }
}



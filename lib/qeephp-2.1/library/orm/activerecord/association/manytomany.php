<?php
// $Id: manytomany.php 2677 2009-12-18 07:07:30Z firzen $

/**
 * 定义 QDB_ActiveRecord_Association_ManyToMany 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: manytomany.php 2677 2009-12-18 07:07:30Z firzen $
 * @package orm
 */

/**
 * QDB_ActiveRecord_Association_ManyToMany 类封装 ActiveRecord 对象之间的 many to many 关联
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: manytomany.php 2677 2009-12-18 07:07:30Z firzen $
 * @package orm
 */
class QDB_ActiveRecord_Association_ManyToMany extends QDB_ActiveRecord_Association_Abstract
{
	public $one_to_one = false;

    function init()
    {
        if ($this->_inited) { return $this; }
        parent::init();
        $p = $this->_init_config;

        if (empty($p['mid_class']))
        {
        	// 如果没有指定中间表对应的 ActiveRecord 类，则使用表数据入口直接处理中间表
        	$this->mid_meta = null;

            if (empty($p['mid_table_name']))
            {
            	// 尝试自动确定中间表名称
                $t1 = $this->source_meta->table->name;
                $t2 = $this->target_meta->table->name;
                if ($t1 <= $t2)
                {
                    $mid_table_name = $t1 . '_has_' . $t2;
                }
                else
                {
                    $mid_table_name = $t2 . '_has_' . $t1;
                }
            }
            else
            {
                $mid_table_name = $p['mid_table_name'];
            }

            $this->mid_table = new QDB_Table(array('name' => $mid_table_name));
        }
        else
        {
            // 如果中间表作为实体，则由指定的 ActiveRecord 继承类负责处理中间表
            $this->mid_meta = QDB_ActiveRecord_Meta::instance($p['mid_class']);
            $this->mid_table = $this->mid_meta->table;
        }

        $this->source_key = !empty($p['source_key'])
            ? $p['source_key']
            : reset($this->source_meta->idname);
        $this->target_key = !empty($p['target_key'])
            ? $p['target_key']
            : reset($this->target_meta->idname);
        $this->mid_source_key = !empty($p['mid_source_key'])
            ? $p['mid_source_key']
            : reset($this->source_meta->idname);
        $this->mid_target_key = !empty($p['mid_target_key'])
            ? $p['mid_target_key']
            : reset($this->target_meta->idname);
        $this->mid_mapping_to = !empty($p['mid_mapping_to'])
            ? $p['mid_mapping_to']
            : 'mid_data';

        $class = $this->target_meta->class_name;
        // $this->source_meta->addDynamicMethod("bind{$class}", array($this, 'bindTarget'));
        // $this->source_meta->addDynamicMethod("unbind{$class}", array($this, 'unbindTarget'));

        unset($this->_init_config);
        return $this;
    }

    function onSourceSave(QDB_ActiveRecord_Abstract $source, $recursion)
    {
    	return $this->_updateRelation($source, $recursion);
    }

    function onSourceDestroy(QDB_ActiveRecord_Abstract $source)
    {
    	$source->{$this->mapping_name} = array();
    	return $this->_updateRelation($source);
    }

    /**
     * 添加和一个对象的关联关系
     *
     * @param QDB_ActiveRecord_Abstract $source
     * @param QDB_ActiveRecord_Abstract $target
     *
     * @return QDB_ActiveRecord_Association_ManyToMany
     */
    function bindRelatedObject(QDB_ActiveRecord_Abstract $source, QDB_ActiveRecord_Abstract $target)
    {
        $this->init();

        if ($this->mid_meta)
        {
        }
        else
        {
	        $conn = $this->mid_table->getConn();
	        $target->save($this->on_save);
	        $source_key_value = $source->{$this->source_key};
	        $target_key_value = $target->{$this->target_key};

	        $sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s = %s AND %s = %s',
	                       $conn->qid($this->mid_table->getFullTableName()),
	                       $conn->qid($this->mid_source_key),
	                       $conn->qstr($source_key_value),
	                       $conn->qid($this->mid_target_key),
	                       $conn->qstr($target_key_value)
	        );
	        #公共第二关联键
	        if (strlen($this->mid_common_key)){
	        	$sql.=sprintf(' AND %s=%s',
	        		$conn->qid($this->mid_common_key),
	        		$conn->qstr($source->{$this->mid_common_key})
        		);
	        }
	        if (intval($conn->getOne($sql)) < 1)
	        {
	            $this->mid_table->insert(array
	            (
	                $this->mid_source_key => $source_key_value,
	                $this->mid_target_key => $target_key_value,
	            ));
	        }
        }

        return $this;
    }

    /**
     * 解除和一个对象的关联关系
     *
     * @param QDB_ActiveRecord_Abstract $source
     * @param QDB_ActiveRecord_Abstract $target
     *
     * @return QDB_ActiveRecord_Association_ManyToMany
     */
    function unbindRelatedObject(QDB_ActiveRecord_Abstract $source, QDB_ActiveRecord_Abstract $target)
    {
    }

    /**
     * 更新对象间的关联关系
     *
     * @param QDB_ActiveRecord_Abstract $source
     * @param int $recursion
     *
     * @return QDB_ActiveRecord_Association_Abstract
     */
    protected function _updateRelation(QDB_ActiveRecord_Abstract $source, $recursion = 1)
    {
        $this->init();

        if (!isset($source->{$this->mapping_name}))
        {
            return $this;
        }

        if ($this->mid_meta)
        {
            return $this->_updateRelationByMeta($source, $source->{$this->mapping_name}, $recursion);
        }
        else
        {
            return $this->_updateRelationByTable($source, $source->{$this->mapping_name}, $recursion);
        }
    }

    /**
     * 使用 ActiveRecord 来操作中间表，并更新对象之间的 many_to_many 关系
     *
     * @param QDB_ActiveRecord_Abstract $source
     * @param array|QColl $targets
     * @param int $recursion
     *
     * @return QDB_ActiveRecord_Association_ManyToMany
     */
    protected function _updateRelationByMeta(QDB_ActiveRecord_Abstract $source, $targets, $recursion)
    {
        /**
         * 算法：
         *
         * 1、取出中间表中已有的关联关系
         * 2、和应用程序提供的关联关系进行比对
         * 3、确定要添加的关系
         * 4、确定要删除的关系
         */
        foreach ($targets as $obj)
        {
            $obj->save($recursion - 1, $this->on_save);
            $v = $obj->{$this->target_key};
            $targets[$v] = $v;
        }

        // 取出现有的关联信息
        $source_key_value = $source->{$this->source_key};
        $select=$this->mid_meta->find(array($this->mid_source_key => $source_key_value));
        #第二关联键
        if (strlen($this->mid_common_key)){
        	$select->where(array($this->mid_common_key=>$source->{$this->mid_common_key}));
        }
        $exists = $select->all()
                         ->asColl()
                         ->query();

        /* @var $exists QColl */

        // 然后确定要添加和删除的关联信息
        $added = array();
        foreach ($targets as $target)
        {
        	$v = $target->{$this->target_key};
            if (!$exists->search($this->target_key, $v))
            {
                $added[] = $v;
            }
        }

        $removed = array();
        foreach ($exists as $exist)
        {
        	$v = $exist->{$this->target_key};
        	if (!isset($targets[$v]))
        	{
        		$removed[] = $v;
        	}
        }

        // 添加新增的关联信息
        foreach ($added as $mid_target_key_value)
        {
        	$mid = $this->mid_meta->newObject();
        	$mid->changePropForce($this->mid_source_key, $source_key_value);
        	$mid->changePropForce($this->mid_target_key, $mid_target_key_value);
        	#公共关联键
        	if (strlen($this->mid_common_key)){
        		$mid->changePropForce($this->mid_common_key,$source->{$this->mid_common_key});
        	}
        	$mid->save();
        }

        // 删除多余的关联信息
        if (!empty($removed))
        {
        	foreach ($removed as $removed_id){
        		#第二关联键
        		if (strlen($this->mid_common_key)){
        			$this->mid_meta
        				->destroyWhere("{$this->mid_target_key} = ? and {$this->mid_source_key}=? and {$this->mid_common_key}=?",$removed_id,$source_key_value,$source->{$this->mid_common_key});
        		}else{
        			$this->mid_meta->destroyWhere("{$this->mid_target_key} = ? and {$this->mid_source_key}=?",$removed_id,$source_key_value);
        		}
        	}
//            $this->mid_meta->destroyWhere("{$this->mid_target_key} IN (?)", $removed);
        }

        return $this;
    }

    /**
     * 使用表数据入口来操作中间表，并更新对象之间的 many_to_many 关系
     *
     * @param QDB_ActiveRecord_Abstract $source
     * @param array|QColl $targets
     *
     * @return QDB_ActiveRecord_Association_ManyToMany
     */
    protected function _updateRelationByTable(QDB_ActiveRecord_Abstract $source, $targets, $recursion)
    {
        // 取出现有的关联信息
        $conn = $this->mid_table->getConn();

        $target_key_values = array();
        foreach ($targets as $obj)
        {
            $obj->save($this->on_save, $recursion - 1);
        	$target_key_values[] = $obj->{$this->target_key};
        }

        $source_key_value = $source->{$this->source_key};
        $sql = sprintf('SELECT %s FROM %s WHERE %s = %s',
                       $conn->qid($this->mid_target_key),
                       $conn->qid($this->mid_table->getFullTableName()),
                       $conn->qid($this->mid_source_key),
                       $conn->qstr($source_key_value)
        );
        if (strlen($this->mid_common_key)){
        	$sql.=sprintf(' AND %s =%s',
        		$conn->qid($this->mid_common_key),
        		$conn->qstr($source->{$this->mid_common_key})
        	);
        }
        $exists_mid = $conn->getCol($sql);

        // 然后确定要添加和删除的关联信息
        $insert_mid = array_diff($target_key_values, $exists_mid);
        $remove_mid = array_diff($exists_mid, $target_key_values);

        // 添加新增的关联信息
        foreach ($insert_mid as $mid_target_key_value)
        {
        	$insert_array=array
            (
                $this->mid_source_key => $source_key_value,
                $this->mid_target_key => $mid_target_key_value,
            );
            if (strlen($this->mid_common_key)){
            	$insert_array[$this->mid_common_key]=$source->{$this->mid_common_key};
            }
            $this->mid_table->insert($insert_array);
        }

        // 删除多余的关联信息
        if (!empty($remove_mid))
        {
        	if (strlen($this->mid_common_key)){
        		$this->mid_table->delete("{$this->mid_source_key} = ? AND {$this->mid_target_key} IN (?) AND {$this->mid_common_key} =?",
	        	                         $source_key_value, $remove_mid,$source->{$this->mid_common_key});
        	}else{
	        	$this->mid_table->delete("{$this->mid_source_key} = ? AND {$this->mid_target_key} IN (?)",
	        	                         $source_key_value, $remove_mid);
        	}
        }

        return $this;
    }

}


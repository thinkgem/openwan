<?php
// $Id: hasone.php 2677 2009-12-18 07:07:30Z firzen $

/**
 * 定义 QDB_ActiveRecord_Association_HasOne 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: hasone.php 2677 2009-12-18 07:07:30Z firzen $
 * @package orm
 */

/**
 * QDB_ActiveRecord_Association_HasOne 类封装了对象见的一对一关系
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: hasone.php 2677 2009-12-18 07:07:30Z firzen $
 * @package orm
 */
class QDB_ActiveRecord_Association_HasOne extends QDB_ActiveRecord_Association_HasMany
{
	public $one_to_one = true;
	public $on_save = 'save';

    function onSourceSave(QDB_ActiveRecord_Abstract $source, $recursion)
    {
        $this->init();
        $mapping_name = $this->mapping_name;
        if ($this->on_save === 'skip' 
            || $this->on_save === false
            || !isset($source->{$mapping_name}))
        {
            return $this;
        }

        $source_key_value = $source->{$this->source_key};
        $obj = $source->{$mapping_name};

        if ($obj[$this->target_key] != $source_key_value
            || $obj->isNewRecord()
            || $obj->changed())
        {
            /* @var $obj QDB_ActiveRecord_Abstract */
            $obj->changePropForce($this->target_key, $source_key_value);
            #第二关联键
            if (strlen($this->target_key_2nd) && strlen($this->source_key_2nd)){
            	$obj->changPropForce($this->target_key_2nd,$obj->{$this->source_key_2nd});
            }
            
            $obj->save($recursion - 1, $this->on_save);
        }

        return $this;
    }

    function addRelatedObject(QDB_ActiveRecord_Abstract $source, QDB_ActiveRecord_Abstract $target)
    {
    	return $this;
    }
}


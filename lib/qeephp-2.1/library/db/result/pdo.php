<?php
// $Id: pdo.php 1937 2009-01-05 19:09:40Z dualface $

/**
 * 定义 QDB_Result_Pdo 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: pdo.php 1937 2009-01-05 19:09:40Z dualface $
 * @package database
 */

/**
 * QDB_Result_Pdo 类封装了 PDO 查询结果
 *
 * @author yangyi.cn.gz@gmail.com
 * @version $Id: pdo.php 1937 2009-01-05 19:09:40Z dualface $
 * @package database
 */
class QDB_Result_Pdo extends QDB_Result_Abstract {
    protected function _getFetchMode() {
        $fetch_mode = PDO::FETCH_BOTH;

        if (QDB::FETCH_MODE_ASSOC == $this->fetch_mode) {
            $fetch_mode = PDO::FETCH_ASSOC;
        }

        return $fetch_mode;
    }

    public function free() {
        $this->_handle = null;
    }

    public function fetchAll() {
        return $this->_handle->fetchAll($this->_getFetchMode());
    }

    public function fetchRow() {
        return $this->_handle->fetch($this->_getFetchMode());
    }

    public function fetchCol($column = 0) {
        return $this->_handle->fetchAll(PDO::FETCH_COLUMN, $column);
    }

    /**
     * 从查询句柄提取记录集，以指定的字段名为数组的key
     * 如果不指定key，以记录的第一个字段为key
     *
     * @param string $key
     * 
     * @return array
     */
    public function fetchAssoc($key = null) {
        if (null === $key) {
            $meta = $this->getColumnsMeta(0);
            $key = $meta['name'];
        }
        $rowset = array();
        while ($row = $this->fetchRow()) {
            $rowset[$row[$key]] = $row;
        }
        return $rowset;
    }

    public function getColumnsMeta($column = null) {
        if (null === $column) {
            $meta = array();
            for ($i = 0, $len = $this->_handle->columnCount(); $i < $len; $i++) {
                $meta[] = $this->_handle->getColumnMeta($i);
            }
        } else {
            $meta = $this->_handle->getColumnMeta($column);
        }

        return $meta;
    }
}


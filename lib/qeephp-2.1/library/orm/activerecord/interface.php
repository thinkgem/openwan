<?php
// $Id: interface.php 2234 2009-02-11 07:52:10Z dualface $

/**
 * 定义 QDB_ActiveRecord_Interface 接口
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: interface.php 2234 2009-02-11 07:52:10Z dualface $
 * @package orm
 */

/**
 * QDB_ActiveRecord_Interface 接口确定了所有 QDB_ActiveRecord_Abstract 继承类必须实现的静态方法
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: interface.php 2234 2009-02-11 07:52:10Z dualface $
 * @package orm
 */
interface QDB_ActiveRecord_Interface
{
    /**
     * 自动填充常量
     */
    //! 当前日期和时间
    const AUTOFILL_DATETIME     = '@#@_current_datetime_@#@';
    //! 当前日期
    const AUTOFILL_DATE         = '@#@_current_date_@#@';
    //! 当前时间
    const AUTOFILL_TIME         = '@#@_current_time_@#@';
    //! 当前 UNIX TIMESTAMP
    const AUTOFILL_TIMESTAMP    = '@#@_current_timestamp_@#@';

    /**
     * 返回对象的定义
     *
     * @return array
     */
    static function __define();

    /**
     * 开启一个查询，查找符合条件的对象或对象集合
     *
     * @return QDB_Select
     */
    static function find();

    /**
     * 返回当前 ActiveRecord 类的元数据对象
     *
     * @return QDB_ActiveRecord_Meta
     */
    static function meta();
}


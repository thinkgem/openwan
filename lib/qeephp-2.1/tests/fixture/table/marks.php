<?php
/////////////////////////////////////////////////////////////////////////////
// QeePHP Framework
//
// Copyright (c) 2005 - 2008 QeeYuan China Inc. (http://www.qeeyuan.com)
//
// 许可协议，请查看源代码中附带的 LICENSE.TXT 文件，
// 或者访问 http://www.qeephp.org/ 获得详细信息。
/////////////////////////////////////////////////////////////////////////////

/**
 * 定义 Table_Marks
 *
 * @package test-fixture
 * @version $Id: marks.php 1937 2009-01-05 19:09:40Z dualface $
 */

/**
 * Table_Marks 封装了对 marks 表的操作
 *
 * @package test-fixture
 */
class Table_Marks extends QDB_Table
{
    public $table_name = 'marks';
    public $pk = 'content_id, author_id';
}

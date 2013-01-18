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
 * 定义 Table_ContentsTags 类
 *
 * @package test-fixture
 * @version $Id: contentstags.php 1937 2009-01-05 19:09:40Z dualface $
 */

/**
 * Table_ContentsTags 封装了 contents_has_tags 表的操作
 *
 * @package test-fixture
 */
class Table_ContentsTags extends QDB_Table
{
    public $table_name = 'contents_has_tags';
    public $pk = 'content_id, tag_id';
}

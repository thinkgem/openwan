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
 * 定义 Table_Comments 类
 *
 * @package test-fixture
 * @version $Id: comments.php 1937 2009-01-05 19:09:40Z dualface $
 */

/**
 * Table_Comments 封装了对 comments 表的操作
 *
 * @package test-fixture
 */
class Table_Comments extends QDB_Table
{
    public $table_name = 'comments';
    public $pk = 'comment_id';

    protected $belongs_to = array(
        array(
            'table_class'   => 'Table_Contents',
            'mapping_name'  => 'content',
            'target_key'    => 'content_id',
            'count_cache'   => 'comments_count',
            'on_find_keys'  => 'title',
        ),

        array(
            'table_class'   => 'Table_Authors',
            'mapping_name'  => 'author',
            'target_key'    => 'author_id',
            'count_cache'   => 'comments_count',
            'on_find_keys'  => 'name',
        )
    );
}

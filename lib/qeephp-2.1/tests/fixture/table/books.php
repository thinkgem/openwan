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
 * 定义 Table_Books 类
 *
 * @package test-fixture
 * @version $Id: books.php 1937 2009-01-05 19:09:40Z dualface $
 */

/**
 * Table_Books 类封装 books 表的操作
 *
 * @package test-fixture
 */
class Table_Books extends QDB_Table
{
    public $table_name = 'books';
    public $pk = 'book_code';

    protected $many_to_many = array(
        array(
            'table_class' => 'Table_Authors',
            'mapping_name' => 'authors',
            'mid_table_class' => 'Table_BooksHasAuthors',
            'mid_on_find_keys' => 'remark',
        )
    );
}

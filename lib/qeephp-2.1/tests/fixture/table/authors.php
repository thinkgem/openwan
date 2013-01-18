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
 * 定义 Table_Authors 类
 *
 * @package test_fixture
 * @version $Id: authors.php 1937 2009-01-05 19:09:40Z dualface $
 */

/**
 * Table_Authors 封装了对 authors 表的操作
 *
 * @package test_fixture
 */
class Table_Authors extends QDB_Table
{
    public $table_name = 'authors';
    public $pk = 'author_id';

    protected $has_many = array(
        /**
         * 每个作者拥有多个内容
         */
        array(
            'table_class'   => 'Table_Contents',
            'mapping_name'  => 'contents',
            'source_key'    => 'author_id',
            'count_cache'   => 'contents_count',

            /**
             * 指示在读取作者记录时，是否读取关联的内容记录
             */
            'on_find' => 5,

            /**
             * 指示在读取关联的内容记录时，只需要内容记录的哪些字段
             */
            'on_find_keys' => 'content_id, title',

            /**
             * 指示按照什么排序规则查询关联的内容记录
             */
            'on_find_order' => '[content_id] ASC',

            /**
             * 指示在删除作者记录时，如何处理关联的内容记录
             */
            'on_delete' => 'cascade',

            /**
             * 指示在保存作者记录时，是否保存关联的内容记录
             */
            'on_save' => 'skip',
        ),

        /**
         * 每个作者拥有多个评论
         */
        array(
            'table_class'   => 'Table_Comments',
            'mapping_name'  => 'comments',
            'source_key'    => 'author_id',
            'count_cache'   => 'comments_count',

            /**
             * 指示在删除作者记录时，如何处理关联的评论记录
             */
            'on_delete'      => 'set_value',

            /**
             * 要填充的值
             */
            'on_delete_set_value' => 0,
        ),
    );

    protected $many_to_many = array(
        array(
            'table_class' => 'Table_Books',
            'mapping_name' => 'books',
            'mid_table_class' => 'Table_BooksHasAuthors',
            'mid_on_find_keys' => 'remark',
            'on_find_keys' => 'title',
        )
    );

}

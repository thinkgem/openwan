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
 * 定义 Table_Contents 类
 *
 * @package test_fixture
 * @version $Id: contents.php 1937 2009-01-05 19:09:40Z dualface $
 */

/**
 * Table_Contents 类封装了 contents 表的操作以及与相关表的关联操作
 *
 * @package test_fixture
 */
class Table_Contents extends QDB_Table
{
    /**
     * 不包含全局前缀的数据表名称
     *
     * @var string
     */
    public $table_name = 'contents';

    /**
     * 主键字段名
     *
     * @var string
     */
    public $pk = 'content_id';

    /**
     * belongs to 关联
     *
     * @var array
     */
    protected $belongs_to = array(
        /**
         * 每个内容都有一个作者
         */
        array(
            'table_class'   => 'Table_Authors',
            'mapping_name'  => 'author',
            'source_key'    => 'author_id',
            /**
             * 指示在 Table_Authors 封装的数据表中使用什么字段存储关联内容的记录总数
             */
            'count_cache'   => 'contents_count',

            /**
             * 指示在读取作者信息时，只获取作者表的哪些字段
             */
            'on_find_keys' => array('author_id', 'name' => 'name_alias'),
        ),
    );

    /**
     * has many 关联
     *
     * @var array
     */
    protected $has_many = array(
        /**
         * 每个内容有多个评论
         */
        array(
            'table_class'   => 'Table_Comments',
            'mapping_name'  => 'comments',
            'target_key'    => 'content_id',
            /**
             * count_cache 指示在 contents 表中用什么字段存储关联评论的记录总数
             */
            'count_cache'   => 'comments_count',
        ),

        /**
         * 每个内容有多个评分
         */
        array(
            'table_class'       => 'Table_Marks',
            'mapping_name'      => 'marks',
            /**
             * 指示用什么字段存储对内容评分的平均值
             *
             * 类似的统计功能还有 count_cache, sum_cache、min_cache、max_cache
             */
            'avg_cache'         => 'marks_avg',
        ),
    );

    /**
     * many to many 关联
     *
     * @var array
     */
    protected $many_to_many = array(
        /**
         * 每个内容可以对应多个标签，一个标签也可以对应多个内容
         */
        array(
            'table_class'     => 'Table_Tags',
            'mapping_name'    => 'tags',
            /**
             * 指示用哪一个表数据入口对象处理内容和标签之间的关联关系
             */
            'mid_table_class' => 'Table_ContentsTags',
        ),
     );
}

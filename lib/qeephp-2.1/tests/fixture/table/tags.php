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
 * 定义 Table_Tags 类
 *
 * @package test-fixture
 * @version $Id: tags.php 1937 2009-01-05 19:09:40Z dualface $
 */

/**
 * Table_Tags 类封装了对 tags 表的操作
 *
 * @package test-fixture
 */
class Table_Tags extends QDB_Table
{
    public $table_name = 'tags';
    public $pk = 'tag_id';

    protected $many_to_many = array(
        array(
            'table_class'    => 'Table_Contents',
            'mapping_name'   => 'contents',
            'mid_table_name' => 'contents_has_tags',
            'on_find'        => 'skip',
        ),
    );

}

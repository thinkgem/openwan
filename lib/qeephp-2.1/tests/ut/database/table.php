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
 * 针对表数据入口的单元测试（单表 CRUD 操作）
 *
 * @package tests
 * @version $Id: table.php 2144 2009-01-23 19:35:56Z dualface $
 */

require_once dirname(__FILE__) . '/../../_include.php';

class Test_QDB_Table_Basic extends PHPUnit_Framework_TestCase
{
	/**
	 * @var QDB_Table
	 */
	protected $table;

	protected function setUp()
	{
		$dsn = Q::ini('db_dsn_pool/default');
		if (empty($dsn)) {
			Q::changeIni('db_dsn_pool/default', Q::ini('db_dsn_mysql'));
		}
		$conn = QDB::getConn();
		$params = array(
			'name' 		 => 'posts',
			'pk'         => 'post_id',
			'conn'       => $conn
		);
		$this->table = new QDB_Table($params);
	}

	function testSelect()
	{
		$select = $this->table->select();
		$this->assertType('QDB_Select', $select);
	}

	function testFind2()
	{
		$conditions = '[post_id] = :post_id AND created > :created';
		$select = $this->table->select($conditions, array('post_id' => 1, 'created' => 0));
		$actual = trim($select->__toString());
		$expected = 'SELECT `q_posts`.* FROM `qeephp_test_db`.`q_posts` WHERE ((`q_posts`.`post_id` = 1 AND created > 0))';
		$actual = str_replace("\n", '', $actual);
		$this->assertEquals($expected, $actual);
	}

	function testInsert()
	{
		for ($i = 0; $i < 10; $i++)
		{
			$row = array(
				'title' => 'Title ' . mt_rand(),
				'body' => 'Body ' . mt_rand(),
			);
			$id = $this->table->insert($row, true);
			$this->assertFalse(empty($id));
			$id = reset($id);

			$find = $this->table->getConn()->getAll("SELECT * FROM {$this->table->getFullTableName()} WHERE post_id = {$id}");
			$this->assertType('array', $find);
			$find = reset($find);
			$this->assertEquals($row['title'], $find['title']);
			$this->assertEquals($row['body'], $find['body']);
		}
	}

	function testUpdate()
	{
		$row = array(
			'title' => 'Title ' . mt_rand(),
			'body' => 'Body ' . mt_rand(),
		);
		$id = $this->table->insert($row, true);
		$this->assertFalse(empty($id));
		$id = reset($id);

		$sql = "SELECT * FROM {$this->table->getFullTableName()} WHERE post_id = {$id}";
		$find = $this->table->getConn()->getRow($sql);

		sleep(1);

		$find['title'] = 'Title ' . mt_rand();
		$this->table->update($find);
		$affected_rows = $this->table->getConn()->affectedRows();
		$this->assertEquals(1, $affected_rows);
	}

	function testUpdateMulti()
	{
		$count = $this->table->getConn()->getOne("SELECT COUNT(*) AS row_count FROM {$this->table->getFullTableName()}");

		$pairs = array('title' => 'Title ' . mt_rand());
		$this->table->update($pairs, null);
		$affected_rows = $this->table->getConn()->affectedRows();

		$this->assertEquals($count, $affected_rows);
	}

	function testUpdateMultiWhere()
	{
		$count = (int)$this->table->getConn()->getOne("SELECT COUNT(*) AS row_count FROM {$this->table->getFullTableName()}");

		$pairs = array('title' => 'Title ' . -1, 'body' => 'Body ' . -1);
		$this->table->update($pairs, 'created > ?', 0);
		$affected_rows = $this->table->getConn()->affectedRows();
		$this->assertEquals($count, $affected_rows);
	}

	function testDelete()
	{
		$sql = "SELECT post_id FROM {$this->table->getFullTableName()} ORDER BY post_id ASC";
		$id = $this->table->getConn()->getOne($sql);

		$this->table->delete($id);
		$affected_rows = $this->table->getConn()->affectedRows();
		$this->assertEquals(1, $affected_rows);
		$sql = "SELECT post_id FROM {$this->table->getFullTableName()} WHERE post_id = {$id}";
		$row = $this->table->getConn()->getAll($sql);
		$this->assertTrue(empty($row));
	}

	function testDeleteWhere()
	{
		$row = array('title' => 'delete', 'body' => 'delete');
		$count = 5;
		for ($i = 0; $i < $count; $i++)
		{
			$this->table->insert($row);
		}

		$this->table->delete('title = ? AND body = ?', 'delete', 'delete');
		$affected_rows = $this->table->getConn()->affectedRows();
		$this->assertEquals($count, $affected_rows);

		$this->table->delete(null);
		$affected_rows = $this->table->getConn()->affectedRows();
		$this->assertTrue($affected_rows > 1);
	}

	function testNextID()
	{
		$id = $this->table->nextID();
		$next_id = $this->table->nextID();
		$this->assertTrue($next_id > $id);
	}

}

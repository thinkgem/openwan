<?php

/**
 * 测试 QDB 的 MySQL 驱动
 *
 * @package tests
 * @version $Id: mysql.php 2144 2009-01-23 19:35:56Z dualface $
 */

require dirname(__FILE__) . '/_include.php';

/**
 * MySQL 驱动的单元测试
 *
 * @package tests
 */
class Test_QDB_Adapter_MySQL extends Test_QDB_Adapter_Abstract
{

	function testQstr()
	{
		$checks = array(
			array('12345', "'12345'"),
			array(12345, 12345),
			array(true, 1),
			array(false, 0),
			array(null, 'NULL'),
			array('string', "'string'"),
			array("string'string", "'string\\'string'"),
		);
		$this->qstr($checks);
	}

	function testQid()
	{
	}

	function testExecute()
	{
		$sql = "INSERT INTO q_posts (title, body, created, updated) VALUES (?, ?, ?, ?)";
		$args = array('title', 'body', time(), time());
		$this->execute($sql, $args);
	}


	function testParseSQLString1()
	{
		$where = 'user_id = 1';
		$this->assertEquals($where, $this->dbo->parseSQL('q_posts', $where));
	}

	function testParseSQLString2()
	{
		$where = 'user_id = ?';
		$actual = $this->dbo->parseSQL('q_posts', $where, 1);
		$this->assertEquals('user_id = 1', $actual);
	}

	function testParseSQLString3()
	{
		$where = 'user_id IN (?)';
		$actual = $this->dbo->parseSQL('q_posts', $where, array(1, 2, 3));
		$this->assertEquals('user_id IN (1,2,3)', $actual);
	}

	function testParseSQLString4()
	{
		$where = '[user_id] = ? AND [level_ix] > ?';
		$expected = '`q_posts`.`user_id` = 1 AND `q_posts`.`level_ix` > 3';
		$actual = $this->dbo->parseSQL('q_posts', $where, 1, 3);
		$this->assertEquals($expected, $actual);
	}

	function testParseSQLString5()
	{
		$where = '[posts.user_id] = :user_id AND [level.level_ix] > :level_ix';
		$expected = '`posts`.`user_id` = 2 AND `level`.`level_ix` > 55';
		$actual = $this->dbo->parseSQL('q_posts', $where, array('user_id' => 2, 'level_ix' => 55));
		$this->assertEquals($expected, $actual);
	}

	function testParseSQLString6()
	{
		$where = '[user_id] IN (:users_id) AND [schema.level.level_ix] > :level_ix';
		$expected = '`q_posts`.`user_id` IN (1,2,3) AND `schema`.`level`.`level_ix` > 55';
		$actual = $this->dbo->parseSQL('q_posts', $where, array('users_id' => array(1, 2, 3), 'level_ix' => 55));
		$this->assertEquals($expected, $actual);
	}

	function testParseSQLArray1()
	{
		$where = array('user_id' => 1, 'level_ix' => 3);
		$expected = '`user_id` = 1 AND `level_ix` = 3';
		$actual = $this->dbo->parseSQL(null, $where);
		$this->assertEquals($expected, $actual);
	}

	function testParseSQLArray2()
	{
		$where = array('(', 'user_id' => 1, 'OR', 'level_ix' => 3, ')', 'credits' => 5, 'test' => 6);
		$expected = '( `user_id` = 1 OR `level_ix` = 3 ) AND `credits` = 5 AND `test` = 6';
		$actual = $this->dbo->parseSQL(null, $where);
		$this->assertEquals($expected, $actual);
	}

	function testParseSQLArray3()
	{
		$where = array('(', 'user_id' => array(1,2,3), 'OR', 'level_ix' => 3, ')', 'credits' => 5, 'test' => 6);
		$expected = '( `user_id` IN (1,2,3) OR `level_ix` = 3 ) AND `credits` = 5 AND `test` = 6';
		$actual = $this->dbo->parseSQL(null, $where);
		$this->assertEquals($expected, $actual);
	}

	function testParseSQLArray4()
	{
		$where = array('posts.user_id' => 1, 'OR', '(' , 'level.level_ix' => 3, 'schema.mytable.credits' => 5, ')');
		$expected = '`posts`.`user_id` = 1 OR ( `level`.`level_ix` = 3 AND `schema`.`mytable`.`credits` = 5 )';
		$actual = $this->dbo->parseSQL(null, $where);
		$this->assertEquals($expected, $actual);
	}

	function testParseSQLArray5()
	{
		$where = array('posts.user_id' => 1, 'OR', '[title] LIKE ?');
		$expected = '`posts`.`user_id` = 1 OR `q_posts`.`title` LIKE \'%ABC%\'';
		$actual = $this->dbo->parseSQL('q_posts', $where, '%ABC%');
		$this->assertEquals($expected, $actual);
	}

	function testParseSQLArray6()
	{
		$where = array('posts.user_id' => 1, 'OR', '[title] LIKE :title');
		$expected = '`posts`.`user_id` = 1 OR `q_posts`.`title` LIKE \'%ABC%\'';
		$actual = $this->dbo->parseSQL('q_posts', $where, array('title' => '%ABC%'));
		$this->assertEquals($expected, $actual);
	}

	function testParseSQLArray7()
	{
		$where = array('[user_id] = ?', 'OR', '[title] LIKE ?');
		$expected = '`q_posts`.`user_id` = 1 OR `q_posts`.`title` LIKE \'%ABC%\'';
		$actual = $this->dbo->parseSQL('q_posts', $where, 1, '%ABC%');
		$this->assertEquals($expected, $actual);
	}

	function testParseSQLArray8()
	{
		$where = array('[user_id] = :user_id', 'OR', '[title] LIKE :title');
		$expected = '`q_posts`.`user_id` = 1 OR `q_posts`.`title` LIKE \'%ABC%\'';
		$actual = $this->dbo->parseSQL('q_posts', $where, array('user_id' => 1, 'title' => '%ABC%'));
		$this->assertEquals($expected, $actual);
	}
}

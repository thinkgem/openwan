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
 * 针对表数据入口的单元测试（多表关联的 CRUD 操作）
 *
 * @package test
 * @version $Id: links.php 2144 2009-01-23 19:35:56Z dualface $
 */

require_once dirname(__FILE__) . '/../../_include.php';

class Test_QDB_Table_Links extends PHPUnit_Framework_TestCase
{
    function __construct()
    {
        $dsn = Q::ini('db_dsn_pool/default');
        if (empty($dsn)) {
            Q::changeIni('db_dsn_pool/default', Q::ini('db_dsn_mysql'));
        }
        parent::__construct();
    }

    function testCreateHasMany()
    {
        $tableAuthors = Q::singleton('Table_Authors');
        /* @var $tableAuthors Table_Authors */
        $tableContents = Q::singleton('Table_Contents');
        /* @var $tableContents Table_Contents */

        $conn = $tableAuthors->getConn();
        $conn->startTrans();
        $tableAuthors->getLink('contents')->init()->on_save = 'save';

        $authors = $this->getAuthors();
        $map = array();
        foreach (array_keys($authors) as $offset) {
            $authors[$offset]['contents'] = $this->getContents();
            $id = $tableAuthors->create($authors[$offset]);
            $map[$id] =& $authors[$offset];
        }

        foreach ($map as $id => $author) {
            $row = $conn->getRow("SELECT * FROM {$tableAuthors->qtable_name} WHERE author_id = {$id}");
            $this->assertType('array', $row);
            $this->assertArrayHasKey('name', $row);
            $this->assertEquals($author['name'], $row['name']);

            $sql = "SELECT * FROM {$tableContents->qtable_name} WHERE author_id = {$id} ORDER BY content_id";
            $contents = $conn->getAll($sql);
            $this->assertEquals(count($author['contents']), count($contents));
            foreach ($contents as $offset => $content) {
                $this->assertEquals($author['contents'][$offset]['title'], $content['title']);
            }
        }

        $conn->completeTrans(false);
    }

    function testFindBelongsTo()
    {
        $tableAuthors = Q::singleton('Table_Authors');
        /* @var $tableAuthors Table_Authors */
        $tableAuthors->getConn()->startTrans();

        $authors = $this->insertAuthors();

        $tableContents = Q::singleton('Table_Contents');
        /* @var $tableContents Table_Contents */

        $tableContents->disableLinks('comments, marks, tags');
        $content = array(
            'title' => 'Test Title',
            'author_id' => $authors['liaoyulei'],
        );
        $id = $tableContents->create($content);

        $find = $tableContents->find(array($tableContents->pk => $id))->query();
        $tableContents->enableAllLinks();
        $tableContents->getConn()->completeTrans(false);

        $this->assertEquals($content['title'], $find['title'], "\$find['title'] == \$content['title']");
        $this->assertTrue(!empty($find['author']), "!empty(\$find['author'])");
        $this->assertTrue(!empty($find['author']), "!empty(\$find['author'])");
        $this->assertType('array', $find['author'], "type of \$find['author'] == array");
        $this->assertTrue(!empty($find['author']['author_id']), "!empty(\$find['author']['author_id'])");
        $this->assertEquals($authors['liaoyulei'], $find['author']['author_id'], "\$find['author']['author_id'] == \$authors['liaoyulei']");
        $this->assertEquals('liaoyulei', $find['author']['name_alias'], "\$find['author']['name_alias'] == 'liaoyulei'");
    }

    function testFindHasMany()
    {
        $tableAuthors = Q::singleton('Table_Authors');
        /* @var $tableAuthors Table_Authors */
        $tableAuthors->getConn()->startTrans();
        // $tableAuthors->disableLinks('books');

        $authors = $this->insertAuthors();
        $contents = $this->insertContents($authors);
        $this->insertComments($authors, $contents);

        $author = $tableAuthors->find($authors['liaoyulei'])->query();
        $tableAuthors->getConn()->completeTrans(false);

        $this->assertTrue(!empty($author['contents']), "!empty(\$author['contents'])");
        $this->assertType('array', $author['contents'], "type of \$author['contents'] == array");
        $first = reset($author['contents']);
        $this->assertType('array', $first, "reset(\$author['contents']) == array");
        $this->assertTrue(!empty($first['title']), "!empty(reset(\$author['contents']['title']))");

        $link_contents = $tableAuthors->getLink('contents');
        $on_find_keys = Q::normalize($link_contents->on_find_keys);

        $this->assertEquals(count($on_find_keys), count($first), "count(\$first) == 1");

        if (is_int($link_contents->on_find)) {
            $this->assertEquals($link_contents->on_find, count($author['contents']), "count(\$author['contents']) == " . $link_contents->on_find);
        }

        $first = reset($author['contents']);
        $next = next($author['contents']);
        $this->assertTrue($first['content_id'] < $next['content_id'], "\$first['content_id'] < \$next['content_id']");
    }

    function testManyToMany()
    {

    }

    /**
     * 创建作者记录
     *
     * @return array
     */
    protected function insertAuthors()
    {
        $tableAuthors = Q::singleton('Table_Authors');
        /* @var $tableAuthors Table_Authors */

        $authors = array(
            'liaoyulei' => $tableAuthors->create(array('name' => 'liaoyulei')),
            'dali'      => $tableAuthors->create(array('name' => 'dali')),
            'xiecong'   => $tableAuthors->create(array('name' => 'xiecong')),
        );

        return $authors;
    }

    /**
     * 创建作者记录的同时创建内容记录
     *
     * @param int $contents_nums
     *
     * @return array
     */
    protected function insertAuthorsWithContents($contents_nums = 5)
    {
        $tableAuthors = Q::singleton('Table_Authors');
        /* @var $tableAuthors Table_Authors */

        $authors = array(
            array(
                'name' => 'liaoyulei',
                'contents' => $this->getContents(mt_rand(1, $contents_nums)),
            ),
            array(
                'name' => 'dali',
                'contents' => $this->getContents(mt_rand(1, $contents_nums)),
            ),
            array(
                'name' => 'xiecong',
                'contents' => $this->getContents(mt_rand(1, $contents_nums)),
            ),
        );

        return $tableAuthors->createRowset($authors);
    }

    protected function getAuthors()
    {
        $authors = array(
            array('name' => 'liaoyulei'),
            array('name' => 'dali'),
            array('name' => 'xiecong'),
        );
        return $authors;
    }

    protected function getContents($contents_nums = 10)
    {
        $contents = array();
        for ($i = 0; $i < $contents_nums; $i++) {
            $contents[] = array(
                'title' => 'TITLE ' . mt_rand(),
            );
        }
        return $contents;
    }

    protected function insertContents(array $authors, $nums = 10)
    {
        $tableContents = Q::singleton('Table_Contents');
        /* @var $tableContents Table_Contents */
        $authors = array_values($authors);
        $authors_count = count($authors);

        $contents = array();
        for ($i = 0; $i < $nums; $i++) {
            $content = array(
                'author_id' => $authors[$i % $authors_count],
                'title' => 'TITLE ' . mt_rand(),
            );
            $contents[] = $tableContents->create($content);
        }

        return $contents;
    }

    /**
     * 创建评论记录
     *
     * @param array $authors
     * @param array $contents
     * @param int $nums
     *
     * @return array
     */
    protected function insertComments(array $authors, array $contents, $nums = 20)
    {
        $tableComments = Q::singleton('Table_Comments');
        /* @var $tableComments Table_Comments */
        $authors = array_values($authors);
        $authors_count = count($authors);
        $contents = array_values($contents);
        $contents_count = count($contents);

        $comments = array();
        for ($i = 0; $i < $nums; $i++) {
            $comment = array(
                'author_id' => $authors[$i % $authors_count],
                'content_id' => $contents[$i % $contents_count],
                'body' => 'BODY ' . mt_rand(),
            );
            $comments[] = $tableComments->create($comment);
        }

        return $comments;
    }

    /**
     * 创建书籍记录
     *
     * @param array $authors
     * @param int $nums
     *
     * @return array
     */
    protected function insertBooks(array $authors, $nums = 10)
    {
        $tableBooks = Q::singleton('Table_Books');
        /* @var $tableBooks Table_Books */
        $authors = array_values($authors);
        $authors_count = count($authors);

        $books = array();
        for ($i = 0; $i < $nums; $i++) {
            $c = mt_rand(1, $authors_count);
            $rand_authors = array();
            for ($j = 0; $j < $c; $j++) {
                $rand_authors[] = $authors[mt_rand(0, $j * $j) % $authors_count];
            }
            $book = array(
                'title' => 'BOOK ' . mt_rand(),
                'intro' => 'INTRO ' . mt_rand(),
                'authors' => $rand_authors,
            );
            $books[] = $tableBooks->create($book);
        }

        return $books;
    }
}

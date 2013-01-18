<?php
// $Id: coll.php 2190 2009-02-04 09:35:36Z dualface $

/**
 * 测试 QColl 类
 */
class UT_Core_Coll extends QTest_UnitTest_Abstract
{
    protected $_coll;
    protected $_max = 6;

    function setUp()
    {
        Q::import(FIXTURE_DIR . '/core/coll');
        $this->_coll = new QColl('MyItem');
        for ($i = 0; $i < $this->_max; $i++)
        {
            $this->_coll[] = new MyItem($i);
        }
    }

    function testArrayAccess()
    {
        for ($i = 0; $i < $this->_max; $i++)
        {
            $this->assertTrue(isset($this->_coll));
            $obj = $this->_coll[$i];
            $this->assertType('MyItem', $obj);
            $this->assertEquals($i, $obj->index);
        }

        for ($i = 0; $i < $this->_max; $i++)
        {
            $this->assertTrue(isset($this->_coll[$i]));
            unset($this->_coll[$i]);
            $this->assertFalse(isset($this->_coll[$i]));
        }
    }

    function testIrerator()
    {
        $count = 0;
        foreach ($this->_coll as $key => $obj)
        {
            $this->assertType('MyItem', $obj);
            $this->assertEquals((string)$obj->index, (string)$key);
            $count++;
        }
        $this->assertEquals($this->_max, $count);
    }

    function testNestedItems()
    {
        $i = count($this->_coll);
        $this->_coll[$i] = array(
            new MyItem($i),
            new MyItem($i + 1),
        );

        $arr = $this->_coll[$i];
        $this->assertType('array', $arr);

        foreach ($arr as $item)
        {
            $this->assertType('MyItem', $item);
        }
    }

    function testNonObjectColl()
    {
        $coll = new QColl('integer');
        $coll[] = 5;
        $coll[] = 3;

        $this->assertEquals(2, count($coll));
    }

    function testFirst()
    {
        $first = $this->_coll->first();
        $this->assertType('MyItem', $first);
        $this->assertEquals(0, $first->index);

        unset($this->_coll[0]);
        $first = $this->_coll->first();
        $this->assertType('MyItem', $first);
        $this->assertEquals(1, $first->index);
    }

    function testLast()
    {
        $last = $this->_coll->last();
        $this->assertType('MyItem', $last);
        $this->assertEquals($this->_max - 1, $last->index);

        unset($this->_coll[$this->_max - 1]);
        $last = $this->_coll->last();
        $this->assertType('MyItem', $last);
        $this->assertEquals($this->_max - 2, $last->index);
    }

    function testCount()
    {
        $this->assertEquals($this->_max, count($this->_coll));
    }

    function testAppend()
    {
        $arr = array();
        for ($i = $this->_max; $i < $this->_max * 2; $i++)
        {
            $arr[] = new MyItem($i);
        }
        $this->_coll->append($arr);
        $this->assertEquals($this->_max * 2, count($this->_coll));

        for ($i = 0; $i < $this->_max * 2; $i++)
        {
            $obj = $this->_coll[$i];
            $this->assertType('MyItem', $obj);
            $this->assertEquals($i, $obj->index);
        }
    }

    function arrProvider()
    {
        static $data = null;

        if (is_null($data))
        {
            $arr = array();
            for ($i = 0; $i < $this->_max; $i++)
            {
                $arr[$i] = array('index' => $i);
            }
            $data = array(array($arr));
        }
        return $data;
    }

    /**
     * @dataProvider arrProvider
     */
    function testToArray($data)
    {
        $arr = $this->_coll->toArray();
        $this->assertEquals($data, $arr);
    }

    /**
     * @dataProvider arrProvider
     */
    function testToJSON($data)
    {
        $json = $this->_coll->toJSON();
        $this->assertEquals(json_encode($data), $json);
    }

    function testToXML()
    {
        $this->markTestIncomplete();
    }

    /**
     * @dataProvider arrProvider
     */
    function testCreateFromArray($data)
    {
        $arr = array();
        foreach ($data as $item)
        {
            $arr[] = new MyItem($item['index']);
        }
        $coll = QColl::createFromArray($arr, 'MyItem');
        $this->assertEquals(count($data), count($coll));
        $this->assertEquals($data, $coll->toArray());
    }

    function testValues()
    {
        $values = $this->_coll->values('index');
        $this->assertEquals($this->_max, count($values));
        for ($i = 0; $i < $this->_max; $i++)
        {
            $this->assertEquals($i, $values[$i]);
        }
    }

    function testSearch()
    {
        $item = $this->_coll->search('index', $this->_max - 1);
        $this->assertType('MyItem', $item);
        $item = $this->_coll->search('index', $this->_max);
        $this->assertNull($item);
    }

}


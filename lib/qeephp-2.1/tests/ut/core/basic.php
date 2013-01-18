<?php
// $Id: basic.php 2188 2009-02-04 06:47:51Z dualface $

/**
 * 测试 QeePHP 核心类的基础服务
 *
 * normalize()
 * control();
 */
class UT_Core_Basic extends QTest_UnitTest_Abstract
{
    function normalizeProvider()
    {
        return array(
            array('key1, key2, key3', null),
            array('key1|key2|key3',   '|'),
            array('key1--key2--key3', '--'),
            array('key1, key2, key3', ','),
        );
    }

    /**
     * @dataProvider normalizeProvider
     */
    function testNormalize($items, $test)
    {
        $data = array('key1', 'key2', 'key3');

        if ($test)
        {
            $this->assertEquals($data, Q::normalize($items, $test));
        }
        else
        {
            $this->assertEquals($data, Q::normalize($items));
        }
    }

    function controlProvider()
    {
        return array(
            array('checkbox'),
            array('textbox'),
            array('checkboxex'),
            array('textboxex'),
        );
    }

    /**
     * @dataProvider controlProvider
     */
    function testControl($type)
    {
        Q::import(FIXTURE_DIR . '/core/control');
        $control = Q::control($type);
        $this->assertType('Control_' . $type, $control);
    }
}


<?php
// $Id: config.php 2186 2009-02-03 17:41:49Z dualface $

/**
 * 测试 QeePHP 核心配置管理功能
 */
class UT_Core_Config extends QTest_UnitTest_Abstract
{
    /**
     * 改变和读取单个值
     */
    function testChangeAndReadSingleValue($path = 'test-single-value')
    {
        $values = array('value', 1234, -333, 3.54, true);
        foreach ($values as $value)
        {
            Q::changeIni($path, $value);
            $this->assertEquals($value, Q::ini($path), "Q::ini('{$path}') == {$value}");
        }
    }

    /**
     * 改变和读取指定路径中的值
     */
    function testChangeAndReadSingleValueWithPath()
    {
        $this->testChangeAndReadSingleValue('node/single-value');
    }

    /**
     * 修改和读取多个值
     */
    function testChangeAndReadMultiValues($path = 'test-multi-values')
    {
        $values = array('value', 1234, -333, 3.54, true, 'key1' => 'value');
        Q::changeIni($path, $values);
        $this->assertEquals($values, Q::ini($path), "Q::ini('{$path}') multi values");
    }

    /**
     * 修改和读取指定路径中的多个值
     */
    function testChangeAndReadMultiValuesWithPath()
    {
        $this->testChangeAndReadMultiValues('node/multi-values');
        $path = 'node/multi-values/key1';
        $value = 'value';
        $this->assertEquals($value, Q::ini($path), "Q::ini('{$path}') == {$value}");
    }

    /**
     * 测试清除设置
     */
    function testCleanIni()
    {
        $path = 'node/single-value';
        Q::cleanIni($path);
        $test = Q::ini($path);
        $this->assertTrue(empty($test), "Q::ini('{$path}') == empty");
        $path = 'node';
        Q::cleanIni($path);
        $test = Q::ini($path);
        $this->assertTrue(empty($test), "Q::ini('{$path}') == empty");
    }

}



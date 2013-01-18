<?php
// $Id: objects.php 2188 2009-02-04 06:47:51Z dualface $

/**
 * 测试 QeePHP 核心类对象管理功能
 *
 * register()
 * registry()
 * isRegister()
 * singleton()
 */
class UT_Core_Objects extends QTest_UnitTest_Abstract
{
    function setUp()
    {
        Q::import(FIXTURE_DIR . '/core', true);
    }

    /**
     * 测试对象注册
     */
    function testRegister()
    {
        $obj1 = new Class2();
        Q::register($obj1);
        Q::register($obj1, 'object-1-1');

        $this->assertTrue(Q::isRegistered('class2'));
        $this->assertTrue(Q::isRegistered('Class2'));

        $this->assertFalse(Q::isRegistered('class22'));

        $obj = Q::registry('class2');
        $this->assertSame($obj1, $obj);

        $obj = Q::registry('object-1-1');
        $this->assertSame($obj1, $obj);
    }

    function testSingleton()
    {
        $obj1 = Q::singleton('Class2');
        $obj2 = Q::singleton('Class2');

        $this->assertSame($obj1, $obj2);
    }

}



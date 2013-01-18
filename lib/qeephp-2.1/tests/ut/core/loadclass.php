<?php
// $Id: loadclass.php 2188 2009-02-04 06:47:51Z dualface $

/**
 * 测试 QeePHP 核心类载入功能
 *
 * loadClass()
 * import()
 * registerAutoLoad()
 */
class UT_Core_LoadClass extends QTest_UnitTest_Abstract
{
    function setUp()
    {
        Q::import(FIXTURE_DIR . '/core', true);
    }

    /**
     * 测试 import
     *
     * @expectedException Q_ClassNotDefinedException
     */
    function testLoadClassWithImport()
    {
        Q::loadClass('Class1');
    }

    /**
     * 测试载入类（大小写敏感）
     */
    function testLoadClassWithCaseSensitive()
    {
        Q::loadClass('Class2');
    }

    /**
     * 测试注册自动载入方法
     */
    function testRegisterAutoLoad()
    {
        Q::registerAutoload(__CLASS__);
        $obj = new Class3();
        Q::registerAutoLoad(__CLASS__, false);
        $this->assertFalse(class_exists('Class4'));
    }

    static function autoload($class)
    {
        $filename = FIXTURE_DIR . '/core/loadclass/' . strtolower($class) . '_class.php';
        require $filename;
    }
}


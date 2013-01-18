<?php

abstract class QTest_Case_Abstract extends PHPUnit_Framework_TestCase
{
    protected function assertEmpty($var, $msg = '')
    {
        $this->assertTrue(empty($var), $msg);
    }

    protected function assertNotEmpty($var, $msg = '')
    {
        $this->assertTrue(!empty($var), $msg);
    }
}


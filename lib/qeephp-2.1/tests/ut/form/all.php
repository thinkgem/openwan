<?php
// $Id: all.php 2242 2009-02-16 21:26:30Z dualface $

class UT_Form_All extends QTest_UnitTest_TestSuite_Abstract
{
    static function suite()
    {
        $dir = dirname(__FILE__);
        $suite = new UT_Form_All('UT_Form_Suite');
        $suite->addTestFiles(array(
            "{$dir}/basic.php",
            "{$dir}/group.php",
        ));

        return $suite;
    }
}


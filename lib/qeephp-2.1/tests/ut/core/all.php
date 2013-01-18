<?php
// $Id: all.php 2224 2009-02-08 15:30:33Z dualface $

class UT_Core_All extends QTest_UnitTest_TestSuite_Abstract
{
    static function suite()
    {
        $dir = dirname(__FILE__);
        $suite = new UT_Core_All('UT_Core_Suite');
        $suite->addTestFiles(array(
            "{$dir}/basic.php",
            "{$dir}/cache.php",
            "{$dir}/coll.php",
            "{$dir}/context.php",
            "{$dir}/config.php",
            "{$dir}/loadclass.php",
            "{$dir}/objects.php",
        ));

        return $suite;
    }
}


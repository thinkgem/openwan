<?php

date_default_timezone_set(date_default_timezone_get());
error_reporting(E_ALL | E_STRICT);

require_once dirname(dirname(__FILE__)) . '/library/q.php';
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/TestSuite.php';

require_once dirname(__FILE__) . '/qtest_case_abstract.php';
require_once dirname(__FILE__) . '/qtest_suite_abstract.php';
require_once dirname(__FILE__) . '/qtest_helper.php';

// PHPUnit_Util_Filter::addDirectoryToFilter(dirname(__FILE__));

Q::registerAutoload('QTest_Helper');


<?php
// $Id: _setup.php 2360 2009-04-01 15:35:38Z dualface $

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';
QTest_Helper::import(dirname(__FILE__) . '/fixtures');

QTest_Helper::loadConfig('database.yaml', 'db_dsn_pool');


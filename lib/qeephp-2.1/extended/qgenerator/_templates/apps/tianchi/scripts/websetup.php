<?php
// $Id$

/**
 * @file
 * Web 界面代码生成器的入口文件
 *
 * @ingroup script
 *
 * @{
 */

$app_config = require(dirname(dirname(__FILE__)) . '/config/boot.php');
require $app_config['QEEPHP_DIR'] . '/library/q.php';
require $app_config['APP_DIR'] . '/myapp.php';
require $app_config['QEEPHP_DIR'] . '/commands/websetup/run.php';

$websetup = Websetup::instance($app_config, MyApp::loadConfigFiles($app_config));
$websetup->run();


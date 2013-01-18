<?php
// $Id$


/**
 * 命令行代码生成器的入口文件
 */

if (!isset($argv))
{
    echo <<<EOT
ERR: PHP running command line without \$argv.

EOT;

    exit;
}

$app_config = require(dirname(dirname(__FILE__)) . '/config/boot.php');
require $app_config['QEEPHP_DIR'] . '/library/q.php';
require $app_config['APP_DIR'] . '/myapp.php';
MyApp::instance($app_config);

require $app_config['QEEPHP_DIR'] . '/commands/cli/generator.php';

array_shift($argv);
$generator = new CliGenerator($app_config, $argv);
$generator->generating();


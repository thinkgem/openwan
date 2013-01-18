<?php
/**
 * 定义 CliChili 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: chili.php 1937 2009-01-05 19:09:40Z dualface $
 * @package core
 */

/**
 * 类  CliChili 实现基于命令行的应用程序生成器
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: chili.php 1937 2009-01-05 19:09:40Z dualface $
 * @package core
 */
abstract class CliChili
{
    static function generate($argv)
    {
        $dir = array_shift($argv);
        $appid = array_shift($argv);

        dump($appid, '$appid');
        if (empty($dir) || empty($appid)) return self::help();
        $tpl = array_shift($argv);

        $generator = new QGenerator_Application();
        return $generator->generate($appid, $dir, $tpl);
    }

    /**
     * 显示帮助信息
     */
    static function help()
    {
        echo <<<EOT

php chili.php <appid> [...]

syntax:
    php scripts/chili.php <appid> [dest_dir] [tpl_name]

examples:
    php scripts/chili.php myapp
    php scripts/chili.php myapp d:\\www
    php scripts/chili.php myapp d:\\www tianchi_v



EOT;

        return 0;
    }

}


/**
 * 启动脚本
 */
if (!isset($argv))
{
    echo <<<EOT
ERR: PHP running command line without \$argv.

EOT;

    exit;
}

$root_dir = dirname(dirname(dirname(__FILE__)));
require $root_dir . '/library/q.php';
Q::import($root_dir . '/extended');

array_shift($argv);
CliChili::generate($argv);


<?php

/**
 * 创建开发者手册
 */

require dirname(__FILE__) . '/../library/q.php';
$dir = dirname(__FILE__);
Q::import($dir);
Q::import($dir . '/command');
Q::import($dir . '/command/book');
Q::import($dir . '/_vendor/zf');
Q::changeIni('vendor_dir', dirname(__FILE__) . DS . '_vendor');
if (!isset($argv[2]))
{
    echo <<<EOT

php gen_book.php <source_dir> <output_dir> [mode]

syntax:
    mode: "online", "offline" or "chm", online is default



EOT;

    exit(-1);
}

$source_dir = $argv[1];
$output_dir = $argv[2];

if (isset($argv[3]))
{
    $mode = strtolower(trim($argv[3]));
}
else
{
    $mode = 'online';
}

Command_Book::create()->sourceDir($source_dir)
                      ->outputDir($output_dir)
                      ->docmode($mode)
                      ->execute();


<?php

/**
 * 创建 API 文档
 */

require dirname(__FILE__) . '/../library/q.php';
Q::import(dirname(__FILE__));
Q::import(dirname(__FILE__) . '/command');
Q::import(dirname(__FILE__) . '/command/api');
Q::changeIni('vendor_dir', dirname(__FILE__) . DS . '_vendor');

$source_dir = Q_DIR;
$docs_dir   = dirname(__FILE__) . '/source/api';
$output_dir = dirname(__FILE__) . '/output/api';
$excludes = array(
    '_config',
    '_resources',
    '_vendor',
);

if (isset($argv[1]))
{
    $output_dir = $argv[1];
}

if (isset($argv[2]))
{
    $mode = strtolower(trim($argv[2]));
}
else
{
    $mode = 'online';
}

Command_API::create()->sourceDir($source_dir)
                     ->docsDir($docs_dir)
                     ->outputDir($output_dir)
                     ->excludes($excludes)
                     ->docmode($mode)
                     ->execute();



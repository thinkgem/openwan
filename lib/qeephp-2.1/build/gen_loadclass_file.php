<?php

/**
 * 创建 QeePHP 框架类载入文件
 */

$source_dir = dirname(__FILE__) . '/../library';
$output_file = $source_dir . '/_config/qeephp_class_files.php';

require_once dirname(__FILE__) . '/command/loadclass.php';

Command_LoadClass::create()->sourceDir($source_dir)
                           ->outputFile($output_file)
                           ->execute();




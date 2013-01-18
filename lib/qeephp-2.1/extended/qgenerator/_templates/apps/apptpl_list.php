<?php

/**
 * 返回可用的应用程序模板列表及说明
 */

return array(
    'tianchi' => array(
        'name' => 'tianchi',
        'title' => '默认应用程序模板',
        'description' => '该模板要求设置网站根目录为 public 目录。',
    ),
    'tianchi_v' => array(
        'name' => 'tianchi_v',
        'title' => '虚拟主机应用程序模板',
        'description' => '该模板适合无法修改服务器设置的虚拟主机用户。index.php 放置在应用程序根目录中，并且针对虚拟主机的安全性问题做了相应的修改。',
    ),
);

<?php
// $Id: boot.php 901 2010-07-22 05:49:33Z thinkgem $

/**
 * 应用程序基本启动文件，提供应用程序运行的关键设置信息
 */

$root_dir = dirname(dirname(__FILE__));

// 运行模式有 devel、deploy 和 test 三种
$run_mode = 'devel';

/**
 * 如果要集成第三方的 PHP 库，错误报告也许要修改为：
 *
 * error_reporting(E_ALL & ~(E_STRICT | E_NOTICE));
 */
error_reporting(E_ALL | E_STRICT);

/**
 * 应用程序配置信息
 */
return array(

    /**
     * QeePHP 框架所在目录
     */
    'QEEPHP_DIR'            => "{$root_dir}/lib/qeephp-2.1",

    /**
     * 应用程序的 ID，用于唯一标识一个应用程序
     */
    'APPID'                 => 'openwan',

    /**
     * 运行模式
     */
    'RUN_MODE'              => $run_mode,

    /**
     * 应用程序根目录
     */
    'ROOT_DIR'              => $root_dir,

    /**
     * 主程序所在目录
     */
    'APP_DIR'               => "{$root_dir}/app",

    /**
     * 配置文件所在目录
     */
    'CONFIG_DIR'            => "{$root_dir}/config",

    /**
     * 所有扩展模块所在的目录
     */
    'MODULE_DIR'            => "{$root_dir}/modules",

    /**
     * 定义缓存配置文件要使用的缓存服务
     *
     * 指定使用哪项服务，就需要在后面的 CONFIG_CACHE_SETTINGS 中进行相应的设置。
     */
    'CONFIG_CACHE_BACKEND'  => 'QCache_PHPDataFile',

    /**
     * 指示是否缓存配置文件的内容
     */
    'CONFIG_CACHED'         => true,

    /**
     * 配置文件的扩展名
     */
    'CONFIG_FILE_EXTNAME'   => 'yaml',

    /**
     * 缓存设置
     */
    'CONFIG_CACHE_SETTINGS' => array(
        /**
         * 由于 CONFIG_CACHE_BACKEND 指定为 QCache_File。
         * 所以这里需要为 QCache_File 指定缓存参数
         */
        'QCache_PHPDataFile' => array(
            // deploy 模式缓存 86400 秒
            // devel 模式缓存 10 秒
            // 其他模式缓存 120 秒
            'life_time' => $run_mode == 'deploy' ?
                                86400 :
                                ($run_mode == 'devel' ? 10 : 120),
            'cache_dir' => "{$root_dir}/tmp/runtime_cache",
        ),
    ),
);


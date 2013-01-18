<?php
// $Id: view_redirect.php 2010 2009-01-08 18:56:36Z dualface $

/**
 * 定义 QView_Redirect 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: view_redirect.php 2010 2009-01-08 18:56:36Z dualface $
 * @package mvc
 */

/**
 * QView_Redirect 类封装了一个浏览器重定向操作
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: view_redirect.php 2010 2009-01-08 18:56:36Z dualface $
 * @package mvc
 */
class QView_Redirect
{
    /**
     * 重定向 URL
     *
     * @var string
     */
    public $url;

    /**
     * 重定向延时（秒）
     *
     * @var int
     */
    public $delay;

    /**
     * 构造函数
     *
     * @param string $url
     * @param int $delay
     */
    function __construct($url, $delay = 0)
    {
        $this->url = $url;
        $this->delay = $delay;
    }

    /**
     * 执行
     */
    function execute()
    {
        $delay = (int)$this->delay;
        $url = $this->url;
        if ($delay > 0) {
            echo <<<EOT
<html>
<head>
<meta http-equiv="refresh" content="{$delay};URL={$url}" />
</head>
</html>
EOT;
        } else {
            header("Location: {$url}");
        }
        exit;
    }
}


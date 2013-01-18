<?php
// $Id: router_routenotfound_exception.php 2010 2009-01-08 18:56:36Z dualface $

/**
 * 定义 QRouter_RouteNotFoundException 异常
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: router_routenotfound_exception.php 2010 2009-01-08 18:56:36Z dualface $
 * @package exception
 */

/**
 * QRouter_RouteNotFoundException 异常指示指定的路由规则不存在
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: router_routenotfound_exception.php 2010 2009-01-08 18:56:36Z dualface $
 * @package exception
 */
class QRouter_RouteNotFoundException extends QException
{
    public $route_name;

    function __construct($route_name)
    {
        $this->route_name = $route_name;
        parent::__construct(__('Route "%s" not found.', $route_name));
    }
}


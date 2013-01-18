<?php
// $Id: controller_abstract.php 2010 2009-01-08 18:56:36Z dualface $

/**
 * 定义 QController_Abstract 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: controller_abstract.php 2010 2009-01-08 18:56:36Z dualface $
 * @package mvc
 */

/**
 * QController_Abstract 实现了一个其它控制器的基础类
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: controller_abstract.php 2010 2009-01-08 18:56:36Z dualface $
 * @package mvc
 */
abstract class QController_Abstract
{
    /**
     * 封装请求的对象
     *
     * @var QContext
     */
    protected $_context;

    /**
     * 构造函数
     */
    function __construct()
    {
        $this->_context = QContext::instance();
    }

    /**
     * 检查指定的动作方法是否存在
     *
     * @param string $action_name
     *
     * @return boolean
     */
    function existsAction($action_name)
    {
        $action_method = "action{$action_name}";
        return method_exists($this, $action_method);
    }

    /**
     * 转发请求到控制器的指定动作
     *
     * @param string $udi
     *
     * @return mixed
     */
    protected function _forward($udi)
    {
        $args = func_get_args();
        array_shift($args);
        return new QController_Forward($udi, $args);
    }

    /**
     * 返回一个 QView_Redirect 对象
     *
     * @param string $url
     * @param int $delay
     *
     * @return QView_Redirect
     */
    protected function _redirect($url, $delay = 0)
    {
        return new QView_Redirect($url, $delay);
    }
}


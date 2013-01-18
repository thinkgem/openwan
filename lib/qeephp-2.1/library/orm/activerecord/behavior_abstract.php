<?php
// $Id: behavior_abstract.php 2409 2009-04-10 10:02:31Z jerry $

/**
 * 定义 QDB_ActiveRecord_Behavior_Abstract 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: behavior_abstract.php 2409 2009-04-10 10:02:31Z jerry $
 * @package orm
 */

/**
 * QDB_ActiveRecord_Behavior_Abstract 抽象类是所有行为插件的基础类
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: behavior_abstract.php 2409 2009-04-10 10:02:31Z jerry $
 * @package orm
 */
abstract class QDB_ActiveRecord_Behavior_Abstract implements QDB_ActiveRecord_Callbacks
{

    /**
     * ActiveRecord 继承类的元信息对象
     *
     * @var QDB_ActiveRecord_Meta
     */
    protected $_meta;

    /**
     * 插件的设置信息
     *
     * @var array
     */
    protected $_settings = array();

    /**
     * 插件添加的动态方法
     *
     * @var array
     */
    private $_dynamic_methods = array();

    /**
     * 插件添加的静态方法
     *
     * @var array
     */
    private $_static_methods = array();

    /**
     * 插件添加的事件处理函数
     *
     * @var array
     */
    private $_event_handlers = array();

    /**
     * 插件添加的 getter 方法
     *
     * @var array
     */
    private $_getters = array();

    /**
     * 插件添加的 setter 方法
     *
     * @var array
     */
    private $_setters = array();

    /**
     * 构造函数
     *
     * @param QDB_ActiveRecord_Meta $meta
     * @param array $settings
     */
    function __construct(QDB_ActiveRecord_Meta $meta, array $settings)
    {
        $this->_meta = $meta;
        foreach ($settings as $key => $value)
        {
            if (array_key_exists($key, $this->_settings))
            {
                $this->_settings[$key] = $value;
            }
        }
        $this->bind();
    }

    /**
     * 格式化配置
     * FIXED!
     */
    static function normalizeConfig($config)
    {
        $ret = array();
        foreach ($config as $key => $value)
        {
            if (is_int($key) && !is_array($value))
            {
                $ret[$value] = array();
            }
            else
            {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    /**
     * 绑定行为插件
     */
    abstract function bind();

    /**
     * 撤销行为插件绑定
     */
    function unbind()
    {
    	foreach ($this->_dynamic_methods as $method_name)
    	{
            $this->_meta->removeDynamicMethod($method_name);
    	}
    	foreach ($this->_static_methods as $method_name)
    	{
    		$this->_meta->removeStaticMethod($method_name);
    	}
    	foreach ($this->_event_handlers as $arr)
    	{
    		list($event_type, $callback) = $arr;
    		$this->_meta->removeEventHandler($event_type, $callback);
    	}
    	foreach ($this->_getters as $prop_name)
    	{
    	    $this->_meta->unsetPropGetter($prop_name);
    	}
        foreach ($this->_setters as $prop_name)
        {
            $this->_meta->unsetPropSetter($prop_name);
        }
    }

    /**
     * 为 ActiveRecord 对象添加一个动态方法
     *
     * @param string $method_name
     * @param callback $callback
     * @param array $custom_parameters
     */
    protected function _addDynamicMethod($method_name, $callback, $custom_parameters = array())
    {
    	$this->_meta->addDynamicMethod($method_name, $callback, $custom_parameters);
    	$this->_dynamic_methods[] = $method_name;
    }

    /**
     * 为 ActiveRecord 类添加一个静态方法
     *
     * @param string $method_name
     * @param callback $callback
     * @param array $custom_parameters
     */
    protected function _addStaticMethod($method_name, $callback, $custom_parameters = array())
    {
        $this->_meta->addStaticMethod($method_name, $callback, $custom_parameters);
        $this->_static_methods[] = $method_name;
    }

    /**
     * 为 ActiveRecord 对象添加一个事件处理函数
     *
     * @param int $event_type
     * @param callback $callback
     * @param array $custom_parameters
     */
    protected function _addEventHandler($event_type, $callback, $custom_parameters = array())
    {
    	$this->_meta->addEventHandler($event_type, $callback, $custom_parameters);
        $this->_event_handlers[] = array($event_type, $callback);
    }

    /**
     * 设置一个属性的 getter 方法
     *
     * @param string $prop_name
     * @param callback $callback
     * @param array $custom_parameters
     */
    protected function _setPropGetter($prop_name, $callback, $custom_parameters = array())
    {
        $this->_meta->setPropGetter($prop_name, $callback, $custom_parameters);
        $this->_getters[] = $prop_name;
    }

    /**
     * 设置一个属性的 setter 方法
     *
     * @param string $prop_name
     * @param callback $callback
     * @param array $custom_parameters
     */
    protected function _setPropSetter($prop_name, $callback, $custom_parameters = array())
    {
        $this->_meta->setPropSetter($prop_name, $callback, $custom_parameters);
        $this->_setters[] = $prop_name;
    }
}


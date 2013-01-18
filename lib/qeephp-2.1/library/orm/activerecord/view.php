<?php

/**
 * QDB_ActiveRecord_View 类模拟数据库视图的形式实现 Active Record 模式
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id$
 * @package orm
 */
abstract class QDB_ActiveRecord_View extends QDB_ActiveRecord_Abstract
{
    /**
     * 构造函数
     *
     * @param array|object $data 包含数据的名值对
     * @param int $names_style 名值对的键名风格
     * @param boolean $from_storage 是否从存储器载入数据
     */
    function __construct($data = null, $names_style = QDB::PROP, $from_storage = false)
    {
        $this->_class_name = get_class($this);
        if (! isset(self::$_meta[$this->_class_name]))
        {
            self::$_meta[$this->_class_name] = QDB_ActiveRecord_ViewMeta::instance($this->_class_name);
        }
        $meta = self::$_meta[$this->_class_name];
        /* @var $meta QDB_ActiveRecord_ViewMeta */

        // 设置对象属性默认值
        $this->_props = $meta->default_props;

        if (is_array($data) || $data instanceof Iterator)
        {
            foreach ($data as $key => $value)
            {
                $this->_props[$key] = $value;
            }
        }

        // 触发 after_initialize 事件
        $this->_after_initialize();
        $this->_event(self::AFTER_INITIALIZE);
        $this->_after_initialize_post();
    }

    function isNewRecord()
    {
        return false;
    }

    function save($recursion = 99, $save_method = 'save')
    {
        return false;
    }

    function validate($mode = 'general')
    {
        return false;
    }

    function destroy()
    {
        return false;
    }

    function changeProps($arr, $names_style = QDB::PROP, $attr_accessible = null, $_from_storage = false, $_ignore_readonly = false)
    {
        return $this;
    }

    function changePropForce($prop_name, $prop_value)
    {
        return $this;
    }

    function changed($props_name = null)
    {
        return false;
    }

    function willChanged($props_name)
    {
        return $this;
    }

    function changes()
    {
        return array();
    }

    function cleanChanges($props = null)
    {
        return $this;
    }

    /**
     * 魔法方法，实现对象属性值的读取
     *
     * @param string $prop_name
     *
     * @return mixed
     */
    function __get($prop_name)
    {
        if (!isset(self::$_meta[$this->_class_name]->props[$prop_name])
            && !array_key_exists($prop_name, $this->_props))
        {
            throw new QDB_ActiveRecord_UndefinedPropException($this->_class_name, $prop_name);
        }

        if (isset(self::$_meta[$this->_class_name]->props[$prop_name]))
        {
            $config = self::$_meta[$this->_class_name]->props[$prop_name];
            if (!empty($config['getter']))
            {
                // 如果指定了属性的 getter，则通过 getter 方法来获得属性值
                list($callback, $custom_parameters) = $config['getter'];
                if (!is_array($callback))
                {
                    $callback = array($this, $callback);
                    $args = array($prop_name, $custom_parameters, & $this->_props);
                }else {
                    $args = array($this,$prop_name, $custom_parameters, & $this->_props);
                }
                return call_user_func_array($callback, $args);
            }

            if (!isset($this->_props[$prop_name]) && $config['assoc'])
            {
                // 如果属性是一个关联，并且没有值，则通过 QDB_ActiveRecord_Meta::relatedObjects() 获得关联对象
                $this->_props[$prop_name] = self::$_meta[$this->_class_name]->relatedObjects($this, $prop_name);
            }
        }

        return $this->_props[$prop_name];
    }

    function __set($prop_name, $value)
    {
    }

    function offsetSet($prop_name, $value)
    {
    }

    function offsetUnset($prop_name)
    {
    }

    protected function _create($recursion = 99)
    {
    }

    protected function _update($recursion = 99)
    {
    }

    protected function _replace($recursion = 99)
    {
    }

    protected function _autofill($mode)
    {
    }

}


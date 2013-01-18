<?php
// $Id: activerecord.php 2670 2009-11-18 07:16:09Z firzen $

/**
 * 定义 QDB_ActiveRecord_Abstract 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: activerecord.php 2670 2009-11-18 07:16:09Z firzen $
 * @package orm
 */

/**
 * QDB_ActiveRecord_Abstract 类实现了 Active Record 模式
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: activerecord.php 2670 2009-11-18 07:16:09Z firzen $
 * @package orm
 */
abstract class QDB_ActiveRecord_Abstract implements QDB_ActiveRecord_Callbacks,
                                                    QDB_ActiveRecord_Interface,
                                                    ArrayAccess
{
    /**
     * 对象所有属性的值
     *
     * @var array
     */
    protected $_props;

    /**
     * 当前 ActiveRecord 对象的类名称
     *
     * @var string
     */
    protected $_class_name;

    /**
     * 对象的 ID
     *
     * 如果对象的 ID 是由多个属性组成，则 $_id 是一个由多个属性值组成的名值对。
     *
     * @var mixed
     */
    protected $_id = false;

    /**
     * ActiveRecord 继承类使用的 Meta 对象
     *
     * @var QDB_ActiveRecord_Meta
     */
    protected static $_meta;

    /**
     * 指示对象的哪些属性已经做了修改
     *
     * @var array
     */
    private $_changed_props = array();

    /**
     * 指示对象是否对应数据库中的一条记录
     *
     * @var boolean
     */
    private $_is_new_record = true;

    /**
     * 异常捕捉器
     *
     * @var array
     * @access private
     */
    public $__exception_trap = array();

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
            self::$_meta[$this->_class_name] = QDB_ActiveRecord_Meta::instance($this->_class_name);
        }
        $meta = self::$_meta[$this->_class_name];
        /* @var $meta QDB_ActiveRecord_Meta */

        // 设置对象属性默认值
        $this->_props = $meta->default_props;

        if (is_array($data) || $data instanceof Iterator)
        {
            $this->changeProps($data, $names_style, null, $from_storage, true);
        }

        if ($from_storage)
        {
            $this->_is_new_record = false;
        }

        // 触发 after_initialize 事件
        $this->_after_initialize();
        $this->_event(self::AFTER_INITIALIZE);
        $this->_after_initialize_post();
    }

    /**
     * 获得对象的 ID（既对象在数据库中的主键值）
     *
     * 如果对象的 ID 是由多个属性组成，则 id() 方法会返回一个数组。
     *
     * @param boolean $cached 默认返回缓存值
     *
     * @return mixed
     */
    function id($cached = true)
    {
        if($cached && $this->_id !== false)
        {
            return $this->_id;
        }

        $id = array();
        foreach (self::$_meta[$this->_class_name]->idname as $name)
        {
            $id[$name] = $this->{$name};
        }

        if (count($id) == 1)
        {
            $id = reset($id);
        }

        $this->_id = $id;
        return $id;
    }

    /**
     * 获得对象的 ID 属性名（对象在数据库中的主键字段名）
     *
     * 如果对象的 ID 是由多个属性组成，则 idname() 方法会返回一个包含多个属性名的数组。
     *
     * @return string|array
     */
    function idname()
    {
        if (self::$_meta[$this->_class_name]->idname_count > 1)
        {
            return self::$_meta[$this->_class_name]->idname;
        }
        else
        {
            return reset(self::$_meta[$this->_class_name]->idname);
        }
    }

    /**
     * 确定对象是否对应数据库中的一条记录
     *
     * @return boolean
     */
    function isNewRecord()
    {
        return $this->_is_new_record;
    }

    /**
     * 返回当前对象的元信息对象
     *
     * @return QDB_ActiveRecord_Meta
     */
    function getMeta()
    {
        return self::$_meta[$this->_class_name];
    }

    /**
     * 保存对象到数据库
     *
     * @param int $recursion 保存操作递归到多少层
     * @param string $save_method 保存对象的方法
     *
     * @return QDB_ActiveRecord_Abstract 连贯接口
     */
    function save($recursion = 99, $save_method = 'save')
    {
        $inherit_type_field = self::$_meta[$this->_class_name]->inherit_type_field;
        if ($inherit_type_field && empty($this->_props[$inherit_type_field]))
        {
            $this->_props[$inherit_type_field] = $this->_class_name;
        }

        $this->_before_save();
        $this->_event(self::BEFORE_SAVE);
        $this->_before_save_post();

        try
        {
            switch (strtolower($save_method))
            {
            case 'create':
                $exception_event = self::CREATE_EXCEPTION;
                $this->_create($recursion);
                break;
            case 'update':
                $exception_event = self::UPDATE_EXCEPTION;
                $this->_update($recursion);
                break;
            case 'replace':
                $exception_event = self::REPLACE_EXCEPTION;
                $this->_replace($recursion);
                break;
            case 'save':
            default:
                if ($this->isNewRecord())
                {
                    try
                    {
                        $exception_event = self::CREATE_EXCEPTION;
                        $this->_create($recursion);
                    }
                    catch (QDB_Exception_DuplicateKey $ex)
                    {
                        $exception_event = self::UPDATE_EXCEPTION;
                        $this->_update($recursion);
                    }
                }
                else
                {
                    $exception_event = self::UPDATE_EXCEPTION;
                    $this->_update($recursion);
                }
            }

            $this->_after_save();
            $this->_event(self::AFTER_SAVE);
            $this->_after_save_post();
        }
        catch (Exception $ex)
        {
            if (!empty($this->__exception_trap[$exception_event]))
            {
                foreach ($this->__exception_trap[$exception_event] as $callback)
                {
                    array_unshift($callback[1], $this);
                    array_push($callback[1], $ex);
                    call_user_func_array($callback[0], $callback[1]);
                }
            }

            throw $ex;
        }

        $this->_id = false; // 清除缓存
        return $this;
    }

    /**
     * 返回当前对象的一个复制品
     *
     * 返回的复制品没有 ID 值，因此在保存时将会创建一个新记录。
     * __clone() 操作仅限当前对象的属性，对于关联的对象不会进行克隆。
     *
     * @return QDB_ActiveRecord_Abstract 复制的 ActiveRecord 对象
     */
    function __clone()
    {
        foreach(self::$_meta[$this->_class_name]->idname as $name)
        {
            $this->_props[$name] = self::$_meta[$this->_class_name]->table_meta[$name]['default'];
        }

        $this->_id = false; // 清除缓存
        foreach ($this->meta()->props as $prop_name => $tmp)
        {
            $this->_changed_props[$prop_name] = true;
        }
        $this->_is_new_record = true;
        $this->__exception_trap = array();
    }

    /**
     * 判断对象是否有特定的属性
     *
     * @return boolean
     */
    function hasProp($prop_name)
    {
        return isset(self::$_meta[$this->_class_name]->props[$prop_name]);
    }

    /**
     * 从数据库重新读取当前对象的属性，不影响关联的对象
     */
    function reload()
    {
        if (self::$_meta[$this->_class_name]->idname_count > 1)
        {
            // 复合主键
            $where = $this->id();
        }
        else
        {
            // 单一主键
            $where = array( reset(self::$_meta[$this->_class_name]->idname) => $this->id() );
        }

        $row = self::$_meta[$this->_class_name]->find($where)->asArray()->recursion(0)->query();
        $this->changeProps($row, QDB::FIELD, null, true);
    }

    /**
     * 验证对象属性，失败抛出异常
     *
     * @param string $mode 验证模式，可以是 general、create、update
     *
     * @return array|null 为验证通过的项目及错误信息
     */
    function validate($mode = 'general')
    {
        $this->_before_validate();
        $this->_event(self::BEFORE_VALIDATE);
        $this->_before_validate_post();

        // 根据不同的验证模式，引发不同的事件，并且确定要验证的属性
        $meta = self::$_meta[$this->_class_name];
        /* @var $meta QDB_ActiveRecord_Meta */

        if ($mode == 'create')
        {
            $this->_before_validate_on_create();
            $this->_event(self::BEFORE_VALIDATE_ON_CREATE);
            $this->_before_validate_on_create_post();

            $validation_props = array_keys($meta->props2fields);
            foreach ($meta->idname as $idname)
            {
                unset($validation_props[$idname]);
            }
        }
        elseif ($mode == 'update')
        {
            $this->_before_validate_on_update();
            $this->_event(self::BEFORE_VALIDATE_ON_UPDATE);
            $this->_before_validate_on_update_post();
            $validation_props = $this->changes();
        }else {
        	$validation_props=null;
        }

        $error = $meta->validate($this->_props, $validation_props, $mode);

        if (!empty($error))
        {
            throw new QDB_ActiveRecord_ValidateFailedException($error, $this);
        }

        if ($mode == 'create')
        {
            $this->_after_validate_on_create();
            $this->_event(self::AFTER_VALIDATE_ON_CREATE);
            $this->_after_validate_on_create_post();
        }
        elseif ($mode == 'update')
        {
            $this->_after_validate_on_update();
            $this->_event(self::AFTER_VALIDATE_ON_UPDATE);
            $this->_after_validate_on_update_post();
        }

        $this->_after_validate();
        $this->_event(self::AFTER_VALIDATE);
        $this->_after_validate_post();
    }

    /**
     * 销毁对象对应的数据库记录
     */
    function destroy()
    {
        $id = $this->id(false); // 不使用缓存
        if (empty($id))
        {
            throw new QDB_ActiveRecord_DestroyWithoutIdException($this);
        }

        // 引发 before_destroy 事件
        $this->_before_destroy();
        $this->_event(self::BEFORE_DESTROY);
        $this->_before_destroy_post();

        // 处理关联的对象
        $meta = self::$_meta[$this->_class_name];
        /* @var $meta QDB_ActiveRecord_Meta */
        foreach ($meta->associations as $assoc)
        {
            $assoc->onSourceDestroy($this);
        }

        // 确定删除当前对象的条件
        if ($meta->idname_count > 1)
        {
            $where = $id;
        }
        else
        {
            $where = array(reset($meta->idname) => $id);
        }

        // 从数据库中删除当前对象
        $meta->table->delete($where);

        // 引发 after_destroy 事件
        $this->_after_destroy();
        $this->_event(self::AFTER_DESTROY);
        $this->_after_destroy_post();

        //$this->_id = false; // 清除缓存
        // destroy() 并不改变主键值，无需更改 $this->_id 属性
    }

    /**
     * 批量设置对象的属性值
     *
     * 如果指定了 $attr_accessible 参数，则会忽略 ActiveRecord 类的 attr_accessible 和 attr_protected 设置。
     *
     * @param array|object $arr 名值对数组
     * @param int $names_style 键名是属性名还是字段名
     * @param array|string $attr_accessible 指定哪些属性允许设置
     * @param boolean $_from_storage 内部参数
     * @param boolean $_ignore_readonly 内部参数
     *
     * @return QDB_ActiveRecord_Abstract 连贯接口
     */
    function changeProps($arr, $names_style = QDB::PROP, $attr_accessible = null, $_from_storage = false, $_ignore_readonly = false)
    {
        $meta = self::$_meta[$this->_class_name];
        /* @var $meta QDB_ActiveRecord_Meta */

        if ($attr_accessible)
        {
            $attr_accessible = array_flip(Q::normalize($attr_accessible));
            $check_attr_accessible = true;
        }
        else
        {
            $check_attr_accessible = !empty($meta->attr_accessible);
            $attr_accessible = $meta->attr_accessible;
        }

        // 将数组赋值给对象属性
        foreach ($arr as $prop_name => $value)
        {
            if ($names_style == QDB::FIELD)
            {
                if (!isset($meta->fields2props[$prop_name])) { continue; }
                $prop_name = $meta->fields2props[$prop_name];
            }
            elseif (!isset($meta->props[$prop_name]))
            {
                continue;
            }

            if ($_from_storage)
            {
                if ($meta->props[$prop_name]['virtual'])
                {
                    $this->{$prop_name} = $value;
                    unset($this->_changed_props[$prop_name]);
                }
                else
                {
                    $this->_props[$prop_name] = is_null($value)
                            ? NULL
                            : self::_typed($value, $meta->props[$prop_name]['ptype']);
                }
            }
            else
            {
                if ($check_attr_accessible)
                {
                    if (!isset($attr_accessible[$prop_name])) { continue; }
                }
                elseif (isset($meta->attr_protected[$prop_name]))
                {
                    continue;
                }

                if ($_ignore_readonly)
                {
                    $this->changePropForce($prop_name, $value);
                }
                else
                {
                    $this->{$prop_name} = $value;
                }
            }
        }

        return $this;
    }

    /**
     * 强制改变一个属性的值，忽略属性的 readonly 设置
     *
     * @param string $prop_name 要改变的属性名
     * @param mixed $prop_value 属性的值
     *
     * @return QDB_ActiveRecord_Abstract 连贯接口
     */
    function changePropForce($prop_name, $prop_value)
    {
    	$meta = self::$_meta[$this->_class_name];
    	/* @var $meta QDB_ActiveRecord_Meta */
    	if (!isset($meta->props[$prop_name]))
    	{
    		return $this;
    	}

        try
        {
            $ro = $meta->props[$prop_name]['readonly'];
            self::$_meta[$this->_class_name]->props[$prop_name]['readonly'] = false;
            $this->{$prop_name} = $prop_value;
            self::$_meta[$this->_class_name]->props[$prop_name]['readonly'] = $ro;
        }
        catch (Exception $ex)
        {
            self::$_meta[$this->_class_name]->props[$prop_name]['readonly'] = $ro;
            throw $ex;
        }

        return $this;
    }

    /**
     * 确认对象或指定的对象属性是否已经被修改
     *
     * @param string|array $props_name
     *
     * @return boolean
     */
    function changed($props_name = null)
    {
        if (is_null($props_name))
        {
            return ! empty($this->_changed_props);
        }

        $props_name = Q::normalize($props_name);
        foreach ($props_name as $prop_name)
        {
            if (isset($this->_changed_props[$prop_name]))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * 将指定的属性设置为“脏”状态
     *
     * @param string|array $props_name
     *
     * @return QDB_ActiveRecord_Abstract 连贯接口
     */
    function willChanged($props_name)
    {
        $props_name = Q::normalize($props_name);
        foreach ($props_name as $prop_name)
        {
            if (! isset(self::$_meta[$this->_class_name]->props[$prop_name]))
            {
                continue;
            }
            $this->_changed_props[$prop_name] = $prop_name;
        }

        return $this;
    }

    /**
     * 获得修改过的属性
     *
     * @return array
     */
    function changes()
    {
        return $this->_changed_props;
    }

    /**
     * 清除所有属性或指定的“脏”状态
     *
     * @param string|array $props
     *
     * @return QDB_ActiveRecord_Abstract 连贯接口
     */
    function cleanChanges($props = null)
    {
        if ($props)
        {
            $props = Q::normalize($props);
            foreach ($props as $prop)
            {
                unset($this->_changed_props[$prop]);
            }
        }
        else
        {
            $this->_changed_props = array();
        }
        return $this;
    }

    /**
     * 获得包含对象所有属性的数组
     *
     * @param int $recursion
     * @param int $names_style
     *
     * @return array
     */
    function toArray($recursion = 99, $names_style = QDB::PROP)
    {
        $data = array();
        $meta = self::$_meta[$this->_class_name];
        /* @var $meta QDB_ActiveRecord_Meta */
        foreach ($meta->props as $prop_name => $config)
        {
            if ($names_style == QDB::PROP)
            {
                $name = $prop_name;
            }
            else
            {
                $name = $meta->props2fields[$prop_name];
            }

            if ($config['assoc'])
            {
                if ($recursion > 0 && isset($this->_props[$prop_name]))
                {
                    $data[$name] = $this->{$prop_name}->toArray($recursion - 1, $names_style);
                }
            }
            elseif ($config['virtual'] && empty($config['getter']))
            {
                continue;
            }
            else
            {
                $data[$name] = $this->{$prop_name};
            }
        }
        return $data;
    }

    /**
     * 返回对象所有属性的 JSON 字符串
     *
     * @param int $recursion
     * @param int $names_style
     *
     * @return string
     */
    function toJSON($recursion = 99, $names_style = QDB::PROP)
    {
        return json_encode($this->toArray($recursion, $names_style));
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
        if (!isset(self::$_meta[$this->_class_name]->props[$prop_name]))
        {
            throw new QDB_ActiveRecord_UndefinedPropException($this->_class_name, $prop_name);
        }

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

        return $this->_props[$prop_name];
    }

    /**
     * 魔法方法，实现对象属性的设置
     *
     * @param string $prop_name
     * @param mixed $value
     */
    function __set($prop_name, $value)
    {
        $meta = self::$_meta[$this->_class_name];
        /* @var $meta QDB_ActiveRecord_Meta */

        if (! isset($meta->props[$prop_name]))
        {
            throw new QDB_ActiveRecord_UndefinedPropException($this->_class_name, $prop_name);
        }

        $config = $meta->props[$prop_name];
        if ($config['readonly'])
        {
            throw new QDB_ActiveRecord_ChangingReadonlyPropException($this->_class_name, $prop_name);
        }

        if (!empty($config['setter']))
        {
            // 如果指定了属性的 setter，则通过 setter 方法来修改属性值
            list($callback, $custom_parameters) = $config['setter'];
            if (!is_array($callback))
            {
                $callback = array($this, $callback);
                $args = array($value, $prop_name, $custom_parameters, & $this->_props);
            }else {
                $args = array($this,$value, $prop_name, $custom_parameters, & $this->_props);
            }
            return call_user_func_array($callback, $args);
        }

        if ($config['assoc'])
        {
            // 在指定关联对象时，要进行类型检查
            if ($config['assoc'] == QDB::HAS_ONE || $config['assoc'] == QDB::BELONGS_TO)
            {
                if ($value instanceof $config['assoc_class'])
                {
                    $this->_props[$prop_name] = $value;

                    if ($config['assoc'] == QDB::BELONGS_TO)
                    {
                        $assoc = $meta->assoc($prop_name);
                        $this->_props[$assoc->source_key] = $value[$assoc->target_key];
                        $this->_changed_props[$assoc->source_key] = $assoc->source_key;
                    }
                }
                else
                {
                    throw new QDB_ActiveRecord_SettingPropTypeMismatchException($this->_class_name,
                        $prop_name, $config['assoc_class'],
                        gettype($value));
                }
            }
            else
            {
                if (is_array($value))
                {
                    $this->_props[$prop_name] = QColl::createFromArray($value, $config['assoc_class']);
                }
                elseif ($value instanceof Iterator)
                {
                    $this->_props[$prop_name] = $value;
                }
                else
                {
                    throw new QDB_ActiveRecord_SettingPropTypeMismatchException($this->_class_name, $prop_name, 'Iterator', gettype($value));
                }
            }

            $this->_changed_props[$prop_name] = $prop_name;
        }
        elseif ($this->_props[$prop_name] !== $value)
        {
            $this->_props[$prop_name] = self::_typed($value, $config['ptype']);
            $this->_changed_props[$prop_name] = $prop_name;
        }
    }

    /**
     * 魔法方法，实现对 isset() 的支持
     *
     * @param string $prop_name
     *
     * @return boolean
     */
    function __isset($prop_name)
    {
        return array_key_exists($prop_name, $this->_props);
    }

    /**
     * 魔法方法，用于调用行为插件为对象添加的方法
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    function __call($method, array $args)
    {
        if (isset(self::$_meta[$this->_class_name]->methods[$method]))
        {
            $callback = self::$_meta[$this->_class_name]->methods[$method];
            foreach ($args as $arg)
            {
                array_push($callback[1], $arg);
            }
            array_unshift($callback[1], $this);
            return call_user_func_array($callback[0], $callback[1]);
        }

        // getXX() 和 setXX() 方法
        $prefix = substr($method, 0, 3);
        if ($prefix == 'get')
        {
            $prop_name = substr($method, 3);
            return $this->{$prop_name};
        }
        elseif ($prefix == 'set')
        {
            $prop_name = substr($method, 3);
            $this->{$prop_name} = reset($args);
            return null;
        }

        throw new QDB_ActiveRecord_CallToUndefinedMethodException($this->_class_name, $method);
    }

    /**
     * ArrayAccess 接口方法
     *
     * @param string $prop_name
     *
     * @return boolean
     */
    function offsetExists($prop_name)
    {
        return array_key_exists($prop_name, $this->_props);
    }

    /**
     * ArrayAccess 接口方法
     *
     * @param string $prop_name
     * @param mixed $value
     */
    function offsetSet($prop_name, $value)
    {
        $this->{$prop_name} = $value;
    }

    /**
     * ArrayAccess 接口方法
     *
     * @param string $prop_name
     *
     * @return boolean
     */
    function offsetGet($prop_name)
    {
        return $this->{$prop_name};
    }

    /**
     * ArrayAccess 接口方法
     *
     * @param string $prop_name
     */
    function offsetUnset($prop_name)
    {
    	$this->{$prop_name} = null;
    }

    /**
     * 用于 QColl 的回调方法
     */
    static function _qcoll_callback()
    {
        return array('tojson' => 'multiToJSON');
    }

    /**
     * 将多个 ActiveRecord 对象转换为 JSON 字符串
     *
     * @param array $objects
     * @param int $recursion
     * @param int $names_style
     *
     * @return string
     */
    static function multiToJSON(array $objects, $recursion = 99, $names_style = QDB::PROP)
    {
        $arr = array();
        while (list(, $obj) = each($objects))
        {
            $arr[] = $obj->toArray($recursion, $names_style);
        }
        return json_encode($arr);
    }

    /**
     * 调用 ActiveRecord 对象的动态方法
     *
     * @param string $method
     *
     * @return mixed
     */
    protected function _method($method)
    {
        $args = func_get_args();
        array_shift($args);
        return $this->__call($method, $args);
    }

    /**
     * 触发事件
     *
     * @param int $event
     */
    protected function _event($event)
    {
        $meta = self::$_meta[$this->_class_name];
        if (empty($meta->callbacks[$event]))
        {
            return;
        }

        foreach ($meta->callbacks[$event] as $callback)
        {
            array_unshift($callback[1], $this);
            call_user_func_array($callback[0], $callback[1]);
        }
    }

    /**
     * 在数据库中创建对象
     *
     * @param int $recursion
     */
    protected function _create($recursion = 99)
    {
        $meta = self::$_meta[$this->_class_name];
        /* @var $meta QDB_ActiveRecord_Meta */

        // 根据 create_autofill 设置对属性进行填充
        $this->_autofill('create');

        // 引发 before_create 事件
        $this->_before_create();
        $this->_event(self::BEFORE_CREATE);
        $this->_before_create_post();

        // 进行 create 验证
        $this->validate('create', true);

        // 特别处理 BELONGS_TO 关联
        foreach ($meta->belongsto_props as $prop_name => $assoc)
        {
            /* @var $assoc QDB_ActiveRecord_Association_BelongsTo */
            $mapping_name = $assoc->mapping_name;
            $source_key = $assoc->source_key;

            if (empty($this->_props[$mapping_name]))
            {
                if (empty($this->_props[$source_key]))
                {
                    if ($this->_props[$source_key] === $meta->props[$source_key]['default_value']
                        && !is_null($meta->props[$source_key]['default_value']))
                    {
                        $this->changePropForce($source_key, $meta->props[$source_key]['default_value']);
                    }
                    else
                    {
                        // 如果BELONGS TO关联设置了允许空值，则跳过抛出异常
                        if(empty($assoc->source_meta->props[$mapping_name]['assoc_params']['skip_empty']))
                        {
                            throw new QDB_ActiveRecord_ExpectsAssocPropException($this->_class_name, $mapping_name);
                        }
                    }
                }
            }
            else
            {
                $belongsto = $this->_props[$mapping_name];
                /* @var $belongsto QDB_ActiveRecord_Abstract */
                $this->changePropForce($source_key, $belongsto->{$assoc->target_key});
            }
        }

        // 准备要保存到数据库的数据
        $save_data = array();
        foreach ($this->_props as $prop_name => $value)
        {
            if (isset($meta->create_reject[$prop_name]) || $meta->props[$prop_name]['virtual'])
            {
                continue;
            }
            $save_data[$meta->props2fields[$prop_name]] = $value;
        }

        // 将名值对保存到数据库
        $pk = $meta->table->insert($save_data, true);

        // 将获得的主键值指定给对象
        foreach ($pk as $field_name => $field_value)
        {
            $this->_props[$meta->props2fields[$field_name]] = $field_value;
        }

        // 遍历关联的对象，并调用对象的save()方法
        foreach ($meta->associations as $prop => $assoc)
        {
            if ($assoc->type == QDB::BELONGS_TO || !isset($this->_props[$prop]))
            {
                continue;
            }

            /* @var $assoc QDB_ActiveRecord_Association_Abstract */

            $assoc->init();
            $source_key_value = $this->{$assoc->source_key};

            if (strlen($source_key_value) == 0)
            {
                throw new QDB_ActiveRecord_ExpectsAssocPropException($this->_class_name, $assoc->source_key);
            }

            $assoc->onSourceSave($this, $recursion - 1);
        }
        // 成功保存到数据库后已经不是新记录了
		$this->_is_new_record=false;
        // 引发after_create事件
        $this->_after_create();
        $this->_event(self::AFTER_CREATE);
        $this->_after_create_post();

        // 清除所有属性的“脏”状态
        $this->_changed_props = array();
    }

    /**
     * 更新对象到数据库
     *
     * @param int $recursion
     */
    protected function _update($recursion = 99)
    {
        $meta = self::$_meta[$this->_class_name];
        /* @var $meta QDB_ActiveRecord_Meta */

        /**
         * 仅在有属性更新时才引发 update 事件，并进行更新操作
         */
        if (!empty($this->_changed_props))
        {
	        // 根据 update_autofill 设置对属性进行填充
	        $this->_autofill('update');

	        // 引发 before_update 事件
	        $this->_before_update();
	        $this->_event(self::BEFORE_UPDATE);
	        $this->_before_update_post();

            // 进行 update 验证
            $this->validate('update', true);

	        // 准备要更新到数据库的数据
	        $save_data = array();
	        foreach ($this->_props as $prop_name => $value)
	        {
	            // 根据 update_reject 过滤掉不允许更新的属性
	            if (isset($meta->update_reject[$prop_name])
                    || ($meta->props[$prop_name]['virtual'] && !isset($meta->table_meta[$prop_name])))
	            {
	                continue;
	            }
	            // 只有指定为脏状态的属性才更新到数据库
	            if (isset($this->_changed_props[$prop_name]))
	            {
	                $save_data[$meta->props2fields[$prop_name]] = $value;
	            }
	        }

            if (!empty($save_data))
            {
                // 确定更新条件
                $conditions = array();
                foreach ($meta->table->getPK() as $field_name)
                {
                    $prop_name = $meta->fields2props[$field_name];
                    unset($save_data[$field_name]);
                    $conditions[$field_name] = $this->_props[$prop_name];
                }
                if (!empty($save_data))
                {
                    // 将名值对保存到数据库
                    $meta->table->update($save_data, $conditions);
                }
            }
        }

        // 遍历关联的对象，并调用对象的save()方法
        foreach ($meta->associations as $prop => $assoc)
        {
            if (! isset($this->_props[$prop]))
            {
                continue;
            }

            $assoc->init();
            $source_key_value = $this->{$assoc->source_key};

            if (strlen($source_key_value) == 0)
            {
                throw new QDB_ActiveRecord_ExpectsAssocPropException($this->_class_name, $assoc->source_key);
            }

            $assoc->onSourceSave($this, $recursion - 1);
        }

        // 引发 after_update 事件
        $this->_after_update();
        $this->_event(self::AFTER_UPDATE);
        $this->_after_update_post();

        // 清除所有属性的“脏”状态
        $this->_changed_props = array();
    }

    /**
     * 替换数据库中的对象，如果不存在则创建新记录
     *
     * @param int $recursion
     */
    protected function _replace($recursion = 99)
    {
        if ($this->isNewRecord() || $this->changed())
        {
            if ($this->isNewRecord())
            {
                try
                {
                    // 如果是尚未保存到数据库的记录，则创建新记录
                    $this->_create($recursion);
                }
                catch (QDB_Exception_DuplicateKey $ex)
                {
                    $this->_update($recursion);
                }
            }
            else
            {
                $this->_update($recursion);
            }
        }
    }

    /**
     * 对当前对象的属性进行自动填充
     *
     * @param string $mode
     */
    protected function _autofill($mode)
    {
        $meta = self::$_meta[$this->_class_name];
        /* @var $meta QDB_ActiveRecord_Meta */
        $fill_props = ($mode == 'create') ? $meta->create_autofill : $meta->update_autofill;

        foreach ($fill_props as $prop => $fill)
        {
            if ($fill === self::AUTOFILL_DATETIME)
            {
                $this->_props[$prop] = date('Y-m-d H:i:s', CURRENT_TIMESTAMP);
            }
            elseif ($fill === self::AUTOFILL_TIMESTAMP)
            {
                $this->_props[$prop] = intval(CURRENT_TIMESTAMP);
            }
            elseif ($fill === self::AUTOFILL_DATE)
            {
                $this->_props[$prop] = date('Y-m-d', CURRENT_TIMESTAMP);
            }
            elseif ($fill === self::AUTOFILL_TIME)
            {
                $this->_props[$prop] = date('H:i:s', CURRENT_TIMESTAMP);
            }
            elseif (!is_array($fill))
            {
                $this->_props[$prop] = $fill;
            }
            else
            {
                $this->_props[$prop] = self::_typed(call_user_func($fill), $meta->props[$prop]['ptype']);
            }
            // 设置“脏”状态
            $this->_changed_props[$prop] = true;
        }
    }

    /**
     * 返回类型化以后的值
     *
     * @param mixed $value
     * @param string $ptype
     *
     * @return mixed
     */
    static protected function _typed($value, $ptype)
    {
        switch($ptype)
        {
        case 'int1':
        case 'int2':
        case 'int3':
        case 'int4':
        case 'timestamp':
            return intval($value);
        case 'float':
        case 'double':
        case 'dec':
        	return doubleval($value);
        case 'bool':
            return (bool)$value;
        case 'date':
        case 'datetime':
            return empty($value) ? null : $value;
        }

        return $value;
    }

    /**
     * 事件回调：开始验证之前
     */
    protected function _before_validate() {}

    protected function _before_validate_post() {}

    /**
     * 事件回调：为创建记录进行的验证开始之前
     */
    protected function _before_validate_on_create() {}

    protected function _before_validate_on_create_post() {}

    /**
     * 事件回调：为创建记录进行的验证完成之后
     */
    protected function _after_validate_on_create() {}

    protected function _after_validate_on_create_post() {}

    /**
     * 事件回调：为更新记录进行的验证开始之前
     */
    protected function _before_validate_on_update() {}

    protected function _before_validate_on_update_post() {}

    /**
     * 事件回调：为更新记录进行的验证完成之后
     */
    protected function _after_validate_on_update() {}

    protected function _after_validate_on_update_post() {}

    /**
     * 事件回调：验证完成之后
     */
    protected function _after_validate() {}

    protected function _after_validate_post() {}

    /**
     * 事件回调：保存记录之前
     */
    protected function _before_save() {}

    protected function _before_save_post() {}

    /**
     * 事件回调：保存记录之后
     */
    protected function _after_save() {}

    protected function _after_save_post() {}

    /**
     * 事件回调：创建记录之前
     */
    protected function _before_create() {}

    protected function _before_create_post() {}

    /**
     * 事件回调：创建记录之后
     */
    protected function _after_create() {}

    protected function _after_create_post() {}

    /**
     * 事件回调：更新记录之前
     */
    protected function _before_update() {}

    protected function _before_update_post() {}

    /**
     * 事件回调：更新记录之后
     */
    protected function _after_update() {}

    protected function _after_update_post() {}

    /**
     * 事件回调：删除记录之前
     */
    protected function _before_destroy() {}

    protected function _before_destroy_post() {}

    /**
     * 事件回调：删除记录之后
     */
    protected function _after_destroy() {}

    protected function _after_destroy_post() {}

    /**
     * 事件回调：对象构造之后
     */
    protected function _after_initialize() {}

    protected function _after_initialize_post() {}
}



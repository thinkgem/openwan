<?php

/**
 * 定义 QDB_ActiveRecord_Meta 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @package orm
 */

/**
 * QDB_ActiveRecord_Meta 类封装了 QDB_ActiveRecord_Abstract 继承类的元信息
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @package orm
 */
class QDB_ActiveRecord_Meta implements QDB_ActiveRecord_Callbacks
{
    /**
     * ID 属性名
     *
     * @var array
     */
    public $idname;

    /**
     * ID 属性包含多少个属性
     *
     * @var int
     */
    public $idname_count;

    /**
     * 数据表的元信息
     *
     * @var array
     */
    public $table_meta;

    /**
     * 验证规则
     *
     * @var array
     */
    public $validations = array();

    /**
     * 允许使用 mass-assignment 方式赋值的属性
     *
     * 如果指定了 attr_accessible，则忽略 attr_protected 的设置。
     *
     * @var array
     */
    public $attr_accessible = array();

    /**
     * 拒绝使用 mass-assignment 方式赋值的属性
     *
     * @var array
     */
    public $attr_protected = array();

    /**
     * 创建时要过滤的属性
     *
     * @var array
     */
    public $create_reject = array();

    /**
     * 更新时要过滤的属性
     *
     * @var array
     */
    public $update_reject = array();

    /**
     * 创建时要自动填充的属性
     *
     * @var array
     */
    public $create_autofill = array();

    /**
     * 更新时要自动填充的属性
     *
     * @var array
     */
    public $update_autofill = array();

    /**
     * 属性到字段名的映射
     *
     * @var array
     */
    public $props2fields = array();

    /**
     * 字段名到属性的映射
     *
     * @var array
     */
    public $fields2props = array();

    /**
     * 所有属性的元信息
     *
     * @var array of properties meta
     */
    public $props = array();

    /**
     * 所有属性的默认值，用于初始化一个新的 ActiveRecord 实例
     *
     * @var array
     */
    public $default_props = array();

    /**
     * ActiveRecord 之间的关联
     *
     * @code php
     * array (
     *     'prop_name' => $assoc
     * )
     * @endcode
     *
     * 如果关联已经初始化，则 $assoc 是一个 QDB_ActiveRecord_Association_Abstract 继承类实例。
     * 否则 $assoc 为 false。
     *
     * @var array of QDB_ActiveRecord_Association_Abstract
     */
    public $associations = array();

    /**
     * 事件钩子
     *
     * @var array of callbacks
     */
    public $callbacks = array();

    /**
     * 扩展的方法
     *
     * @var array of callbacks
     */
    public $methods = array();

    /**
     * 扩展的静态方法
     *
     * @var array of callbacks
     */
    public $static_methods = array();

    /**
     * 表数据入口
     *
     * @var QDB_Table
     */
    public $table;

    /**
     * Meta 对应的 ActiveRecord 继承类
     *
     * @var string
     */
    public $class_name;

    /**
     * ActiveRecord 的基础类
     *
     * @var string
     */
    public $inherit_base_class;

    /**
     * 用于指定继承类名称的字段名
     *
     * @var string
     */
    public $inherit_type_field;

    /**
     * BELONGS_TO 关联的 source_key
     *
     * @var array
     */
    public $belongsto_props = array();

    /**
     * 行为插件对象
     *
     * @var array of QDB_ActiveRecord_Behavior_Abstract objects
     */
    protected $_behaviors = array();

    /**
     * 指示是否已经初始化了对象的关联
     *
     * @var boolean
     */
    protected $_associations_inited = false;

    /**
     * 可用的对象聚合类型
     *
     * @var array
     */
    protected static $_assoc_types = array(
        QDB::HAS_ONE,
        QDB::HAS_MANY,
        QDB::BELONGS_TO,
        QDB::MANY_TO_MANY
    );

    /**
     * 验证策略可用的选项
     *
     * @var array
     */
    protected static $_validation_policy_options = array(
        'allow_null'      => false,
        'check_all_rules' => false,
    );

    /**
     * 所有 ActiveRecord 继承类的 Meta 对象
     *
     * @var array of QDB_ActiveRecord_Meta
     */
    protected static $_metas = array();

    /**
     * 构造函数
     *
     * @param string $class
     */
    protected function __construct($class)
    {
        $this->_init1($class);
    }

    /**
     * 获得指定指定 ActiveRecord 继承类的元对象唯一实例
     *
     * @param string $class
     *
     * @return QDB_ActiveRecord_Meta
     */
    static function instance($class)
    {
        if (!isset(self::$_metas[$class]))
        {
            self::$_metas[$class] = new QDB_ActiveRecord_Meta($class);
            self::$_metas[$class]->_init2();
        }
        return self::$_metas[$class];
    }

    /**
     * 返回一个根据 $data 数组构造的 ActiveRecord 继承类实例
     *
     * @return QDB_ActiveRecord_Abstract
     */
    function newObject(array $data = null)
    {
        return new $this->class_name($data);
    }

    /**
     * 根据表名构造一个元对象
     *
     *
     */
    function createFromTable($table_name, $class_name = null)
    {
        // TODO!
    }

    /**
     * 开启一个查询
     *
     * @return QDB_Select
     */
    function find()
    {
        return $this->findByArgs(func_get_args());
    }

    /**
     * 开启一个查询，并根据提供的参数设置查询对象
     *
     * @param array $args
     *
     * @return QDB_Select
     */
    function findByArgs(array $args = array())
    {
        $select = new QDB_Select($this->table->getConn());
        $select->asColl()->from($this->table)->asObject($this->class_name);
        $c = count($args);
        if ($c > 0)
        {
            if ($c == 1 && is_int($args[0]) && $this->idname_count == 1)
            {
                $select->where(array(reset($this->idname) => $args[0]));
            }
            else
            {
                call_user_func_array(array($select, 'where'), $args);
            }
        }

        if ($this->inherit_base_class && $this->inherit_type_field)
        {
            // 如果是来自某个继承类的查询，则限定只能查询该类型的对象
            $select->where(array($this->inherit_type_field => $this->class_name));
        }

        return $select;
    }

    /**
     * 更新符合条件的对象
     *
     * @param array $attribs
     */
    function updateWhere(array $attribs)
    {
        $args = func_get_args();
        array_shift($args);
        $objs = $this->findByArgs($args)->all()->query();
        foreach ($objs as $obj)
        {
            /* @var $obj QDB_ActiveRecord_Abstract */
            $obj->changeProps($attribs);
            $obj->save(0, 'update');
            unset($obj);
        }
    }

    /**
     * 更新符合条件的记录
     */
    function updateDbWhere()
    {
        $args = func_get_args();
        call_user_func_array(array($this->table, 'update'), $args);
    }

    /**
     * 实例化符合条件的对象，并调用这些对象的 destroy() 方法，返回被删除的对象数
     *
     * @return int
     */
    function destroyWhere()
    {
        $objs = $this->findByArgs(func_get_args())->all()->query();
        $c = count($objs);
        foreach ($objs as $obj)
        {
            $obj->destroy();
            unset($obj);
        }
        return $c;
    }

    /**
     * 从数据库中直接删除符合条件的对象
     *
     * 与 destroyWhere() 不同，deleteWhere() 会直接从数据库中删除符合条件的记录。
     * 而不是先把符合条件的对象查询出来再调用对象的 destroy() 方法进行删除。
     *
     * 因此，deleteWhere() 速度更快，但无法处理对象间的关联关系。
     */
    function deleteWhere()
    {
        $args = func_get_args();
        call_user_func_array(array($this->table, 'delete'), $args);
    }

    /**
     * 对数据进行验证，返回所有未通过验证数据的错误信息
     *
     * @param array $data 要验证的数据
     * @param array|string $props 指定仅仅验证哪些属性
     * @param string $mode 验证模式
     *
     * @return array 所有没有通过验证的属性名称及验证规则
     */
    function validate(array $data, $props = null, $mode = 'general')
    {
        if (!is_null($props))
        {
            $props = array_flip(Q::normalize($props));
        }
        else
        {
            $props = $this->props2fields;
        }

        $error = array();

        $mode = 'on_' . strtolower($mode);

        foreach ($this->validations as $prop => $policy)
        {
            if (!isset($props[$prop]))
            {
                continue;
            }
            if (!isset($data[$prop]))
            {
                $data[$prop] = null;
            }

            if (isset($this->belongsto_props[$prop]) && empty($policy['rules']))
            {
                continue;
            }

            if (isset($policy[$mode]))
            {
                $policy = $policy[$mode];
            }

            if (is_null($data[$prop]))
            {
                // 对于 null 数据，如果指定了自动填充，则跳过对该数据的验证
                switch ($mode)
                {
                case 'update':
                    if (isset($this->update_autofill[$prop]))
                    {
                        continue 2;
                    }
                    break;

                default:
                    if (isset($this->create_autofill[$prop]))
                    {
                        continue 2;
                    }
                    break;
                }

                if (!$policy['policy']['allow_null'])
                {
                    // allow_null 为 false 时，如果数据为 null，则视为验证失败
                    $error[$prop]['not_null'] = 'not null';
                }
                elseif (empty($policy['rules']))
                {
                    continue;
                }
            }

            foreach ($policy['rules'] as $index => $rule)
            {
                $validation = array_shift($rule);
                $msg = array_pop($rule);
                array_unshift($rule, $data[$prop]);

                $ret = QValidator::validateByArgs($validation, $rule);
                if ($ret === QValidator::SKIP_OTHERS)
                {
                    break;
                }
                elseif (!$ret)
                {
                    $error[$prop][$index] = $msg;
                    if (isset($policy['policy']) && !$policy['policy']['check_all_rules'])
                    {
                        break;
                    }
                }
            }
        }

        return $error;
    }

    /**
     * 获得对象的关联对象
     *
     * @param QDB_ActiveRecord_Abstract $obj
     * @param string $prop_name
     *
     * @return QDB_ActiveRecord_Abstract|QDB_ActiveRecord_Association_Coll
     */
    function relatedObjects(QDB_ActiveRecord_Abstract $obj, $prop_name)
    {
        /**
         * @var QDB_ActiveRecord_Meta
         */
        $target_meta = self::instance($this->props[$prop_name]['assoc_class']);
        $assoc = $this->assoc($prop_name)->init();
        $source_key_value = $obj->{$assoc->source_key};

        if (empty($source_key_value))
        {
            if ($assoc->one_to_one)
            {
                return $target_meta->newObject();
            }
            else
            {
                return new QDB_ActiveRecord_Association_Coll($target_meta->class_name);
            }
        }

        switch ($assoc->type)
        {
        case QDB::HAS_ONE:
        case QDB::HAS_MANY:
        case QDB::BELONGS_TO:
            /**
             * @var QDB_Select
             */
            $select = $target_meta->find(array($assoc->target_key => $source_key_value));
            if (strlen($assoc->source_key_2nd) && strlen($assoc->target_key_2nd)){
            	$select->where(array($assoc->target_key_2nd=>$obj->{$assoc->source_key_2nd}));
            }
            break;
        case QDB::MANY_TO_MANY:
            $assoc->mid_table->init();
            /* @var $assoc QDB_ActiveRecord_Association_ManyToMany */
            if (strlen($assoc->mid_common_key)){
            	#第二关联键
	            $select = $target_meta->find("[{$assoc->target_key}] = [m.{$assoc->mid_target_key}]")
                      ->joinInner(array('m' => $assoc->mid_table), null,
                           "[{$assoc->mid_source_key}] = ? and [{$assoc->mid_common_key}] =?", $source_key_value,$obj->{$assoc->mid_common_key});
            }else {
	            $select = $target_meta->find("[{$assoc->target_key}] = [m.{$assoc->mid_target_key}]")
                      ->joinInner(array('m' => $assoc->mid_table), null,
                           "[{$assoc->mid_source_key}] = ?", $source_key_value);
            }
            break;
        default:
            // LC_MSG: 无效的关联类型 "%s".
            throw new QDB_ActiveRecord_Association_Exception(__('无效的关联类型 "%s".', $assoc->type));
        }

        /* @var $select QDB_Select */

        if (!empty($assoc->on_find_where))
        {
            call_user_func_array(array($select, 'where'), $assoc->on_find_where);
        }
        if (!empty($assoc->on_find_order))
        {
            $select->order($assoc->on_find_order);
        }

        if ($assoc->on_find === 'all' || $assoc->on_find === true)
        {
            $select->all();
        }
        elseif (is_int($assoc->on_find))
        {
            $select->limit(0, $assoc->on_find);
        }
        elseif (is_array($assoc->on_find))
        {
            $select->limit($assoc->on_find[0], $assoc->on_find[1]);
        }

        if ($assoc->one_to_one)
        {
            $objects = $select->query();
            if (count($objects))
            {
                return (is_object($objects)) ? $objects->first() : reset($objects);
            }
            else
            {
                return $target_meta->newObject();
            }
        }
        else
        {
            return $select->asColl()->query();
        }
    }

    /**
     * 检查是否绑定了指定的行为插件
     *
     */
    function hasBindBehavior($name)
    {
        return isset($this->_behaviors[$name]) ? true : false;
    }

    /**
     * 绑定行为插件
     *
     * @param string|array $behaviors
     * @param array $config
     *
     * @return QDB_ActiveRecord_Meta
     */
    function bindBehaviors($behaviors, array $config = null)
    {
        $behaviors = Q::normalize($behaviors);
        if (!is_array($config))
        {
            $config = array();
        }
        else
        {
            $config = array_change_key_case($config, CASE_LOWER);
        }

        foreach ($behaviors as $name)
        {
            $name = strtolower($name);
            // 已经绑定过的插件不再绑定
            if (isset($this->_behaviors[$name]))
            {
                continue;
            }

            // 载入插件
            $class = 'Model_Behavior_' . ucfirst($name);

            // 构造行为插件
            $settings = (!empty($config[$name])) ? $config[$name] : array();
            $this->_behaviors[$name] = new $class($this, $settings);
        }

        return $this;
    }

    /**
     * 撤销与指定行为插件的绑定
     *
     * @param string|array $behaviors
     *
     * @return QDB_ActiveRecord_Meta
     */
    function unbindBehaviors($behaviors)
    {
        $behaviors = Q::normalize($behaviors);

        foreach ($behaviors as $name)
        {
            $name = strtolower($name);
            if (!isset($this->_behaviors[$name]))
            {
                continue;
            }
            $this->_behaviors[$name]->unbind();
            unset($this->_behaviors[$name]);
        }

        return $this;
    }

    /**
     * 添加一个动态方法
     *
     * @param string $method_name
     * @param callback $callback
     * @param array $custom_parameters
     *
     * @return QDB_ActiveRecord_Meta
     */
    function addDynamicMethod($method_name, $callback, array $custom_parameters = array())
    {
        if (!empty($this->methods[$method_name]))
        {
            // LC_MSG: 指定的动态方法名 "%s" 已经存在于 "%s" 对象中.
            throw new QDB_ActiveRecord_Meta_Exception(__('指定的动态方法名 "%s" 已经存在于 "%s" 对象中.', $method_name, $this->class_name));
        }
        $this->methods[$method_name] = array($callback, $custom_parameters);
        return $this;
    }

    /**
     * 删除指定的动态方法
     *
     * @param string $method_name
     *
     * @return QDB_ActiveRecord_Meta
     */
    function removeDynamicMethod($method_name)
    {
        unset($this->methods[$method_name]);
        return $this;
    }

    /**
     * 添加一个静态方法
     *
     * @param string $method_name
     * @param callback $callback
     * @param array $custom_parameters
     *
     * @return QDB_ActiveRecord_Meta
     */
    function addStaticMethod($method_name, $callback, array $custom_parameters = array())
    {
        if (!empty($this->static_methods[$method_name]))
        {
            // LC_MSG: 指定的静态方法名 "%s" 已经存在于 "%s" 对象中.
            throw new QDB_ActiveRecord_Meta_Exception(__('指定的静态方法名 "%s" 已经存在于 "%s" 对象中.', $method_name, $this->class_name));
        }
        $this->static_methods[$method_name] = array($callback, $custom_parameters);
        return $this;
    }

    /**
     * 删除指定的静态方法
     *
     * @param string $method_name
     *
     * @return QDB_ActiveRecord_Meta
     */
    function removeStaticMethod($method_name)
    {
        unset($this->static_methods[$method_name]);
        return $this;
    }

    /**
     * 设置属性的 setter 方法
     *
     * @param string $prop_name
     * @param callback $callback
     * @param array $custom_parameters
     *
     * @return QDB_ActiveRecord_Meta
     */
    function setPropSetter($prop_name, $callback, array $custom_parameters = array())
    {
        if (isset($this->props[$prop_name]))
        {
            $this->props[$prop_name]['setter'] = array($callback, $custom_parameters);
        }
        else
        {
            $this->addProp($prop_name, array('setter' => $callback, 'setter_params'=> $custom_parameters));
        }

        return $this;
    }

    /**
     * 取消属性的 setter 方法
     *
     * @param string $prop_name
     *
     * @return QDB_ActiveRecord_Meta
     */
    function unsetPropSetter($prop_name)
    {
        if (isset($this->props[$prop_name]))
        {
            unset($this->props[$prop_name]['setter']);
        }
        return $this;
    }

    /**
     * 设置属性的 getter 方法
     *
     * @param string $name
     * @param callback $callback
     * @param array $custom_parameters
     *
     * @return QDB_ActiveRecord_Meta
     */
    function setPropGetter($name, $callback, array $custom_parameters = array())
    {
        if (isset($this->props[$name]))
        {
            $this->props[$name]['getter'] = array($callback, $custom_parameters);
        }
        else
        {
            $this->addProp($name, array('getter' => $callback, 'getter_params'=>$custom_parameters));
        }
    }

    /**
     * 取消属性的 getter 方法
     *
     * @param string $prop_name
     *
     * @return QDB_ActiveRecord_Meta
     */
    function unsetPropGetter($prop_name)
    {
        if (isset($this->props[$prop_name]))
        {
            unset($this->props[$prop_name]['getter']);
        }
        return $this;
    }

    /**
     * 为指定事件添加处理方法
     *
     * @param int $event_type
     * @param callback $callback
     * @param array $custom_parameters
     *
     * @return QDB_ActiveRecord_Meta
     */
    function addEventHandler($event_type, $callback, array $custom_parameters = array())
    {
        $this->callbacks[$event_type][] = array($callback, $custom_parameters);
        return $this;
    }

    /**
     * 为指定对象添加异常捕捉器
     *
     * @param QDB_ActiveRecord_Abstract $obj
     * @param int $exception_type
     * @param callback $callback
     * @param array $custom_parameters
     *
     * @return QDB_ActiveRecord_Meta
     */
    function addExceptionTrap(QDB_ActiveRecord_Abstract $obj, $exception_type, $callback, array $custom_parameters = array())
    {
        $obj->__exception_trap[$exception_type][] = array($callback, $custom_parameters);
        return $this;
    }

    /**
     * 删除指定事件的一个处理方法
     *
     * @param int $event_type
     * @param callback $callback
     *
     * @return QDB_ActiveRecord_Meta
     */
    function removeEventHandler($event_type, $callback)
    {
        if (empty($this->callbacks[$event_type]))
        {
            return $this;
        }

        foreach ($this->callbacks[$event_type] as $offset => $arr)
        {
            if ($arr[0] == $callback)
            {
                unset($this->callbacks[$event_type][$offset]);
                return $this;
            }
        }
        return $this;
    }

    /**
     * 添加一个属性
     *
     * @param string $prop_name
     * @param array $config
     *
     * @return QDB_ActiveRecord_Meta
     */
    function addProp($prop_name, array $config)
    {
        if (isset($this->props[$prop_name]))
        {
            // LC_MSG: 尝试添加的属性 "%s" 已经存在.
            throw new QDB_ActiveRecord_Meta_Exception(__('尝试添加的属性 "%s" 已经存在.', $prop_name));
        }

        $config = array_change_key_case($config, CASE_LOWER);
        $params = array('assoc' => false);
        $params['readonly'] = isset($config['readonly']) ? (bool)$config['readonly'] : false;

        // 确定属性和字段名之间的映射关系
        if (!empty($config['field_name']))
        {
            $field_name = $config['field_name'];
        }
        else
        {
            $field_name = isset($this->table_meta[$prop_name]) ? $this->table_meta[$prop_name]['name'] : $prop_name;
        }
        $this->props2fields[$prop_name] = $field_name;
        $this->fields2props[$field_name] = $prop_name;

        // 根据数据表的元信息确定属性是否是虚拟属性
        $meta_key_name = strtolower($field_name);
        if (!empty($this->table_meta[$meta_key_name]))
        {
            // 如果是非虚拟属性，则根据数据表的元信息设置属性的基本验证策略
            $params['virtual'] = false;
            $field_meta = $this->table_meta[$meta_key_name];
            $params['default_value'] = $field_meta['default'];
            $params['ptype'] = $field_meta['ptype'];
        }
        else
        {
            $params['virtual'] = true;
            $params['default_value'] = null;
            $params['ptype'] = 'varchar'; // TODO! 这个不能够在配置指定？是bug吗？
        }
        // 处理对象聚合
        foreach (self::$_assoc_types as $type)
        {
            if (empty($config[$type]))
            {
                continue;
            }
            $params['assoc'] = $type;
            $params['assoc_class'] = $config[$type];
            $params['assoc_params'] = $config;
            $this->associations[$prop_name] = $params;
        }

        // 设置属性信息
        if (!$params['virtual'] || $params['assoc'])
        {
            $this->default_props[$prop_name] = $params['default_value'];
        }
        $this->props[$prop_name] = $params;

        // 设置 getter 和 setter
        if (!empty($config['setter']))
        {
            $setter_params = !empty($config['setter_params']) ? (array)$config['setter_params'] : array();
            if (is_array($config['setter']))
            {
                $this->setPropSetter($prop_name, $config['setter'], $setter_params);
            }
            else
            {
                if (strpos($config['setter'], '::'))
                {
                    $config['setter'] = explode('::', $config['setter']);
                }
                $this->setPropSetter($prop_name, $config['setter'], $setter_params);
            }
        }
        if (!empty($config['getter']))
        {
            $getter_params = !empty($config['getter_params']) ? (array)$config['getter_params'] : array();
            if (is_array($config['getter']))
            {
                $this->setPropGetter($prop_name, $config['getter'], $getter_params);
            }
            else
            {
                if (strpos($config['getter'], '::'))
                {
                    $config['getter'] = explode('::', $config['getter']);
                }
                $this->setPropGetter($prop_name, $config['getter'], $getter_params);
            }
        }

        return $this;
    }

    /**
     * 添加一个对象关联
     *
     * $prop_name 参数指定使用 ActiveRecord 对象的什么属性来引用关联的对象。
     * 例如“文章”对象的 comments 属性引用了多个关联的“评论”对象。
     *
     * $assoc_type 指定了关联的类型，可以是 QDB::BELONGS_TO、QDB::HAS_MANY、QDB::HAS_ONE 或 QDB::MANY_TO_MANY。
     *
     * $config 指定了关联的属性，可用的属性有多项。
     *
     * @param string $prop_name
     * @param int $assoc_type
     * @param array $config
     *
     * @return QDB_ActiveRecord_Meta
     */
    function addAssoc($prop_name, $assoc_type, array $config)
    {
        switch ($assoc_type)
        {
        case QDB::HAS_ONE:
        case QDB::HAS_MANY:
            if (empty($config['target_key']))
            {
                $config['target_key'] = strtolower($this->class_name) . '_id';
            }
            break;
        case QDB::BELONGS_TO:
            if (empty($config['source_key']))
            {
                $config['source_key'] = strtolower($config['assoc_class']) . '_id';
            }
            break;
        case QDB::MANY_TO_MANY:
            if (empty($config['mid_source_key']))
            {
                $config['mid_source_key'] = strtolower($this->class_name) . '_id';
            }
            if (empty($config['mid_target_key']))
            {
                $config['mid_target_key'] = strtolower($config['assoc_class']) . '_id';
            }
        }

        $assoc = $config['assoc_params'];
        $assoc['mapping_name'] = $prop_name;
        $assoc['target_class'] = $config['assoc_class'];
        unset($assoc[$assoc_type]);

        $association = QDB_ActiveRecord_Association_Abstract::create($assoc_type, $assoc, $this);
        $association->registerCallbacks($assoc);
        $this->associations[$prop_name] = $association;

        if ($association->type == QDB::BELONGS_TO)
        {
            $association->init();
            $this->belongsto_props[$association->source_key] = $association;
        }
        return $association;
    }

    /**
     * 判断是否有指定关联
     *
     * @return boolean
     */
    function hasAssoc($prop_name)
    {
        return isset($this->associations[$prop_name]);
    }

    /**
     * 访问指定属性对应的关联
     *
     * @param string $prop_name
     *
     * @return QDB_ActiveRecord_Association_Abstract
     */
    function assoc($prop_name)
    {
        if (!isset($this->associations[$prop_name]))
        {
            throw new QDB_ActiveRecord_Association_NotDefinedException($this->class_name, $prop_name);
        }

        return $this->associations[$prop_name];
    }

    /**
     * 移除一个关联
     *
     * @param string $prop_name
     *
     * @return QDB_ActiveRecord_Meta
     */
    function removeAssoc($prop_name)
    {
        unset($this->props[$prop_name]);
        unset($this->associations[$prop_name]);
        return $this;
    }

    /**
     * 为指定属性添加一个验证规则
     *
     * @param string $prop_name
     * @param mixed $validation
     *
     * @return QDB_ActiveRecord_Meta
     */
    function addValidation($prop_name, $validation)
    {
        $p = array($prop_name => array($validation));
        $r = $this->_prepareValidationRules($p);
        if (!empty($r[$prop_name]['rules']))
        {
            foreach ($r[$prop_name]['rules'] as $rule)
            {
                $this->validations[$prop_name]['rules'][] = $rule;
            }
        }

        return $this;
    }

    /**
     * 取得指定属性的所有验证规则
     *
     * @param string $prop_name
     *
     * @return array
     */
    function propValidations($prop_name)
    {
        if (isset($this->validations[$prop_name]))
        {
            return $this->validations[$prop_name];
        }
        return array('policy' => self::$_validation_policy_options, 'rules' => array());
    }

    /**
     * 取得所有属性的所有验证规则
     *
     * @return array
     */
    function allValidations()
    {
        return $this->validations;
    }

    /**
     * 清除指定属性的所有验证规则
     *
     * @param string $prop_name
     *
     * @return QDB_ActiveRecord_Meta
     */
    function removePropValidations($prop_name)
    {
        if (isset($this->validations[$prop_name]))
        {
            unset($this->validations[$prop_name]);
        }
        return $this;
    }

    /**
     * 清除所有属性的所有验证规则
     *
     * @return QDB_ActiveRecord_Meta
     */
    function removeAllValidations()
    {
        $this->validations = array();
        return $this;
    }

    /**
     * 调用 ActiveRecord 继承类定义的自定义静态方法
     *
     * @param string $method_name
     * @param array $args
     *
     * @return mixed
     */
    function __call($method_name, array $args)
    {
        if (isset($this->static_methods[$method_name]))
        {
            $callback = $this->static_methods[$method_name];
            foreach ($args as $arg)
            {
                array_push($callback[1], $arg);
            }
            return call_user_func_array($callback[0], $callback[1]);
        }

        throw new QDB_ActiveRecord_Meta_Exception(__('未定义的方法 "%s".', $method_name));
    }

    /**
     * 准备验证策略
     *
     * @param array $policies 要解析的策略
     * @param array $ref 用于 include 参考的策略
     * @param boolean $set_policy 是否指定验证策略
     */
    protected function _prepareValidationRules($policies, array $ref = array(), $set_policy = true)
    {
        $validation = $this->validations;

        foreach ($policies as $prop_name => $policy)
        {
            if (!is_array($policy))
            {
                continue;
            }
            $validation[$prop_name] = array(
                'policy' => self::$_validation_policy_options,
                'rules' => array()
            );

            if (isset($this->props2fields[$prop_name]))
            {
                $fn = $this->props2fields[$prop_name];
                if (isset($this->table_meta[$fn]))
                {
                    $validation[$prop_name]['policy']['allow_null'] = ! $this->table_meta[$fn]['not_null'];
                }
            }

            if (!$set_policy)
            {
                unset($validation[$prop_name]['policy']);
            }

            foreach ($policy as $option => $rule)
            {
                if (isset($validation[$prop_name]['policy'][$option]))
                {
                    // 设置一个验证选项
                    $validation[$prop_name]['policy'][$option] = $rule;
                }
                elseif ($option === 'on_create' || $option === 'on_update')
                {
                    // 解析 on_create 和 on_update 规则
                    $rule = array($option => (array) $rule);
                    $ret = $this->_prepareValidationRules($rule, $validation[$prop_name]['rules'], false);
                    $validation[$prop_name][$option] = $ret[$option];
                }
                elseif ($option === 'include')
                {
                    // 解析规则包含
                    $include = Q::normalize($rule);
                    foreach ($include as $rule_name)
                    {
                        if (isset($ref[$rule_name]))
                        {
                            $validation[$prop_name]['rules'][$rule_name] = $ref[$rule_name];
                        }
                    }
                }
                elseif (is_array($rule))
                {
                    // $rule 是验证规则
                    if (is_string($option))
                    {
                        $rule_name = $option;
                    }
                    else
                    {
                        $rule_name = $rule[0];
                        if (is_array($rule_name))
                        {
                            $rule_name = $rule_name[count($rule_name) - 1];
                        }
                        if (isset($validation[$prop_name]['rules'][$rule_name]))
                        {
                            $rule_name .= '_' . ($option + 1);
                        }
                    }
                    $validation[$prop_name]['rules'][$rule_name] = $rule;
                }
                else
                {
                    // LC_MSG: 指定了无效的验证规则 "%s".
                    throw new QDB_ActiveRecord_Meta_Exception(__('指定了无效的验证规则 "%s".', $option . ' - ' . $rule));
                }
            }
        }

        return $validation;
    }

    /**
     * 根据数据表名称获得表数据入口对象
     *
     * @param string $table_name
     * @param array $table_config
     *
     * @return QDB_Table
     */
    protected function _tableByName($table_name, array $table_config = array())
    {
        $obj_id = 'activerecord_table_' . strtolower($table_name);
        if (Q::isRegistered($obj_id))
        {
            return Q::registry($obj_id);
        }
        else
        {
            $table_config['name'] = $table_name;
            $table = new QDB_Table($table_config);
            Q::register($table, $obj_id);
            return $table;
        }
    }

    /**
     * 根据类名称获得表数据入口对象
     *
     * @param string $table_class
     * @param array $table_config
     *
     * @return QDB_Table
     */
    protected function _tableByClass($table_class, array $table_config = array())
    {
        $obj_id = 'activerecord_table_' . strtolower($table_class);
        if (Q::isRegistered($obj_id))
        {
            return Q::registry($obj_id);
        }
        else
        {
            $table = new $table_class($table_config);
            Q::register($table, $obj_id);
            return $table;
        }
    }

    /**
     * 第一步初始化
     *
     * @param string $class
     */
    protected function _init1($class)
    {
        // 从指定类获得初步的定义信息
        Q::loadClass($class);
        $this->class_name = $class;
        $ref = (array) call_user_func(array($class, '__define'));

        /**
         * 检查是否是继承
         */
        if (!empty($ref['inherit']))
        {
            $this->inherit_base_class = $ref['inherit'];
            /**
             * 继承类的 __define() 方法只需要指定与父类不同的内容
             */
            $base_ref = (array) call_user_func(array($this->inherit_base_class, '__define'));
            $ref = array_merge_recursive($base_ref, $ref);
        }
        // 被继承的类
        $this->inherit_type_field = !empty($ref['inherit_type_field'])
                                    ? $ref['inherit_type_field']
                                    : null;

        // 设置表数据入口对象
        $table_config = !empty($ref['table_config']) ? (array)$ref['table_config'] : array();
        if (!empty($ref['table_class']))
        {
            $this->table = $this->_tableByClass($ref['table_class'], $table_config);
        }
        else
        {
            $this->table = $this->_tableByName($ref['table_name'], $table_config);
        }
        $this->table_meta = $this->table->columns();

        // 根据字段定义确定字段属性
        if (empty($ref['props']) || ! is_array($ref['props']))
        {
            $ref['props'] = array();
        }

        foreach ($ref['props'] as $prop_name => $config)
        {
            $this->addProp($prop_name, $config);
        }

        // 将没有指定的字段也设置为对象属性
        foreach ($this->table_meta as $prop_name => $field)
        {
            if (isset($this->props2fields[$prop_name])) continue;
            $this->addProp($prop_name, $field);
        }

        // 设置其他选项
        if (!empty($ref['create_reject']))
        {
            $this->create_reject = array_flip(Q::normalize($ref['create_reject']));
        }
        if (!empty($ref['update_reject']))
        {
            $this->update_reject = array_flip(Q::normalize($ref['update_reject']));
        }
        if (!empty($ref['create_autofill']) && is_array($ref['create_autofill']))
        {
            $this->create_autofill = $ref['create_autofill'];
        }
        if (!empty($ref['update_autofill']) && is_array($ref['update_autofill']))
        {
            $this->update_autofill = $ref['update_autofill'];
        }
        if (!empty($ref['attr_accessible']))
        {
            $this->attr_accessible = array_flip(Q::normalize($ref['attr_accessible']));
        }
        if (!empty($ref['attr_protected']))
        {
            $this->attr_protected = array_flip(Q::normalize($ref['attr_protected']));
        }

        // 准备验证规则
        if (empty($ref['validations']) || ! is_array($ref['validations']))
        {
            $ref['validations'] = array();
        }
        $this->validations = $this->_prepareValidationRules($ref['validations']);

        // 设置对象 ID 属性名
        $pk = $this->table->getPK();
        $this->idname = array();
        foreach ($this->table->getPK() as $pk)
        {
            $pn = $this->fields2props[$pk];
            $this->idname[$pn] = $pn;
        }
        $this->idname_count = count($this->idname);

        // 绑定行为插件
        if (isset($ref['behaviors']))
        {
            $config = isset($ref['behaviors_settings']) ? $ref['behaviors_settings'] : array();
            $this->bindBehaviors($ref['behaviors'], $config);
        }
    }

    /**
     * 第二步初始化
     *
     * 避免因为关联到自身造成循环引用。
     */
    protected function _init2()
    {
        foreach (array_keys($this->associations) as $prop_name)
        {
            $config = $this->associations[$prop_name];
            if (is_array($config))
            {
                $this->addAssoc($prop_name, $config['assoc'], $config);
            }
        }
    }

}


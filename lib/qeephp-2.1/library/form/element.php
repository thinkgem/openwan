<?php
// $Id: element.php 2535 2009-06-02 02:04:53Z jerry $

/**
 * 定义 QForm_Element 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: element.php 2535 2009-06-02 02:04:53Z jerry $
 * @package form
 */

/**
 * QForm_Element 类封装了表单中的一个值元素
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: element.php 2535 2009-06-02 02:04:53Z jerry $
 * @package form
 */
class QForm_Element
{
    /**
     * 属性值
     *
     * @var array
     */
    protected $_attrs = array();

    /**
     * 该元素的所有者
     *
     * @var QForm_Group
     */
    protected $_owner;

    /**
     * 未过滤的值
     *
     * @var mixed
     */
    protected $_unfiltered_value = null;

    /**
     * 过滤器链
     *
     * @var array
     */
    protected $_filters = array();

    /**
     * 验证规则
     *
     * @var array
     */
    protected $_validations = array();

    /**
     * 数据的验证结果
     *
     * @var boolean
     */
    protected $_is_valid = true;

    /**
     * 验证失败的信息
     *
     * @var string
     */
    protected $_error_msg = array();

    /**
     * 构造函数
     *
     * @param string $id 元素ID
     * @param array $attrs 属性
     * @param QForm_Group $owner
     */
    function __construct($id = null, array $attrs = null, QForm_Group $owner = null)
    {
        if (!is_array($attrs))
        {
            $attrs = array();
        }

        $this->_attrs = $attrs;
        if (!isset($attrs['id']))
        {
            $this->_attrs['id'] = $id;
        }
        if (!isset($attrs['name']))
        {
            $this->_attrs['name'] = $id;
        }
        if (!isset($attrs['_data_binding']))
        {
            $this->_attrs['_data_binding'] = true;
        }

        $this->_owner = $owner;
        if ($owner)
        {
            if (!isset($attrs['_nested_name']))
            {
                $this->_attrs['_nested_name'] = $owner->nestedName();
            }

            if(!empty($this->_attrs['_nested_name']))
            {
                $_lpos = strpos($this->_attrs['name'], '[');
                if($_lpos === false)
                {
                    $this->_attrs['name'] = "{$this->_attrs['_nested_name']}[{$this->_attrs['name']}]";
                }
                else
                {
                    $_prefix = substr($this->_attrs['name'], 0, $_lpos);
                    $_suffix = substr($this->_attrs['name'], $_lpos);
                    $this->_attrs['name'] = "{$this->_attrs['_nested_name']}[{$_prefix}]{$_suffix}";
                }
            }
        }
        else
        {
            $this->_attrs['_nested_name'] = "";
        }
    }

    /**
     * 魔法方法，以便通过对象属性直接访问元素的属性值
     *
     * @code php
     * echo $element->title;
     * @endcode
     *
     * @param string $attr
     *
     * @return mixed
     */
    function __get($attr)
    {
        return $this->get($attr);
    }

    /**
     * 魔法方法，以便通过指定对象属性的方式来修改元素的属性值
     *
     * @param string $attr 属性名
     * @param mixed $value 属性值
     */
    function __set($attr, $value)
    {
        $this->_attrs[$attr] = $value;
    }

    /**
     * 获得属性值，如果属性不存在返回 $default 参数指定的默认值
     *
     * @param string $attr 属性名
     * @param mixed $default 默认值
     *
     * @return mixed 属性值
     */
    function get($attr, $default = null)
    {
        if ($attr == 'validations')
        {
            return $this->_validations;
        } else if ($attr == 'filters') {
            return $this->_filters;
        }

        return isset($this->_attrs[$attr]) ? $this->_attrs[$attr] : $default;
    }

    /**
     * 修改属性值
     *
     * @param string $attr 属性名
     * @param mixed $value 属性值
     *
     * @return QForm_Element
     */
    function set($attr, $value)
    {
        $this->_attrs[$attr] = $value;
        return $this;
    }

    /**
     * 确定数据绑定状态
     *
     * @return boolean
     */
    function dataBinding()
    {
        return $this->_data_binding;
    }

    /**
     * 修改数据绑定状态
     *
     * @param boolean $enabled
     *
     * @return QForm_Element
     */
    function enableDataBinding($enabled = true)
    {
        $this->_data_binding = (bool)$enabled;
    }

    /**
     * 返回群组的嵌套名
     *
     * @return string
     */
    function nestedName()
    {
        return $this->_attrs['_nested_name'];
    }

    /**
     * 设置嵌套名
     *
     * @param string $name
     *
     * @return QForm_Element
     */
    function changeNestedName($name)
    {
        $this->_nested_name = $name;
        return $this;
    }

    /**
     * 返回元素的所有者
     *
     * @return QForm_Group
     */
    function owner()
    {
        return $this->_owner;
    }

    /**
     * 返回所有不是以“_”开头的属性的值
     *
     * @return array
     */
    function attrs()
    {
        $ret = array();
        foreach ($this->_attrs as $attr => $value)
        {
            if ($attr{0} == '_') continue;
            $ret[$attr] = $value;
        }
        return $ret;
    }

    /**
     * 返回所有属性的值
     *
     * @return array
     */
    function allAttrs()
    {
        return $this->_attrs;
    }

    /**
     * 调用该元素所属群组的 add() 方法，以便在连贯接口中连续添加元素
     *
     * @param enum $type
     * @param string $id
     * @param array $attrs
     *
     * @return QForm_Element
     */
    function add($type, $id, array $attrs = null)
    {
        if (!is_null($this->_owner))
        {
            return $this->_owner->add($type, $id, $attrs);
        }
        // LC_MSG: 当前元素 "%s" 不属于任何群组，因此无法完成 add() 操作.
        throw new QForm_Exception(
            __('当前元素 "%s" 不属于任何群组，因此无法完成 add() 操作.', $this->id)
        );
    }

    /**
     * 指示该元素是否是一个群组
     *
     * @return boolean
     */
    function isGroup()
    {
        return false;
    }

    /**
     * 添加过滤器
     *
     * 多个过滤器可以使用以“,”分割的字符串来表示：
     *
     * @code php
     * $element->addFilters('trim, strtolower');
     * @endcode
     *
     * 或者以包含多个过滤器名的数组表示：
     *
     * @code php
     * $element->addFilters(array('trim', 'strtolower'));
     * @endcode
     *
     * 如果是需要附加参数的过滤器 ，则必须采用下面的格式：
     *
     * @code php
     * $element->addFilters(array(
     *     array('substr', 0, 5),
     *     'strtolower',
     * ));
     * @endcode
     *
     * @param string|array $filters 要添加的过滤器
     *
     * @return QForm_Element 返回元素对象本身，实现连贯接口
     */
    function addFilters($filters)
    {
        if (!is_array($filters)) $filters = Q::normalize($filters);

        foreach ($filters as $filter)
        {
            if (!is_array($filter)) $filter = array($filter);
            $this->_filters[] = $filter;
        }
        return $this;
    }

    /**
     * 添加验证规则
     *
     * 每一个验证规则是一个数组，可以采用两种方式添加：
     *
     * @code php
     * $element->addValidations('max_length', 5, '不能超过5个字符');
     * // 或者
     * $element->addValidations(array('max_length', 5, '不能超过5个字符'));
     * @endcode
     *
     * 如果要添加一个 callback 方法作为验证规则，必须这样写：
     *
     * @code php
     * $element->addValidations(array($obj, 'method_name'), $args, 'error_message'));
     * @endcode
     *
     * 如果要一次性添加多个验证规则，需要使用二维数组：
     *
     * @code php
     * $element->addValidations(array(
     *     array('min', 3, '不能小于3'),
     *     array('max', 9, '不能大于9'),
     * ));
     * @endcode
     *
     * 如果要将 ActiveRecord 模型的验证规则添加给元素，可以使用：
     *
     * @code php
     * // 将 Post 模型中与该表单元素同名属性的验证规则添加到表单元素中
     * $element->addValidations(Post::meta());
     * // 或者将指定属性的验证规则添加到表单元素中
     * $element->addValidations(Post::meta(), 'propname');
     * @endcode
     *
     * @param mixed $validations 要添加的验证规则
     *
     * @return QForm_Element 返回元素对象本身，实现连贯接口
     */
    function addValidations($validations)
    {
        $args = func_get_args();
        if ($validations instanceof QDB_ActiveRecord_Meta)
        {
            if (isset($args[1]))
            {
                $validations = $validations->propValidations($args[1]);
            }
            else
            {
                $validations = $validations->propValidations($this->id);
            }
            foreach ($validations['rules'] as $v)
            {
                $this->_validations[] = $v;
            }
        }
        elseif (!is_array($validations))
        {
            $this->_validations[] = $args;
        }
        else
        {
            // 如果没有提供第二个参数，并且 $validations 的第一个元素是数组，则视为二维数组
            if (!isset($args[1]) && is_array(reset($validations)))
            {
                foreach ($validations as $v)
                {
                    $this->_validations[] = $v;
                }
            }
            else
            {
                // 否则视为一个验证规则
                $this->_validations[] = $args;
            }
        }

        return $this;
    }

    /**
     * 清除该元素的所有验证规则
     *
     * @return QForm_Element
     */
    function cleanValidations()
    {
        $this->_validations = array();
        return $this;
    }

    /**
     * 导入数据，但不进行过滤和验证
     *
     * @param mixed $data
     *
     * @return QForm_Element
     */
    function import($data)
    {
        if ($this->_data_binding)
        {
            $this->_attrs['value'] = $this->_unfiltered_value = $data;
        }
        return $this;
    }

    /**
     * 导入数据后进行过滤，并返回验证结果
     *
     * 通常调用 QForm 对象的 validate() 方法一次性导入整个表单的数据。
     *
     * @param mixed $data 要导入并验证的数据
     * @param array $failed 保存验证失败的信息
     *
     * @return boolean 验证结果
     */
    function validate($data, & $failed = null)
    {
        if (!$this->_data_binding) return false;

        $this->_unfiltered_value = $data;
        $data = QFilter::filterBatch($data, $this->_filters);
        $this->_attrs['value'] = $data;

        if (!empty($this->_validations))
        {
            $failed = null;
            $this->_is_valid = (bool)QValidator::validateBatch($data, $this->_validations, QValidator::CHECK_ALL, $failed);
            if (!$this->_is_valid)
            {
                $this->_error_msg = array();
                foreach ($failed as $v)
                {
                    $this->_error_msg[] = array_pop($v);
                }
            }
            $failed = $this->_error_msg;
        }
        else
        {
            $this->_error_msg = array();
            $failed = null;
            $this->_is_valid = true;
        }

        return $this->_is_valid;
    }

    /**
     * 指示表单元素的值是否有效
     *
     * @return boolean
     */
    function isValid()
    {
        return (bool)$this->_is_valid;
    }

    /**
     * 指示表单元素的值是否无效
     *
     * @return boolean
     */
    function isInvalid()
    {
        return !((bool)$this->_is_valid);
    }

    /**
     * 设置一个元素为无效状态，以及错误消息
     *
     * 设置一个元素为无效状态后，整个表单的状态都会无效。
     * 为了能够通过 errorMsg() 取得导致表单元素无效的错误信息，可以指定 $msg 参数。
     *
     * @code php
     * $element->invalidate('order 的值不能小于 0');
     * @endcode
     *
     * @param string|array $msg 错误消息，如果有多个可以用数组
     *
     * @return QForm_Element 返回元素对象本身，实现连贯接口
     */
    function invalidate($msg = null)
    {
        $this->_is_valid = false;
        if (!is_array($msg)) $msg = array($msg);
        $this->_error_msg = $msg;
        return $this;
    }

    /**
     * 返回验证错误信息
     *
     * @return array 包含该元素所有错误消息的数组
     */
    function errorMsg()
    {
        return $this->_error_msg;
    }

    /**
     * 获得表单元素的值
     *
     * @return mixed 表单元素的值
     */
    function value()
    {
        return $this->value;
    }

    /**
     * 返回未过滤的值
     *
     * @return mixed 表单元素未过滤的值
     */
    function unfilteredValue()
    {
        return $this->_unfiltered_value;
    }
}


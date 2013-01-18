<?php
// $Id: group.php 2514 2009-05-22 08:22:48Z jerry $

/**
 * 定义 QForm_Group 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: group.php 2514 2009-05-22 08:22:48Z jerry $
 * @package form
 */

/**
 * 类 QForm_Group 是容纳多个元素或群组的集合
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: group.php 2514 2009-05-22 08:22:48Z jerry $
 * @package form
 */
class QForm_Group extends QForm_Element implements ArrayAccess
{
    /**
     * 聚合的元素
     *
     * @var QColl
     */
    protected $_elements;

    /**
     * 构造函数
     *
     * @param string $id 表单 ID
     * @param array $attrs 属性
     */
    function __construct($id = null, array $attrs = null, QForm_Group $owner = null)
    {
        parent::__construct($id, $attrs, $owner);
        $this->_elements = new QColl('QForm_Element');
    }

    /**
     * 设置嵌套名
     *
     * @param string $name
     *
     * @return QForm_Element_Abstract
     */
    function changeNestedName($name)
    {
        $this->_nested_name = $name;
        foreach ($this->_elements as $element)
        {
            $element->changeNestedName($name);
        }
        return $this;
    }

    /**
     * 添加一个元素，并返回该元素对象
     *
     * @code php
     * $form->add(QForm::ELEMENT, 'title', array('_ui' => 'textbox', 'size' => 40));
     * @endcode
     *
     * $type 参数只能是 QForm::ELEMENT 或 QForm::GROUP 两者之一。
     *
     * @param enum $type 要添加的元素类型
     * @param string $id 元素 ID
     * @param array $attrs 元素属性
     *
     * @return QForm_Element
     */
    function add($type, $id, array $attrs = null)
    {
        if ($type == QForm::ELEMENT)
        {
            $item = new QForm_Element($id, $attrs, $this);
        }
        elseif ($type == QForm::GROUP)
        {
            $item = new QForm_Group($id, null, $this);
            if (!empty($attrs))
            {
                $callback = array($item, 'add');
                foreach ($attrs as $element)
                {
                    call_user_func_array($callback, $element);
                }
            }
        }
        else
        {
            throw new QForm_Exception(__('Invalid type "%s".', $type));
        }
        $this->_elements[$id] = $item;
        return $item;
    }

    /**
     * 从群组中删除指定的元素
     *
     * @param string $id
     *
     * @return QForm_Group
     */
    function remove($id)
    {
        unset($this->_elements[$id]);
        return $this;
    }

    /**
     * 从配置批量添加元素
     *
     * 具体用法参考开发者手册关于表单的章节。
     *
     * @param array $config
     *
     * @return QForm_Group
     */
    function loadFromConfig(array $config)
    {
        foreach ($config as $id => $attrs)
        {
            if (!is_array($attrs))
            {
                $attrs = array();
            }
            if(!isset($this->_attrs['qform_group_id']))
            {
                $this->_attrs['qform_group_id'] = "";
            }

            if (isset($attrs['_elements']))
            {
                if(!isset($attrs['qform_group_id']))
                {
                    $attrs['qform_group_id'] = $id;
                }
                $elements = (array)$attrs['_elements'];
                unset($attrs['_elements']);
                $group = new QForm_Group($id, $attrs, $this);
                if (!empty($elements))
                {
                    $group->loadFromConfig($elements);
                }
                $this->_elements[$id] = $group;
            }
            else
            {
                if (isset($attrs['_filters']))
                {
                    $filters = $attrs['_filters'];
                    unset($attrs['_filters']);
                }
                else
                {
                    $filters = null;
                }
                if (isset($attrs['_validations']))
                {
                    $validations = $attrs['_validations'];
                    unset($attrs['_validations']);
                }
                else
                {
                    $validations = null;
                }

                $attrs['qform_group_id'] = $this->_attrs['qform_group_id'];

                $element = new QForm_Element($id, $attrs, $this);

                if (!empty($filters))
                {
                    $element->addFilters($filters);
                }
                if (!empty($validations))
                {
                    $element->addValidations($validations);
                }

                if(isset($attrs['value']))
                {
                    $element->_unfiltered_value = $attrs['value'];
                }

                $this->_elements[$id] = $element;
            }
        }

        return $this;
    }

    /**
     * 为群组的元素添加验证规则
     *
     * 验证规则可以是一个多维数组：
     *
     * @code php
     * $group->addValidations(array(
     *      // 为该群组的 title 元素添加验证规则
     *      'title' => array('max_length', 5, '不能超过5个字符'),
     *
     *      // 为 body 元素添加验证规则
     *      'body'  => array(
     *          array('not_empty', '不能为空'),
     *          array('min_length', 10, '不能少于10个字符'),
     *      ),
     * ));
     * @endcode
     *
     * 如果要将 ActiveRecord 模型的验证规则添加给元素，可以使用：
     *
     * @code php
     * // 将 Post 模型中与该表单元素同名属性的验证规则添加到表单元素中
     * $group->addValidations(Post::meta());
     * // 或者只从指定属性取得验证规则
     * $group->addValidations(Post::meta(), 'title, body');
     * // 或者将指定属性的规则添加到指定元素
     * // 将 Post 模型 name 属性的验证规则添加到群组中的 title 元素
     * $group->addValidations(Post::meta(), array('name' => 'title', 'body'));
     * @endcode
     *
     * @param mixed $source 要添加的验证规则
     *
     * @return QForm_Group 返回群组对象本身，实现连贯接口
     */
    function addValidations($source)
    {
        if ($source instanceof QDB_ActiveRecord_Meta)
        {
            $validations = $source->allValidations();
            foreach ($validations as $id => $source)
            {
                if ($this->existsElement($id))
                {
                    $this->element($id)->addValidations($source['rules']);
                }
            }
        }
        elseif (is_array($source))
        {
            foreach ($source as $id => $validations)
            {
                if ($this->existsElement($id))
                {
                    $this->element($id)->addValidations($validations);
                }
            }
        }
        else
        {
            throw new QForm_Exception(__('Typemismatch, expected array, actual is "%s".', gettype($source)));
        }

        return $this;
    }

    /**
     * 清除该组所有元素的验证规则
     *
     * @return QForm_Group
     */
    function cleanValidations()
    {
        foreach ($this->_elements as $element)
        {
            $element->cleanValidations();
        }
        return $this;
    }

    /**
     * 返回指定 ID 的子元素
     *
     * @param string $id
     *
     * @return QForm_Element
     */
    function element($id)
    {
        if (strpos($id, '/'))
        {
            $arr = explode('/', $id);
            $element = $this;
            foreach ($arr as $id)
            {
                $element = $element->element($id);
            }
            return $element;
        }

        return $this->_elements[$id];
    }

    /**
     * 检查指定的元素是否存在
     *
     * @param string $id
     *
     * @return boolean
     */
    function existsElement($id)
    {
        if (strpos($id, '/'))
        {
            $arr = explode('/', $id);
            $element = $this;
            foreach ($arr as $id)
            {
                if (!$element->existsElement($id)) return false;
                $element = $element->element($id);
            }
            return true;
        }

        return isset($this->_elements[$id]);
    }

    /**
     * 返回包含所有元素的集合
     *
     * @return QColl
     */
    function elements()
    {
        return $this->_elements;
    }

    /**
     * 指示当前元素是一个组
     *
     * @return boolean
     */
    function isGroup()
    {
        return true;
    }

    /**
     * 导入数据，但不进行过滤和验证
     *
     * @param array $data
     *
     * @return QForm_Group
     */
    function import($data)
    {
        return $this->_import($data);
    }

    /**
     * 确认群组所有元素的有效性
     *
     * @return boolean
     */
    function isValid()
    {
        $is_valid = true;
        foreach ($this->elements() as $element)
        {
            if (!$element->dataBinding()) continue;
            $is_valid &= $element->isValid();
        }
        return (bool)$is_valid;
    }

    /**
     * 导入数据并验证，返回验证结果
     *
     * @param array $data 要导入的数据
     * @param array $failed 保存验证失败的信息
     *
     * @return boolean
     * @access private
     */
    function validate($data, & $failed = null)
    {
        return $this->_validate($data, $failed);
    }

    /**
     * 指示群组的数据为无效状态
     *
     * @param mixed $error
     *
     * @return QForm_Group
     */
    function invalidate($error = null)
    {
        if (empty($error)) return $this;

        if ($error instanceof QValidator_ValidateFailedException)
        {
            $errors = $error->validate_errors;
        }
        elseif (!is_array($error))
        {
            $keys = Q::normalize($error);
            $errors = array();
            foreach ($keys as $key) $errors[$key] = '';
        }
        else
        {
            $errors = $error;
        }

        foreach ($errors as $id => $msg)
        {
            if ($this->existsElement($id))
            {
                $this->element($id)->invalidate($msg);
            }
        }
        return $this;
    }

    /**
     * 返回包含群组所有元素值的数组
     *
     * @return array
     */
    function values()
    {
        $ret = array();
        $this->_values($ret);
        return $ret;
    }

    /**
     * 获得表单群组中所有元素的值
     *
     * @return array 群组所有元素的值
     */
    function value()
    {
        return $this->values();
    }

    /**
     * 返回包含群组所有元素未过滤值得数组
     *
     * @return array
     */
    function unfilteredValues()
    {
        $ret = array();
        $t = null;
        $this->_values($ret, $t, true);
        return $ret;
    }

    /**
     * 返回包含群组所有元素未过滤值得数组
     *
     * @return array
     */
    function unfilteredValue()
    {
        return $this->unfilteredValues();
    }

    /**
     * ArrayAccess 接口实现：检查指定键名是否存在
     *
     * @param string $id
     *
     * @return boolean
     * @access private
     */
    function offsetExists($id)
    {
        return isset($this->_elements[$id]);
    }

    /**
     * ArrayAccess 接口实现：取得指定键名的元素
     *
     * @param string $id
     *
     * @return QForm_Element
     * @access private
     */
    function offsetGet($id)
    {
        return $this->_elements[$id];
    }

    /**
     * ArrayAccess 接口实现：设置指定键名的元素
     *
     * @param string $id
     * @param QForm_Element $element
     *
     * @access private
     */
    function offsetSet($id, $element)
    {
        $this->_elements[$id] = $element;
    }

    /**
     * ArrayAccess 接口实现：删除指定键名的元素
     *
     * @param string $id
     *
     * @access private
     */
    function offsetUnset($id)
    {
        unset($this->_elements[$id]);
    }

    /**
     * 导入数据，但不进行过滤和验证
     *
     * @param array $data 要导入的数据
     * @param array $parent_data
     *
     * @return boolean
     * @access private
     */
    protected function _import($data, $parent_data = null)
    {
        if (!is_array($data) && !($data instanceof ArrayAccess))
        {
            // LC_MSG: $data 参数应该是一个数组或一个实现了 ArrayAccess 接口的对象.
            throw new QForm_Exception(__('$data 参数应该是一个数组或一个实现了 ArrayAccess 接口的对象.'));
        }

        foreach ($this->_elements as $id => $element)
        {
            /* @var $element QForm_Element */
            if (!$element->dataBinding()) continue;

            $nested_name = $element->nestedName();
            $id = $element->id;

            if ($element->isGroup())
            {
                // 群组
                if ($nested_name)
                {
                    if (isset($data[$nested_name])
                            && (
                                  is_array($data[$nested_name])
                                  || $data[$nested_name] instanceof ArrayAccess
                                )
                        )
                    {
                        $element->_import($data[$nested_name], $data);
                    }
                    else
                    {
                        $element->_import(array(), $data);
                    }
                }
                else
                {
                    $element->_import($data, $parent_data);
                }
            }
            else
            {
                // 值元素
                $id = $element->id;
                if ($nested_name || is_null($parent_data))
                {
                    if($data instanceof ArrayAccess)
                    {
                        try { $value = $data[$id]; }
                        catch(QException $ex) { $value = null; }
                    }
                    else
                    {
                        $value = isset($data[$id]) ? $data[$id] : null;
                    }
                }
                else
                {
                    if($data instanceof ArrayAccess)
                    {
                        try { $value = $parent_data[$id]; }
                        catch(QException $ex) { $value = null; }
                    }
                    else
                    {
                        $value = isset($parent_data[$id]) ? $parent_data[$id] : null;
                    }
                }
                $element->import($value);
            }
        }
    }

    /**
     * 导入数据并验证，返回验证结果
     *
     * @param array $data 要导入的数据
     * @param array $failed 保存验证失败的信息
     * @param array $parent_data
     *
     * @return boolean
     * @access private
     */
    protected function _validate($data, & $failed = null, array $parent_data = null)
    {
        if (!is_array($data) && !($data instanceof ArrayAccess))
        {
            // LC_MSG: $data 参数应该是一个数组或一个实现了 ArrayAccess 接口的对象.
            throw new QForm_Exception(__('$data 参数应该是一个数组或一个实现了 ArrayAccess 接口的对象.'));
        }

        $failed = array();
        $is_valid = true;

        foreach ($this->_elements as $id => $element)
        {
            /* @var $element QForm_Element */
            if (!$element->dataBinding()) continue;

            $nested_name = $element->nestedName();
            $id = $element->id;

            if ($element->isGroup())
            {
                // 群组
                $failed[$id] = array();

                if ($nested_name)
                {
                    if (isset($data[$nested_name]) && is_array($data[$nested_name]))
                    {
                        $ret = $element->_validate($data[$nested_name], $failed[$id], $data);
                    }
                    else
                    {
                        $ret = $element->_validate(array(), $failed[$nested_name], $data);
                    }
                    if ($ret)
                    {
                        unset($failed[$nested_name]);
                    }
                }
                else
                {
                    $ret = $element->_validate($data, $failed[$id], $parent_data);
                }
            }
            else
            {
                // 值元素
                $id = $element->id;
                if ($nested_name || is_null($parent_data))
                {
                    $value = isset($data[$id]) ? $data[$id] : null;
                }
                else
                {
                    $value = isset($parent_data[$id]) ? $parent_data[$id] : null;
                }
                $failed[$id] = array();
                $ret = $element->validate($value, $failed[$id]);

                if ($ret)
                {
                    unset($failed[$id]);
                }
            }

            $is_valid &= $ret;
        }

        return (bool)$is_valid;
    }

    /**
     * 提取群组所有元素值的数组
     */
    protected function _values(& $ret, & $parent_data = null, $return_unfiltered_value = false)
    {
        foreach ($this->_elements as $id => $element)
        {
            /* @var $element QForm_Element */
            if (!$element->dataBinding()) continue;

            if ($element->isGroup())
            {
                $ret[$id] = array();
                $element->_values($ret[$id], $ret, $return_unfiltered_value);
                if (empty($ret[$id]))
                {
                    unset($ret[$id]);
                }
            }
            else
            {
                if ($return_unfiltered_value)
                {
                    if ($element->nestedName() || is_null($parent_data))
                    {
                        $ret[$id] = $element->unfilteredValue();
                    }
                    else
                    {
                        $parent_data[$id] = $element->unfilteredValue();
                    }
                }
                else
                {
                    if ($element->nestedName() || is_null($parent_data))
                    {
                        $ret[$id] = $element->value();
                    }
                    else
                    {
                        $parent_data[$id] = $element->value();
                    }
                }
            }
        }
    }
}


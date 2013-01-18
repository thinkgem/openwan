<?php
// $Id: form.php 2401 2009-04-07 03:50:08Z dualface $

/**
 * 定义 QForm 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: form.php 2401 2009-04-07 03:50:08Z dualface $
 * @package form
 */

/**
 * 类 QForm 封装了表单的数据和行为
 *
 * 有关 QForm 类的详细使用，请参考开发者手册的相关章节。
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: form.php 2401 2009-04-07 03:50:08Z dualface $
 * @package form
 */
class QForm extends QForm_Group
{
    /**
     * 表单方法类型
     */
    const POST    = 'post';
    const GET     = 'get';
    const PUT     = 'put';
    const DELETE  = 'delete';

    /**
     * 表单元素类型
     */
    // 值元素
    const ELEMENT = 'element';
    // 群组
    const GROUP   = 'group';

    /**
     * 表单编码类型
     */
    const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART  = 'multipart/form-data';

    /**
     * 构造函数
     *
     * 表单对象构造后，会调用 _after_created() 方法。
     * QForm 继承类可以在 _after_created() 方法中做进一步的设置和初始化。
     *
     * @param string $id 表单 ID
     * @param string $action 表单提交的目的地 URL
     * @param string $method 表单提交方法
     * @param arary $attrs 附加的属性
     */
    function __construct($id = 'form1', $action = null, $method = self::POST, array $attrs = null)
    {
        parent::__construct($id, $attrs);
        $this->action = $action;
        $this->method = $method;
        $this->_nested_name = '';
        $this->enctype = self::ENCTYPE_URLENCODED;
        $this->_after_created();
    }

    /**
     * 从配置载入表单设置和元素
     *
     * @code php
     * $form = QForm();
     * $config = Helper_YAML::loadCached('form_config.yaml');
     * $form->loadFromConfig($config);
     * @endcode
     *
     * @param array $config 包含表单配置的数组
     *
     * @return QForm 返回表单对象本身，实现连贯接口
     */
    function loadFromConfig(array $config)
    {
        if (isset($config['~form']))
        {
            foreach ((array)$config['~form'] as $attr => $value)
            {
                $this->_attrs[$attr] = $value;
            }
        }
        unset($config['~form']);

        parent::loadFromConfig($config);
        return $this;
    }

    /**
     * 从一个 YAML 文件载入表单设置和元素
     *
     * @param string $filename 要载入的配置文件
     * @param bollean $cached 是否缓存配置文件
     *
     * @return QForm 返回表单对象本身，实现连贯接口
     */
    function loadFromConfigFile($filename, $cached = true)
    {
        if ($cached)
        {
            $config = Helper_YAML::loadCached($filename);
        }
        else
        {
            $config = Helper_YAML::load($filename);
        }
        return $this->loadFromConfig($config);
    }

    /**
     * 导入数据并验证，返回验证结果
     *
     * 通过 validate() 方法，数据将被导入表单对象。
     * 并在导入时进行过滤和验证，最后返回验证结果。
     *
     * @code php
     * if ($form->validate($_POST))
     * {
     *     ... 验证通过
     * }
     * @endcode
     *
     * 验证后的数据使用 values() 方法可以取得。
     * 而未过滤的原始数据使用 unfilteredValues() 方法可以取得。
     *
     * @param mixed $data 要导入的数据，可以是数组或者实现了 ArrayAccess 接口的对象，例如 QColl
     * @param array $failed 如果需要确定哪些数据没有验证通过，可以提供 $failed 参数。
     *                      验证结果后该参数将包含所有没有通过验证的表单元素的名字。
     *
     * @return boolean 验证结果
     */
    function validate($data, & $failed = null)
    {
        $this->_before_validate($data);
        parent::validate($data, $failed);
        $this->_after_validate($data);

        $is_valid = $this->isValid();
        if ($is_valid)
        {
            $this->_after_validate_successed();
        }
        else
        {
            $this->_after_validate_failed();
        }
        return $is_valid;
    }

    /**
     * 表单对象构造后调用的事件方法
     */
    protected function _after_created()
    {
    }

    /**
     * 表单对象验证之前调用的事件方法
     *
     * @param mixed $data 要验证的数据
     */
    protected function _before_validate(& $data)
    {
    }

    /**
     * 表单对象验证之后调用的事件方法
     *
     * @param mixed $data 要验证的数据
     */
    protected function _after_validate(& $data)
    {
    }

    /**
     * 当表单验证失败时调用的事件方法
     */
    protected function _after_validate_failed()
    {
    }

    /**
     * 当表单验证成功时调用的事件方法
     */
    protected function _after_validate_successed()
    {
    }
}


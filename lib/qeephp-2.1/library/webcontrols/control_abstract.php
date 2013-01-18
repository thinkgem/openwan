<?php
// $Id: control_abstract.php 2649 2009-08-17 01:30:55Z jerry $

/**
 * 定义 QUI_Control_Abstract 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: control_abstract.php 2649 2009-08-17 01:30:55Z jerry $
 * @package webcontrols
 */

/**
 * QUI_Control_Abstract 是用户界面控件的基础类
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: control_abstract.php 2649 2009-08-17 01:30:55Z jerry $
 * @package webcontrols
 */
abstract class QUI_Control_Abstract
{
    /**
     * 运行时上下文
     *
     * @var QContext
     */
    protected $_context;

    /**
     * 渲染控件视图时要使用的视图变量
     *
     * @var array
     */
    protected $_view = array();

    /**
     * 渲染控件视图时要使用的类
     *
     * @var string
     */
    protected $_render_class = 'QView_Render_PHP_Parser';

    /**
     * 视图渲染对象
     *
     * @var QView_Render_PHP
     */
    protected $_render;

    /**
     * 控件的 ID
     *
     * @var string
     */
    protected $_id;

    /**
     * 控件的属性
     *
     * @var array
     */
    protected $_attrs;

    /**
     * 构造函数
     *
     * @param string $id
     * @param array $attrs
     *
     */
    function __construct($id, $attrs = array())
    {
        $this->_context = QContext::instance();
        $this->_id = $id;
        if(! isset($attrs['name']) || is_null($attrs['name']))
        {
            $attrs['name'] = $id;
        }
        $this->_attrs = $attrs;
    }

    /**
     * 返回或修改控件的 ID
     *
     * @param string $id 控件 ID
     *
     * @return string|QUI_Control_Abstract
     */
    function id($id = null)
    {
        if (! is_null($id))
        {
            $this->_id = $id;
            return $this;
        }

        return $this->_id;
    }

    /**
     * 返回或修改控件的 NAME
     *
     * @param string $name 控件 NAME
     *
     * @return string|QUI_Control_Abstract
     */
    function name($name = null)
    {
        if (! is_null($name))
        {
            $this->_attrs['name'] = $name;
            return $this;
        }
        return $this->_attrs['name'];
    }

    function get($attr, $default = null)
    {
        return isset($this->_attrs[$attr]) ? $this->_attrs[$attr] : $default;
    }

    function set($attr, $value = null)
    {
        if (is_array($attr))
        {
            $this->_attrs = array_merge($this->_attrs, $attr);
        }
        else
        {
            $this->_attrs[$attr] = $value;
        }
        return $this;
    }

    /**
     * 返回控件属性值
     *
     * @param string $attr
     *
     * @return mixed
     */
    function __get($attr)
    {
        return isset($this->_attrs[$attr]) ? $this->_attrs[$attr] : null;
    }

    /**
     * 设置控件属性
     *
     * @param string $attr
     * @param mixed $value
     */
    function __set($attr, $value)
    {
        $this->_attrs[$attr] = $value;
    }

    function __isset($attr)
    {
        return isset($this->_attrs[$attr]);
    }

    function __unset($attr)
    {
        unset($this->_attrs[$attr]);
    }

    /**
     * 返回或设置控件的所有属性
     *
     * @param array $attrs 控件属性
     *
     * @return array|QUI_Control_Abstract
     */
    function attrs(array $attrs = null)
    {
        if (is_array($attrs))
        {
            $this->_attrs = $attrs;
            return $this;
        }
        return $this->_attrs;
    }

    /**
     * 显示一个控件
     *
     * @param QView_Render_PHP $render
     *
     * @return mixed
     */
    function display($render = null)
    {
        if ($render instanceof QView_Render_PHP_Parser)
        {
            $this->_render = $render;
        }
        $this->_before_render();
        echo $this->render();
    }

    function __toString()
    {
        $this->_before_render();
        return $this->render();
    }

    /**
     * 渲染一个控件，并返回渲染结果
     *
     * @return string
     */
    abstract function render();

    /**
     * 渲染指定的视图文件
     *
     * 渲染时，视图要使用的数据保存在控件的 $_view 属性中。
     *
     * @param string $filename
     * @param array $more_vars
     *
     * @return string
     */
    protected function _fetchView($filename, array $more_vars = null)
    {
        $vars = $this->_view;

        /**
         * TODO! 全局变量应该放到 控件抽象类 _before_render() 中
        */
        $vars['_ctx']         = $this->_context;
        $vars['_CTL_ID']      = $this->id();
        $vars['_BASE_DIR']    = $this->_context->baseDir();
        $vars['_BASE_URI']    = $this->_context->baseUri();
        $vars['_REQUEST_URI'] = $this->_context->requestUri();
        //

        if (is_array($more_vars))
        {
            $vars = array_merge($vars, $more_vars);
        }

        if(strpos($filename, 'view:') === 0)
        {
            $filename = Q::ini('app_config/APP_DIR') . DS
                . 'view' . DS . ltrim(substr($filename, 5), '/\\');
        }
        elseif($filename{0} != DS && strpos($filename, ':') === false)
        {
            $_class_name = strtolower(get_class($this));
            $_sep = explode('_', $_class_name);
            array_pop($_sep);
            $filename = Q::ini('app_config/APP_DIR') . DS
                . implode($_sep, DS)
                . DS . ltrim($filename, '/\\');
        }

        if (is_null($this->_render))
        {
            $this->_render = new $this->_render_class(dirname($filename));
        }
        else
        {
            // TODO! 假如共享视图解析器实例，需在此做必要处理
        }

        $this->_render->assign($vars);

        $extname = pathinfo($filename, PATHINFO_EXTENSION);
        $pextname = $this->_render->extname();
        if (empty($extname) || ($extname != $pextname && !empty($pextname)))
        {
            $filename .= '.' . (($pextname) ? $pextname : 'php');
        }

        return $this->_render->parse($filename);
    }

    /**
     * 渲染之前调用
     *
     * 继承类可以覆盖此方法。
     */
    protected function _before_render()
    {
    }

    protected function _extract($attr, $default = null)
    {
        $value = $this->get($attr, $default);
        unset($this->_attrs[$attr]);
        return $value;
    }

    /**
     * 根据 ID 和 NAME 属性返回字符串
     *
     * @return string
     */
    protected function _printIdAndName()
    {
        $out = ' id="' . htmlspecialchars($this->id()) . '" ';
        if (strlen($this->_attrs['name']) > 0)
        {
            $out .= 'name="' . htmlspecialchars($this->_attrs['name']) . '" ';
        }
        return $out;
    }

    protected function _printValue()
    {
        if (is_object($this->value) && !method_exists($this->value, '__toString'))
        {
            return '';
        }
        return strlen($this->value)
            ? 'value="' . htmlspecialchars($this->value) . '" '
            : '';
    }

    /**
     * 根据 DISABLED 属性返回字符串
     *
     * @return string
     */
    protected function _printDisabled()
    {
        return ($this->disabled) ? 'disabled="disabled" ' : '';
    }

    /**
     * 根据 CHECKED 属性返回字符串
     *
     * @return string
     */
    protected function _printChecked()
    {
        if ($this->checked_by_value)
        {
            return $this->value ? 'checked="checked" ' : '';
        }
        else
        {
            $checked = $this->checked;

            if (! $checked) return '';

            $value = $this->value;

            if ($value)
            {
                if ($checked == $value && strlen($checked) == strlen($value)
                    && strlen($checked) > 0)
                {
                    return 'checked="checked" ';
                }
                else
                {
                    return '';
                }
            }
            else
            {
                return 'checked="checked" ';
            }
        }
    }

    /**
     * 构造控件的属性字符串
     *
     * @param array|string $exclude
     *
     * @return string
     */
    protected function _printAttrs($exclude = 'id, name, value')
    {
        $exclude = Q::normalize($exclude);
        $exclude = array_flip($exclude);
        $out = '';
        foreach ($this->_attrs as $attr => $value)
        {
            if (isset($exclude[$attr])) continue;
            $out .= $attr .'="' . htmlspecialchars($value) . '" ';
        }
        return $out;
    }

}


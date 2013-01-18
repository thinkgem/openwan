<?php
// $Id: view_render_php.php 2672 2009-11-27 14:16:02Z jerry $

/**
 * 定义 QView_Render_PHP 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: view_render_php.php 2672 2009-11-27 14:16:02Z jerry $
 * @package mvc
 */

/**
 * QView_Render_PHP 类实现了视图架构的基础
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: view_render_php.php 2672 2009-11-27 14:16:02Z jerry $
 * @package mvc
 */
class QView_Render_PHP
{
    /**
     * 视图分析类名
     *
     * @var string
     */
    protected $_parser_name = 'QView_Render_PHP_Parser';

    /**
     * 视图文件所在目录
     *
     * @var string
     */
    public $view_dir;

    /**
     * 要输出的头信息
     *
     * @var array
     */
    public $headers;

    /**
     * 视图文件的扩展名
     *
     * @var string
     */
    public $file_extname = 'php';

    /**
     * 模板变量
     *
     * @var array
     */
    protected $_vars;

    /**
     * 视图
     *
     * @var string
     */
    protected $_viewname;

    /**
     * 要使用的布局视图
     *
     * @var string
     */
    protected $_view_layouts;

    /**
     * 当前使用的分析器
     *
     * @var QView_Render_PHP_Parser
     */
    protected $_parser;

    /**
     * 构造函数
     *
     * @param array $config
     */
    function __construct(array $config = null)
    {
        if (is_array($config))
        {
            foreach ($config as $key => $value)
            {
                $this->{$key} = $value;
            }
        }

        $this->cleanVars();
    }

    /**
     * 设置视图名称
     *
     * @param string $viewname
     *
     * @return QView_Render_PHP
     */
    function setViewname($viewname)
    {
        $this->_viewname = $viewname;
        return $this;
    }

    /**
     * 指定模板变量
     *
     * @param string|array $key
     * @param mixed $data
     *
     * @return QView_Render_PHP
     */
    function assign($key, $data = null)
    {
        if (is_array($key))
        {
            $this->_vars = array_merge($this->_vars, $key);
        }
        else
        {
            $this->_vars[$key] = $data;
        }
        return $this;
    }

    /**
     * 获取指定模板变量
     *
     * @param string
     *
     * @return mixed
     */
    function getVar($key, $default = null)
    {
        return isset($this->_vars[$key]) ? $this->_vars[$key] : $default;
    }

    /**
     * 获取所有模板变量
     *
     *
     * @return mixed
     */
    function getVars()
    {
        return $this->_vars;
    }

	/**
     * 清除所有模板变量
     *
     * @return QView_Render_PHP
	 */
	function cleanVars()
    {
        ///*
        $context = QContext::instance();
        $this->_vars = array(
            '_ctx'          => $context,
            '_BASE_DIR'     => $context->baseDir(),
            '_BASE_URI'     => $context->baseUri(),
            '_REQUEST_URI'  => $context->requestUri(),
        );
        //*/

        // TODO! 全局变量应该放到 控制器抽象类 _before_render() 中
        //$this->_vars = array();

        return $this;
    }

    /**
     * 渲染视图
     *
     * @param string $viewname
     * @param array $vars
     * @param array $config
     */
    function display($viewname = null, array $vars = null, array $config = null)
    {
        if (empty($viewname))
        {
            $viewname = $this->_viewname;
        }

        if (Q::ini('runtime_response_header'))
        {
            header('Content-Type: text/html; charset=' . Q::ini('i18n_response_charset'));
        }

        echo $this->fetch($viewname, $vars, $config);
    }

    /**
     * 执行
     */
    function execute()
    {
        $this->display($this->_viewname);
    }

    /**
     * 渲染视图并返回渲染结果
     *
     * @param string $viewname
     * @param array $vars
     * @param array $config
     *
     * @return string
     */
    function fetch($viewname = null, array $vars = null, array $config = null)
    {
        if (empty($viewname))
        {
            $viewname = $this->_viewname;
        }

        $this->_before_render();
        $view_dir = isset($config['view_dir']) ? $config['view_dir'] : $this->view_dir;
        $extname = isset($config['file_extname']) ? $config['file_extname'] : $this->file_extname;
        $filename = "{$view_dir}/{$viewname}.{$extname}";

        if (file_exists($filename))
        {
            if (!is_array($vars))
            {
                $vars = $this->_vars;
            }
            if (is_null($this->_parser))
            {
                $parser_name = $this->_parser_name;
                $this->_parser = new $parser_name($view_dir);
            }
            $output = $this->_parser->assign($vars)->parse($filename);
        }
        else
        {
            $output = '';
        }

        $this->_after_render($output);
        return $output;
    }

    /**
     * 渲染之前调用
     *
     * 继承类可以覆盖此方法。
     */
    protected function _before_render()
    {
    }

    /**
     * 渲染之后调用
     *
     * 继承类可以覆盖此方法。
     *
     * @param string $output
     */
    protected function _after_render(& $output)
    {
    }

}

/**
 * QView_Render_PHP_Parser 类实现了视图的分析
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: view_render_php.php 2672 2009-11-27 14:16:02Z jerry $
 * @package mvc
 */
class QView_Render_PHP_Parser
{
    /**
     * 视图文件扩展名
     * 
     * @var string
     */
    protected $_extname;

    /**
     * 视图堆栈
     *
     * @var array
     */
    private $_stacks = array();

    /**
     * 当前处理的视图
     *
     * @var int
     */
    private $_current;

    /**
     * 视图变量
     *
     * @var array
     */
    protected $_vars;

    /**
     * 视图文件所在目录
     *
     * @var string
     */
    private $_view_dir;

    /**
     * 构造函数
     */
    function __construct($view_dir)
    {
        $this->_view_dir = $view_dir;
    }

    /**
     * 设置分析器已经指定的变量
     *
     * @param array $vars
     *
     * @return QView_Render_PHP_Parser
     */
    function assign(array $vars)
    {
        $this->_vars = $vars;
        return $this;
    }

    /**
     * 返回分析器使用的视图文件的扩展名
     *
     * @return string
     */
    function extname()
    {
        return $this->_extname;
    }

    /**
     * 分析一个视图文件并返回结果
     *
     * @param string $filename
     * @param string $view_id
     * @param array $inherited_stack
     *
     * @return string
     */
    function parse($filename, $view_id = null, array $inherited_stack = null)
    {
        if (!$view_id) $view_id = mt_rand();

        $stack = array(
            'id'            => $view_id,
            'contents'      => '',
            'extends'       => '',
            'blocks_stacks' => array(),
            'blocks'        => array(),
            'blocks_config' => array(),
            'nested_blocks' => array(),
        );
        array_push($this->_stacks, $stack);
        $this->_current = count($this->_stacks) - 1;
        unset($stack);

        ob_start();
        $this->_include($filename);
        $stack = $this->_stacks[$this->_current];
        $stack['contents'] = ob_get_clean();

        // 如果有继承视图，则用继承视图中定义的块内容替换当前视图的块内容
        if (is_array($inherited_stack))
        {
            foreach ($inherited_stack['blocks'] as $block_name => $contents)
            {
                if (isset($stack['blocks_config'][$block_name]))
                {
                    switch (strtolower($stack['blocks_config'][$block_name]))
                    {
                    case 'append':
                        $stack['blocks'][$block_name] .= $contents;
                        break;
                    case 'replace':
                    default:
                        $stack['blocks'][$block_name] = $contents;
                    }
                }
                else
                {
                    $stack['blocks'][$block_name] = $contents;
                }
            }
        }

        // 如果有嵌套 block，则替换内容
        while (list($child, $parent) = array_pop($stack['nested_blocks']))
        {
            $stack['blocks'][$parent] = str_replace("%block_contents_placeholder_{$child}_{$view_id}%",
                $stack['blocks'][$child], $stack['blocks'][$parent]);
            unset($stack['blocks'][$child]);
        }

        // 保存对当前视图堆栈的修改
        $this->_stacks[$this->_current] = $stack;

        if ($stack['extends'])
        {
            // 如果有当前视图是从某个视图继承的，则载入继承视图
            $filename = "{$this->_view_dir}/{$stack['extends']}.{$this->_extname}";
            return $this->parse($filename, $view_id, $this->_stacks[$this->_current]);
        }
        else
        {
            // 最后一个视图一定是没有 extends 的
            $last = array_pop($this->_stacks);
            foreach ($last['blocks'] as $block_name => $contents)
            {
                $last['contents'] = str_replace("%block_contents_placeholder_{$block_name}_{$last['id']}%",
                    $contents, $last['contents']);
            }
            $this->_stacks = array();

            return $last['contents'];
        }
    }

    /**
     * 视图的继承
     *
     * @param string $tplname
     *
     * @access public
     */
    protected function _extends($tplname)
    {
        $this->_stacks[$this->_current]['extends'] = $tplname;
    }

    /**
     * 开始定义一个区块
     *
     * @param string $block_name
     * @param mixed $config
     *
     * @access public
     */
    protected function _block($block_name, $config = null)
    {
        $stack =& $this->_stacks[$this->_current];
        if (!empty($stack['blocks_stacks']))
        {
            // 如果存在嵌套的 block，则需要记录下嵌套的关系
            $last = $stack['blocks_stacks'][count($stack['blocks_stacks']) - 1];
            $stack['nested_blocks'][] = array($block_name, $last);
        }
        $this->_stacks[$this->_current]['blocks_config'][$block_name] = $config;
        array_push($stack['blocks_stacks'], $block_name);
        ob_start();
    }

    /**
     * 结束一个区块
     *
     * @access public
     */
    protected function _endblock()
    {
        $block_name = array_pop($this->_stacks[$this->_current]['blocks_stacks']);
        $this->_stacks[$this->_current]['blocks'][$block_name] = ob_get_clean();
        echo "%block_contents_placeholder_{$block_name}_{$this->_stacks[$this->_current]['id']}%";
    }

    /**
     * 构造一个控件
     *
     * @param string $control_type
     * @param string $id
     * @param array $args
     *
     *
     * @access public
     */
    protected function _control($control_type, $id = null, $args = array())
    {
        Q::control($control_type, $id, $args)->display();
        // TODO! display($this) 避免多次构造视图解析器实例
        // 由于视图解析器实例的继承问题，所以暂时无法利用
    }

    /**
     * 载入一个视图片段
     *
     * @param string $element_name
     * @param array $vars
     *
     * @access public
     */
    protected function _element($element_name, array $vars = null)
    {
        $filename = "{$this->_view_dir}/_elements/{$element_name}_element.{$this->_extname}";
        $this->_include($filename, $vars);
    }

    /**
     * 载入视图文件
     */
    protected function _include($___filename, array $___vars = null)
    {
        $this->_extname = pathinfo($___filename, PATHINFO_EXTENSION);
        extract($this->_vars);
        if (is_array($___vars)) extract($___vars);
        include $___filename;
    }
}


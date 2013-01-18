<?php
// $Id: abstract.php 895 2010-03-23 05:36:29Z thinkgem $
/**
 * 应用程序的公共控制器基础类
 * @author WangZhen <thinkgem@163.com>
 */
abstract class Controller_Abstract extends QController_Abstract
{
    /**
     * 控制器动作要渲染的数据
     *
     * @var array
     */
    protected $_view = array();

    /**
     * 控制器要使用的视图类
     *
     * @var string
     */
    protected $_view_class = 'QView_Render_PHP';

    /**
     * 控制器要使用的视图
     *
     * @var string
     */
    protected $_viewname = null;

    /**
     * 控制器所属的应用程序
     *
     * @var CommunityApp
     */
    protected $_app;

    /**
     * 当前位置路径
     * @var <type>
     */
    protected $_pathway;

    /**
     * 构造函数
     */
    function __construct($app)
    {
        parent::__construct();
        $this->_app = $app;

        //建立路径
        $this->_pathway = new Helper_Pathway();
        $this->_pathway->addStep('首页',url('default::default/index'));
    }

    /**
     * 执行指定的动作
     *
     * @return mixed
     */
    function execute($action_name, array $args = array())
    {
        $action_method = "action{$action_name}";

        // 执行指定的动作方法
        $this->_before_execute();

        #IFDEF DBEUG
        QLog::log('EXECUTE ACTION: '. get_class($this) . '::' . $action_method . '()', QLog::DEBUG);
        #ENDIF

        $response = call_user_func_array(array($this, $action_method), $args);
        $this->_after_execute($response);

        if (is_null($response) && is_array($this->_view))
        {
            // 如果动作没有返回值，并且 $this->view 不为 null，
            // 则假定动作要通过 $this->view 输出数据
            $config = array('view_dir' => $this->_getViewDir());
            $response = new $this->_view_class($config);
            $response->setViewname($this->_getViewName())->assign($this->_view);
            $this->_before_render($response);
        }
        elseif ($response instanceof $this->_view_class)
        {
            $response->assign($this->_view);
            $this->_before_render($response);
        }

        return $response;
    }

    /**
     * 指定的控制器动作未定义时调用
     *
     * @param string $action_name
     */
    function _on_action_not_defined($action_name)
    {
    }

    /**
     * 执行控制器动作之前调用
     */
    protected function _before_execute()
    {
        $this->_view['_app'] = $this->_app;
        $this->_view['pathway'] = $this->_pathway;
        $this->_view['currentUser'] = $this->_app->currentUser();        
    }

    /**
     * 执行控制器动作之后调用
     *
     * @param mixed $response
     */
    protected function _after_execute(& $response)
    {
    }

    /**
     * 渲染之前调用
     *
     * @param QView_Render_PHP
     */
    protected function _before_render($response)
    {
    }

    /**
     * 准备视图目录
     *
     * @return array
     */
    protected function _getViewDir()
    {
        if ($this->_context->module_name)
        {
            $dir = Q::ini('app_config/MODULE_DIR') . "/{$this->_context->module_name}/view";
        }
        else
        {
            $dir = Q::ini('app_config/APP_DIR') . '/view';
        }

        if ($this->_context->namespace)
        {
            $dir .= "/{$this->_context->namespace}";
        }
        return $dir;
    }

    /**
     * 确定要使用的视图
     *
     * @return string
     */
    protected function _getViewName()
    {
        if ($this->_viewname === false)
        {
            return false;
        }
        $viewname = empty($this->_viewname) ? $this->_context->action_name : $this->_viewname;
        return strtolower("{$this->_context->controller_name}/{$viewname}");
    }

    /**
     * 显示一个提示页面，然后重定向浏览器到新地址
     *
     * @param string $caption
     * @param string $message
     * @param string $url
     * @param int $delay
     * @param string $script
     *
     * @return QView_Render_PHP
     */
    protected function _redirectMessage($caption, $message, $url, $delay = 5, $script = '')
    {
        $config = array('view_dir' => $this->_getViewDir());
        $response = new $this->_view_class($config);
        $response->setViewname('redirect_message');
        $response->assign(array(
            'message_caption'   => $caption,
            'message_body'      => $message,
            'redirect_url'      => $url,
            'redirect_delay'    => $delay,
            'hidden_script'     => $script,
        ));

        return $response;
    }
}


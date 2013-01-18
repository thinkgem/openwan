<?php

/**
 * 默认控制器
 *
 * @package app
 */
class Controller_Default extends Controller_Abstract
{
    /**
     * 确认应用程序基本信息
     */
    function actionIndex()
    {
        $app = $this->_managed_app;
        $this->_view['app_config']       = $app->config();
        // $this->view['ini_descriptions'] = $app->getIniDescriptions($this->app()->lang);
    }

    function actionThanks()
    {

    }
}

<?php
// $Id: context.php 2227 2009-02-08 15:38:10Z dualface $

/**
 * 测试 QContext 类
 */
class UT_Core_Context extends QTest_UnitTest_Abstract
{
    protected $_ctx;

    function setUp()
    {
        $this->_ctx = QContext::instance();
    }

    function arrayProvider()
    {
        return array(
            array(array(
                'controller' => 'goods',
                'action'     => 'search',
                'key'        => 'phone',
                'page'       => 5,
            )),
            array(array(
                'controller' => null,
                'action'     => null,
            )),
            array(array(
                'controller' => 'posts',
                'action'     => 'create',
                'namespace'  => 'admin',
            )),
        );
    }

    /**
     * @dataProvider arrayProvider
     */
    function testGetAccess($data)
    {
        $_GET       = $data;
        $_POST      = array();
        $_REQUEST   = $_GET;
        $_COOKIE    = array();
        $this->_ctx->reinit(true);

        foreach ($data as $key => $value)
        {
            $this->assertEquals($value, $this->_ctx->{$key});
            $this->assertEquals($value, $this->_ctx->get($key));
            $this->assertEquals($value, $this->_ctx->query($key));
            $this->assertEquals($value, $this->_ctx[$key]);
            $this->assertEquals($key . $value, $this->_ctx->get($key . $value, $key . $value));
            $this->assertEquals($key . $value, $this->_ctx->query($key . $value, $key . $value));
        }
    }

    /**
     * @dataProvider arrayProvider
     */
    function testPostAccess($data)
    {
        $_GET       = array();
        $_POST      = $data;
        $_REQUEST   = $_POST;
        $_COOKIE    = array();
        $this->_ctx->reinit(true);

        foreach ($data as $key => $value)
        {
            if (!in_array($key, array('controller_name', 'action_name', 'namespace', 'module')))
            {
                $this->assertEquals($value, $this->_ctx->{$key});
            }
            $this->assertEquals($value, $this->_ctx->query($key));
            $this->assertEquals($value, $this->_ctx->post($key));
            $this->assertEquals($value, $this->_ctx[$key]);
            $this->assertEquals($key . $value, $this->_ctx->query($key . $value, $key . $value));
            $this->assertEquals($key . $value, $this->_ctx->post($key . $value, $key . $value));
        }
    }

    /**
     * @dataProvider arrayProvider
     */
    function testRequestAccess($data)
    {
        $_GET       = array_slice($data, 0, 1);
        $_POST      = array_slice($data, 1);
        $_REQUEST   = $data;
        $_COOKIE    = array();
        $this->_ctx->reinit(true);

        foreach ($data as $key => $value)
        {
            $this->assertEquals($value, $this->_ctx->query($key));
            $this->assertEquals($value, $this->_ctx[$key]);
            $this->assertEquals($key . $value, $this->_ctx->query($key . $value, $key . $value));
        }
    }

    /**
     * @dataProvider arrayProvider
     */
    function testCookieAccess($data)
    {
        $_GET       = array();
        $_POST      = array();
        $_REQUEST   = array();
        $_COOKIE    = $data;
        $this->_ctx->reinit(true);

        foreach ($data as $key => $value)
        {
            $this->assertEquals($value, $this->_ctx->cookie($key));
            $this->assertEquals($key . $value, $this->_ctx->cookie($key . $value, $key . $value));
        }
    }

    /**
     * @dataProvider arrayProvider
     */
    function testServerAccess($data)
    {
        $_SERVER    = $data;
        $this->_ctx->reinit(true);

        foreach ($data as $key => $value)
        {
            $this->assertEquals($value, $this->_ctx->server($key));
            $this->assertEquals($key . $value, $this->_ctx->server($key . $value, $key . $value));
        }
    }

    /**
     * @dataProvider arrayProvider
     */
    function testEnvAccess($data)
    {
        $_ENV       = $data;
        $this->_ctx->reinit(true);

        foreach ($data as $key => $value)
        {
            $this->assertEquals($value, $this->_ctx->env($key));
            $this->assertEquals($key . $value, $this->_ctx->env($key . $value, $key . $value));
        }
    }

    /**
     * @dataProvider arrayProvider
     */
    function testParamsAccess($data)
    {
        $this->_ctx->reinit(true);

        foreach ($data as $key => $value)
        {
            $this->assertEquals($value, $this->_ctx->param($key, $value));
            $this->_ctx->changeParam($key, $value);
            $this->assertEquals($value, $this->_ctx->param($key));
        }
    }

    function testProtocol()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $this->_ctx->reinit(true);
        $this->assertEquals('http', $this->_ctx->protocol());
    }

    function requestUriProvider()
    {
        return array(
            array('/index.php?controller=posts&action=create'),
            array('/news/index.php?controller=posts&action=create'),
            array('/index.php/posts/create'),
            array('/news/show/id/1'),
        );
    }

    /**
     * @dataProvider requestUriProvider
     */
    function testRquestUri($uri)
    {
        $_SERVER['REQUEST_URI'] = $uri;
        $this->_ctx->reinit(true);
        $this->assertEquals($uri, $this->_ctx->requestUri());
        $this->_ctx->changeRequestUri($uri . $uri);
        $this->assertEquals($uri . $uri, $this->_ctx->requestUri());
    }

    /**
     * @daraProvider urlProvider
     */
    function testUrl($args, $url)
    {
        $this->markTestIncomplete();
    }

    function testUDIString()
    {
        $this->markTestIncomplete();
    }

    function testUDIArray()
    {
        $this->markTestIncomplete();
    }

    function testNormalizeUDI()
    {
        $this->markTestIncomplete();
    }

    function testRequestUDI()
    {
        $this->markTestIncomplete();
    }

    function testChangeRequestUDI()
    {
        $this->markTestIncomplete();
    }
}


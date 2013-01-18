<?php
// $Id: router.php 2326 2009-03-22 06:56:05Z dualface $

/**
 * 测试路由
 */
class UT_MVC_Router extends QTest_UnitTest_Abstract
{
    /**
     * 用于测试的 URL 和期望的解析结果
     *
     * @var array
     */
    protected $_tests_url = array();

    /**
     * 用于测试反向解析的参数和期望的测试结果
     *
     * @var array
     */
    protected $_tests_args = array();

    protected function setUp()
    {
        $rules = Helper_YAML::load(FIXTURE_DIR . '/mvc/routes.yaml');
        foreach ($rules as $route_name => $rule)
        {
            $index = 0;
            foreach ($rule['tests_url'] as $test)
            {
                $this->_tests_url["{$route_name}-{$index}"] = $test;
                $index++;
            }
            unset($rules[$route_name]['tests_url']);

            $index = 0;
            foreach ($rule['tests_args'] as $test)
            {
                $this->_tests_args["{$route_name}:{$index}"] = $test;
                $index++;
            }
            unset($rules[$route_name]['tests_args']);
        }
        Q::changeIni('routes', $rules);
    }

    /**
     * 测试对 URL 的分析
     */
    function testParse()
    {
        $router = new QRouter();
        $router->import(Q::ini('routes'));
        foreach ($this->_tests_url as $route_name => $test)
        {
            list($route_name) = explode('-', $route_name);
            $path = $test['_path'];
            unset($test['_path']);

            $result = $router->match($path);
            $this->assertType('array', $result);

            foreach ($test as $varname => $value)
            {
                if (!array_key_exists($varname, $result) || $value != $result[$varname])
                {
                    $r = $router->lastMatchedRouteName();
                    dump($test, "path: {$path}");
                    dump($router->get($r), "matched route: {$r}");
                    dump($result, 'parse result');
                    dump($router->get($route_name), "expected route: {$route_name}");
                }

                $this->assertArrayHasKey($varname, $result, "\$result has't key: {$varname}.");
                $this->assertEquals($value, $result[$varname],
                    sprintf('expected varname "%s" value is "%s".', $varname, $value));
                unset($result[$varname]);
            }

            foreach ($result as $key => $value)
            {
                switch ($key)
                {
                case QContext::UDI_CONTROLLER:
                case QContext::UDI_NAMESPACE:
                case QContext::UDI_MODULE:
                    $this->assertEquals('default', $value);
                    break;
                case QContext::UDI_ACTION:
                    $this->assertEquals('index', $value);
                    break;
                default:
                    $this->assertEmpty($value, "\$result[{$key}] not empty.");
                }
            }
        }
    }

    /**
     * 测试反向解析
     */
    function testReverseParse()
    {
        $context = QContext::instance();
        $router = new QRouter();
        $router->import(Q::ini('routes'));
        foreach ($this->_tests_args as $offset => $test)
        {
            $copy = $test;
            $path = $test['_path'];
            unset($test['_path']);
            $result = $router->url($test);

            list($route_name) = explode(':', $offset);

            if ($route_name != $router->lastReverseMatchedRouteName())
            {
                dump('-----------------------------------');
                dump($path, 'test path');
                dump($test, $result);
                dump($router->lastReverseMatchedRouteName(), 'used route');
                dump($route_name, 'expected route');
                dump($router->get($route_name));
            }

            $this->assertEquals($route_name, $router->lastReverseMatchedRouteName(), "Expected route name is [{$route_name}].");
            $this->assertEquals($path, $result, "{$path} == {$result}\n" . print_r($copy, true) . "\n" . print_r($test, true));
        }
    }

}



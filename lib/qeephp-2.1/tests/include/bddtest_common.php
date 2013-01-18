<?php

/**
 * 用于 BDD 测试的公共文件
 */

require_once 'PHPUnit/Extensions/Story/TestCase.php';

/**
 * PHPUnit_Extensions_Story_Runner 使用 PHP 5.3 的闭包来改善 BDD 代码的书写
 *
 * 在继承类中，不再需要实现 runGiven()、runWhen() 和 runThen() 方法。
 */
abstract class PHPUnit_Extensions_Story_Runner extends PHPUnit_Extensions_Story_TestCase
{

    protected function runStepWithClosure(& $world, $action, $arguments)
    {
        if (isset($arguments[0]) && $arguments[0] instanceof Closure)
        {
            $lambda = $arguments[0];
            $lambda($world, $action);
        }
    }

    function runGiven(&$world, $action, $arguments)
    {
        $this->runStepWithClosure($world, $action, $arguments);
    }

    function runWhen(&$world, $action, $arguments)
    {
        $this->runStepWithClosure($world, $action, $arguments);
    }

    function runThen(&$world, $action, $arguments)
    {
        $this->runStepWithClosure($world, $action, $arguments);
    }
}


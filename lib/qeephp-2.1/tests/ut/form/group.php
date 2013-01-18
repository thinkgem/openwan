<?php
// $Id: group.php 2259 2009-02-18 00:58:27Z dualface $

/**
 * 测试 QForm 的群组功能
 */
class UT_Form_Group extends QTest_UnitTest_Abstract
{
    function setUp()
    {
        Q::import(FIXTURE_DIR . '/form');

    }

    function testCreatingForm()
    {
        $forms = array(
            Form_Group::createDirect(),
            Form_Group::createFromConfig(),
        );

        foreach ($forms as $form)
        {
            $this->assertTrue($form->existsElement('userinfo'), '$form->existsElement(\'userinfo\')');
            $this->assertTrue($form->existsElement('userinfo/username'), '$form->existsElement(\'userinfo/username\')');
            $this->assertTrue($form->existsElement('profile/address'), '$form->existsElement(\'profile/address\')');

            $this->assertTrue(isset($form['userinfo']), 'isset($form[\'userinfo\'])');
            $this->assertTrue(isset($form['userinfo']['username']), 'isset($form[\'userinfo\'][\'username\'])');
            $this->assertTrue(isset($form['profile']['address']), 'isset($form[\'profile\'][\'address\'])');

            $username = $form->element('userinfo/username');
            $this->assertType('QForm_Element', $username);
            $this->assertEquals('username', $username->id);

            $username = $form['userinfo']['username'];
            $this->assertType('QForm_Element', $username);
            $this->assertEquals('username', $username->id);

            $this->assertSame($form, $form['userinfo']->owner());
            $this->assertSame($form['userinfo'], $form['userinfo']['username']->owner());
        }
    }

    function nestedDataProvider()
    {
        return array(
            array(
                null,
                array(
                    'userinfo' => array('username' => 'dualFace ', 'password' => '123456'),
                    'profile'  => array('address'  => 'ZiGong',   'postcode' => '643000'),
                ),
                array(
                    'userinfo' => array('username' => 'dualface', 'password' => '123456'),
                    'profile'  => array('address'  => 'ZiGong',   'postcode' => '643000'),
                ),
            ),

            array(
                array('userinfo' => array('username', 'password'), 'profile' => array('postcode')),
                array(
                    'userinfo' => array('username' => ' 1123', 'password' => '123'),
                    'profile'  => array('address'  => 'ZiGong', 'postcode' => 'XASDS'),
                ),
                array(
                    'userinfo' => array('username' => '1123', 'password' => '123'),
                    'profile'  => array('address'  => 'ZiGong', 'postcode' => 'XASDS'),
                )
            ),

            array(
                array('userinfo' => array('username', 'password'), 'profile' => array('address', 'postcode')),
                array(
                    'userinfo' => array('username' => ' 23A ', 'password' => '123'),
                    'profile'  => array('address'  => '', 'postcode' => 'XASDS'),
                ),
                array(
                    'userinfo' => array('username' => '23a', 'password' => '123'),
                    'profile'  => array('address'  => '', 'postcode' => 'XASDS'),
                )
            ),
        );
    }

    /**
     * @dataProvider nestedDataProvider
     */
    function testNestedValidateWithEmptyID($test_failed, $post, $values)
    {
        $this->testNestedValidate($test_failed, $post, $values, null);
        $this->testNestedValidate($test_failed, $post, $values, '');
    }

    /**
     * @dataProvider nestedDataProvider
     */
    function testNestedValidate($test_failed, $post, $values, $id = 'form1')
    {
        $form = Form_Group::createFromConfig($id);
        $failed = null;

        $ret = $form->validate($post, $failed);
        $this->assertEquals($ret, $form->isValid());
        $this->assertEquals($values, $form->values());
        $this->assertEquals($post, $form->unfilteredValues());

        if (empty($test_failed))
        {
            $this->assertTrue($ret);
        }
        else
        {
            $t = array_keys($failed);
            $arr = array();
            foreach ($t as $key)
            {
                $arr[$key] = array_keys($failed[$key]);
            }
            $this->assertEquals($test_failed, $arr);
        }

        $form = Form_Group::createFromConfig();
        $failed = null;

        $ret = $form->import($post);
        $this->assertEquals($post, $form->values());
        $this->assertEquals($post, $form->unfilteredValues());
    }

    /**
     * @dataProvider nestedDataProvider
     */
    function testNestedValidate2WithEmptyID($test_failed, $post, $values)
    {
        $this->testNestedValidate2($test_failed, $post, $values, null);
        $this->testNestedValidate2($test_failed, $post, $values, '');
    }

    /**
     * @dataProvider nestedDataProvider
     */
    function testNestedValidate2($test_failed, $post, $values, $id = 'form1')
    {
        $form = Form_Group::createFromConfig($id);
        $form['userinfo']->changeNestedName('');
        $failed = null;

        $arr = $post['userinfo'];
        unset($post['userinfo']);
        $post = array_merge($arr, $post);
        $arr = $values['userinfo'];
        unset($values['userinfo']);
        $values = array_merge($arr, $values);

        $ret = $form->validate($post, $failed);
        $this->assertEquals($ret, $form->isValid());
        $this->assertEquals($values, $form->values(), '$values == $form->values()');
        $this->assertEquals($post, $form->unfilteredValues(), '$post == $form->unfilteredValues()');

        if (empty($test_failed))
        {
            $this->assertTrue($ret);
        }
        else
        {
            $t = array_keys($failed);
            $arr = array();
            foreach ($t as $key)
            {
                $arr[$key] = array_keys($failed[$key]);
            }
            $this->assertEquals($test_failed, $arr);
        }

        $ret = $form->import($post);
        $this->assertEquals($post, $form->values());
        $this->assertEquals($post, $form->unfilteredValues());
    }


    /**
     * @dataProvider nestedDataProvider
     */
    function testNestedValidate3WithEmptyID($test_failed, $post, $values)
    {
        $this->testNestedValidate3($test_failed, $post, $values, null);
        $this->testNestedValidate3($test_failed, $post, $values, '');
    }

    /**
     * @dataProvider nestedDataProvider
     */
    function testNestedValidate3($test_failed, $post, $values, $id = 'form1')
    {
        $form = Form_Group::createFromConfig($id);
        $form['userinfo']['username']->changeNestedName('');
        $failed = null;

        $username = $post['userinfo']['username'];
        unset($post['userinfo']['username']);
        $post['username'] = $username;
        $username = $values['userinfo']['username'];
        unset($values['userinfo']['username']);
        $values['username'] = $username;

        $ret = $form->validate($post, $failed);
        $this->assertEquals($ret, $form->isValid());
        $this->assertEquals($values, $form->values(), '$values == $form->values()');
        $this->assertEquals($post, $form->unfilteredValues(), '$post == $form->unfilteredValues()');

        if (empty($test_failed))
        {
            $this->assertTrue($ret);
        }
        else
        {
            $t = array_keys($failed);
            $arr = array();
            foreach ($t as $key)
            {
                $arr[$key] = array_keys($failed[$key]);
            }
            $this->assertEquals($test_failed, $arr);
        }

        $ret = $form->import($post);
        $this->assertEquals($post, $form->values());
        $this->assertEquals($post, $form->unfilteredValues());
    }

    /**
     * @dataProvider nestedDataProvider
     */
    function testDataBindingWithEmptyID($test_failed, $post, $values)
    {
        $this->testDataBinding($test_failed, $post, $values, null);
        $this->testDataBinding($test_failed, $post, $values, '');
    }

    /**
     * @dataProvider nestedDataProvider
     */
    function testDataBinding($test_failed, $post, $values, $id = 'form1')
    {
        $form = Form_Group::createFromConfig($id);
        $form['userinfo']['username']->enableDataBinding(false);
        unset($values['userinfo']['username']);
        $failed = null;

        $ret = $form->validate($post, $failed);
        $this->assertEquals($ret, $form->isValid());
        $this->assertEquals($values, $form->values(), '$values == $form->values()');
        unset($post['userinfo']['username']);
        $this->assertEquals($post, $form->unfilteredValues(), '$post == $form->unfilteredValues()');

        if (isset($test_failed['userinfo']))
        {
            $key = array_search('username', $test_failed['userinfo']);
            if ($key !== false)
            {
                unset($test_failed['userinfo'][$key]);
                sort($test_failed['userinfo']);
            }
        }

        if (empty($test_failed))
        {
            $this->assertTrue($ret);
        }
        else
        {
            $t = array_keys($failed);
            $arr = array();
            foreach ($t as $key)
            {
                $arr[$key] = array_keys($failed[$key]);
            }
            $this->assertEquals($test_failed, $arr);
        }

        $ret = $form->import($post);
        $this->assertEquals($post, $form->values());
        $this->assertEquals($post, $form->unfilteredValues());
    }

    /**
     * @dataProvider nestedDataProvider
     */
    function testDataBinding2WithEmptyID($test_failed, $post, $values)
    {
        $this->testDataBinding2($test_failed, $post, $values, null);
        $this->testDataBinding2($test_failed, $post, $values, '');
    }

    /**
     * @dataProvider nestedDataProvider
     */
    function testDataBinding2($test_failed, $post, $values, $id = 'form1')
    {
        $form = Form_Group::createFromConfig($id);
        $form['userinfo']->enableDataBinding(false);
        unset($values['userinfo']);
        $failed = null;

        $ret = $form->validate($post, $failed);
        $this->assertEquals($ret, $form->isValid());
        $this->assertEquals($values, $form->values(), '$values == $form->values()');
        unset($post['userinfo']);
        $this->assertEquals($post, $form->unfilteredValues(), '$post == $form->unfilteredValues()');

        if (isset($test_failed['userinfo']))
        {
            unset($test_failed['userinfo']);
        }

        if (empty($test_failed))
        {
            $this->assertTrue($ret);
        }
        else
        {
            $t = array_keys($failed);
            $arr = array();
            foreach ($t as $key)
            {
                $arr[$key] = array_keys($failed[$key]);
            }
            $this->assertEquals($test_failed, $arr);
        }

        $ret = $form->import($post);
        $this->assertEquals($post, $form->values());
        $this->assertEquals($post, $form->unfilteredValues());
    }

    /**
     * @dataProvider nestedDataProvider
     */
    function testInvalidate($test_failed, $post, $values)
    {
        $form = Form_Group::createFromConfig();


    }
}


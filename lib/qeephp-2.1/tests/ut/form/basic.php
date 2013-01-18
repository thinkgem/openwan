<?php
// $Id: basic.php 2259 2009-02-18 00:58:27Z dualface $

/**
 * 测试 QForm 的基本功能
 */
class UT_Form_Basic extends QTest_UnitTest_Abstract
{
    const URL1 = 'http://www.example.com/register';
    const URL2 = 'http://www.example.com/login';

    function setUp()
    {
        Q::import(FIXTURE_DIR . '/form');
    }

    function testCreatingForm()
    {
        $forms[] = new QForm('form1', self::URL1, QForm::POST);

        $form = new QForm();
        $form->id = 'form1';
        $form->action = self::URL1;
        $form->method = QForm::POST;
        $forms[] = $form;

        $form = new QForm();
        $form->set('id', 'form1')
             ->set('action', self::URL1)
             ->set('method', QForm::POST);
        $forms[] = $form;

        foreach ($forms as $form)
        {
            $this->assertEquals('form1', $form->id);
            $this->assertEquals('form1', $form->name);
            $this->assertEquals(self::URL1, $form->action);
            $this->assertEquals(QForm::POST, $form->method);
        }
    }

    function testCreatingForm2()
    {
        $form1 = new QForm('form1', self::URL1, QForm::POST);
        $this->assertEquals('form1', $form1->id);
        $this->assertEquals('form1', $form1->name);

        $form2 = new QForm();
        $form2->id = 'form2';
        $form2->name = 'form2-name';
        $this->assertEquals('form2', $form2->id);
        $this->assertEquals('form2-name', $form2->name);

        $form3 = new QForm();
        $form3->set('id', 'form3')
              ->set('name', 'form3-name');
        $this->assertEquals('form3', $form3->id);
        $this->assertEquals('form3-name', $form3->name);
    }

    function testLoadFromConfig()
    {
        $form = new QForm('form1', self::URL1, QForm::POST);
        $this->assertEquals('form1', $form->id);
        $this->assertEquals('form1', $form->name);
        $this->assertEquals(self::URL1, $form->action);
        $this->assertEquals(QForm::POST, $form->method);

        $config = Helper_YAML::load(FIXTURE_DIR . '/form/form1.yaml');
        $form->loadFromConfig($config);

        $this->assertEquals('form1', $form->id);
        $this->assertEquals('form1-name', $form->name);
        $this->assertEquals(self::URL2, $form->action);
        $this->assertEquals(QForm::GET, $form->method);
    }

    function testElements()
    {
        $form = new QForm('form1', self::URL1);
        $form->add(QForm::ELEMENT, 'username', array(
                '_ui' => 'textbox', 'maxlength' => 15, 'class' => 'input'
             ))
             ->add(QForm::ELEMENT, 'password', array(
                '_ui' => 'password', 'class' => 'input'
             ));

        $this->assertTrue($form->existsElement('username'));
        $this->assertTrue($form->existsElement('password'));
        $this->assertEquals('textbox', $form->element('username')->_ui);
        $this->assertEquals('password', $form->element('password')->_ui);

        $form->remove('password');
        $this->assertFalse($form->existsElement('password'));

        $attrs = $form['username']->attrs();
        $this->assertEquals(array(
            'id' => 'username', 'name' => 'username',
            'maxlength' => 15, 'class' => 'input'
        ), $attrs);

        $all_attrs = $form['username']->allAttrs();
        $this->assertEquals(array(
            'id' => 'username', 'name' => 'username',
            'maxlength' => 15, 'class' => 'input', '_ui' => 'textbox',
            '_data_binding' => true, '_nested_name' => 'form1',
        ), $all_attrs);

        unset($form['username']);
        $this->assertFalse($form->existsElement('username'));
        $this->assertFalse(isset($form['username']));
    }

    function processDataProvider()
    {
        return array(
            array('',      array('name' => 'sSA', 'price' => '1.5'), array('name' => 'ssa', 'price' => (float)1.5)),
            array('name',  array('name' => 'DS24', 'price' => '1.5'), array('name' => 'ds24', 'price' => (float)1.5)),
            array('name',  array('name' => 'a', 'price' => '1.5'), array('name' => 'a', 'price' => (float)1.5)),
            array('price', array('name' => 'aff', 'price' => 0.1), array('name' => 'aff', 'price' => (float)0.1)),
            array('price', array('name' => 'AfF', 'price' => -1), array('name' => 'aff', 'price' => (float)-1)),
            array('price', array('name' => 'aFf', 'price' => 'xxx'), array('name' => 'aff', 'price' => (float)0)),
            array('name, price', array('name' => 'b', 'price' => -5.3), array('name' => 'b', 'price' => (float)-5.3)),
        );
    }

    /**
     * @dataProvider processDataProvider
     */
    function testProcessData($test_failed, $post, $values, $id = 'form1')
    {
        $forms = array(
            Form_Data::createDirect($id),
            Form_Data::createFromConfig($id),
        );

        $test_failed = Q::normalize($test_failed);
        foreach ($forms as $form)
        {
            $this->assertTrue(isset($form['name']), 'element "name" not exists.');
            $this->assertTrue(isset($form['price']), 'element "price" not exists.');
            /* @var $form Form_Data */

            $failed = null;
            $ret = $form->validate($post, $failed);
            $this->assertEquals($ret, $form->isValid(), '$ret === $form->isValid()');
            $this->assertEquals($values, $form->values(), '$values === $form->values()');
            $this->assertEquals($values, $form->value());
            $this->assertEquals($post, $form->unfilteredValues(), '$post === $form->unfilteredValues()');
            $this->assertEquals($post, $form->unfilteredValue());
            if (empty($test_failed))
            {
                $this->assertTrue($ret);
            }
            else
            {
                foreach ($failed as $id => $errors)
                {
                    $this->assertEquals(false, $form[$id]->isValid());
                    $this->assertEquals($errors, $form[$id]->errorMsg());
                }

                $failed = array_keys($failed);
                $this->assertEquals($test_failed, $failed);
            }
        }
    }


    /**
     * @dataProvider processDataProvider
     */
    function testProcessDataWithEmptyID($test_failed, $post, $values)
    {
        $this->testProcessData($test_failed, $post, $values, null);
        $this->testProcessData($test_failed, $post, $values, '');
    }
}


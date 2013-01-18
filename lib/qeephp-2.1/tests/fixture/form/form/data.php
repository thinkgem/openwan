<?php

class Form_Data extends QForm
{
    /**
     * @return Form_Data
     */
    static function createDirect($id = 'form1')
    {
        $form = new Form_Data($id);
        $form->add(self::ELEMENT, 'name', array('_ui' => 'textbox'))
             ->addFilters('trim, strtolower')
             ->addFilters(array(array('substr', 0, 5)))
             ->addValidations(array(
                array('is_alpha', 'name 只能是字母'),
                array('min_length', 2, 'name 至少要 2 个字符'),
             ));

        $form->add(self::ELEMENT, 'price', array('_ui' => 'textbox'))
             ->addFilters('floatval')
             ->addValidations(array(
                array('greater_than', 0.1, 'price 必须大于 0.1'),
                array('Form_Data::nullValidate', '空验证操作'),
                array('is_float', 'price 必须是一个浮点数'),
             ));

        return $form;
    }

    /**
     * @return Form_Data
     */
    static function createFromConfig($id = 'form1')
    {
        $form = new Form_Data($id);
        $form->loadFromConfigFile(dirname(__FILE__) . '/data_form.yaml', false);
        return $form;
    }

    static function nullValidate()
    {
        return true;
    }

}


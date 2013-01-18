<?php
// $Id: formatter.php 895 2010-03-23 05:36:29Z thinkgem $

/**
 * 自动格式化属性行为插件
 *
 * Example：
      'formatter' => array(
            'props' => array(
                'status' => array(
                    'formatter' => 'dict',
                    'dict' => array(
                        0 => '新节目',
                        1 => '待审核',
                        2 => '已发布',
                        3 => '打回',
                        4 => '删除',
                    ),
                ),
                'created' => array(
                    'formatter' => 'date',
                    'format' => 'Y-m-d',
                ),
                'description' => array(
                    'formatter' => 'nl2br',
                ),
            ),
 *
 */
class Model_Behavior_Formatter extends QDB_ActiveRecord_Behavior_Abstract
{
    protected $_settings = array
    (
        'props' => array(),
    );

    function __construct(QDB_ActiveRecord_Meta $meta, array $settings)
    {
        parent::__construct($meta, $settings);

        if(empty($this->_settings['props']) || ! is_array($this->_settings['props']))
        {
            throw new QException(__("Missing \"props\" parameter in \"%s\" Model", $meta->class_name));
        }

        foreach($this->_settings['props'] as $prop => & $_config)
        {
            if(! isset($_config['mapping_name']))
            {
                $_config['mapping_name'] = "{$prop}_formatted";
            }
            if(isset($this->_meta->prop[$_config['mapping_name']]))
            {
                throw new QException(__("\"%s\" prop has been set up, you must use another mapping name", $_config['mapping_name']));
            }
            if(! isset($_config['formatter']))
            {
                $_config['formatter'] = "serialize";
            }
            $_config['prop_name'] = $prop;

            $this->_meta->setPropGetter(
                $_config['mapping_name'],
                array("Model_Behavior_Formatter_{$_config['formatter']}", 'get'),
                $_config
            );

            $this->_meta->setPropSetter(
                $_config['mapping_name'],
                array("Model_Behavior_Formatter_{$_config['formatter']}", 'set'),
                $_config
            );
        }
    }

    function bind()
    {
    }


}
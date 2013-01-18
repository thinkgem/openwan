<?php return array (
  'expired' => 1358483326,
  'data' => 
  array (
    'mtime' => 1269322608,
    'yaml' => 
    array (
      '~form' => 
      array (
        'id' => 'category_form',
        'name' => 'category_form',
        'method' => 'post',
      ),
      'name' => 
      array (
        '_ui' => 'textbox',
        '_label' => '名称',
        '_filters' => 'trim',
        '_req' => true,
        '_tips' => '分类的名称',
        'maxlength' => 64,
        'class' => 'txt',
      ),
      'weight' => 
      array (
        '_ui' => 'textbox',
        '_filters' => 'intval',
        '_label' => '权重',
        '_req' => true,
        '_tips' => '值越大排序越靠前',
        'maxlength' => 64,
        'class' => 'txt',
      ),
      'enabled' => 
      array (
        '_ui' => 'radiogroup',
        '_filters' => 'intval',
        '_label' => '可用性',
        '_tips' => '指示该分类是否可用',
        'class' => 'radio',
        'items' => 
        array (
          1 => '可用',
          0 => '不可用',
        ),
        'value' => 1,
        'caption_class' => 'inline',
      ),
      'description' => 
      array (
        '_ui' => 'memo',
        '_label' => '描述',
        '_tips' => '说明信息',
        'maxlength' => 255,
        'cols' => 23,
        'rows' => 4,
        'class' => 'txt',
      ),
    ),
  ),
);
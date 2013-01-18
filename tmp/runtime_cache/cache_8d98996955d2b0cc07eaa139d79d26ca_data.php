<?php return array (
  'expired' => 1344259984,
  'data' => 
  array (
    'mtime' => 1269322608,
    'yaml' => 
    array (
      '~form' => 
      array (
        'id' => 'user_form',
        'name' => 'user_form',
        'method' => 'post',
      ),
      'username' => 
      array (
        '_ui' => 'textbox',
        '_label' => '用户名',
        '_filters' => 'trim',
        '_req' => true,
        '_tips' => '用户名长度只能在3-32位之间，有字母、数字和下划线组成',
        'maxlength' => 32,
        'class' => 'txt w200',
      ),
      'password' => 
      array (
        '_ui' => 'textbox',
        '_label' => '密码',
        '_filters' => 'trim',
        '_req' => true,
        '_tips' => '密码长度只能在4-32位之间',
        'maxlength' => 32,
        'class' => 'txt w200',
      ),
      'nickname' => 
      array (
        '_ui' => 'textbox',
        '_label' => '昵称',
        '_filters' => 'trim',
        '_req' => true,
        '_tips' => '',
        'maxlength' => 64,
        'class' => 'txt w200',
      ),
      'group_id' => 
      array (
        '_ui' => 'dropdownlist',
        '_label' => '用户组',
        '_tips' => '指示改用户所归属的用户组',
        '_req' => true,
        'class' => 'txt',
      ),
      'level_id' => 
      array (
        '_ui' => 'dropdownlist',
        '_label' => '秘密',
        '_tips' => '指示浏览等级',
        '_req' => true,
        'class' => 'txt',
      ),
      'sex' => 
      array (
        '_ui' => 'radiogroup',
        '_label' => '性别',
        '_filters' => 'intval',
        '_tips' => '',
        'class' => 'radio',
        'items' => 
        array (
          0 => '保密',
          1 => '男',
          2 => '女',
        ),
        'value' => 0,
        'caption_class' => 'inline',
      ),
      'birthday' => 
      array (
        '_ui' => 'textbox',
        '_label' => '生日',
        '_tips' => '',
        'maxlength' => 32,
        'class' => 'txt w200',
      ),
      'address' => 
      array (
        '_ui' => 'textbox',
        '_label' => '地址',
        '_tips' => '',
        'maxlength' => 64,
        'class' => 'txt w200',
      ),
      'email' => 
      array (
        '_ui' => 'textbox',
        '_label' => '电子邮箱',
        '_tips' => '',
        'maxlength' => 64,
        'class' => 'txt w200',
      ),
      'duty' => 
      array (
        '_ui' => 'textbox',
        '_label' => '职务',
        '_tips' => '',
        'maxlength' => 64,
        'class' => 'txt w200',
      ),
      'office_phone' => 
      array (
        '_ui' => 'textbox',
        '_label' => '办公电话',
        '_tips' => '',
        'maxlength' => 64,
        'class' => 'txt w200',
      ),
      'home_phone' => 
      array (
        '_ui' => 'textbox',
        '_label' => '家庭电话',
        '_tips' => '',
        'maxlength' => 64,
        'class' => 'txt w200',
      ),
      'mobile_phone' => 
      array (
        '_ui' => 'textbox',
        '_label' => '移动手机',
        '_tips' => '',
        'maxlength' => 64,
        'class' => 'txt w200',
      ),
      'description' => 
      array (
        '_ui' => 'memo',
        '_label' => '个人简介',
        '_tips' => '',
        'maxlength' => 255,
        'cols' => 23,
        'rows' => 5,
        'class' => 'txt w260',
      ),
      'enabled' => 
      array (
        '_ui' => 'radiogroup',
        '_filters' => 'intval',
        '_label' => '可用性',
        '_tips' => '指示该该用户是否允许登录使用',
        'class' => 'radio',
        'items' => 
        array (
          1 => '可用',
          0 => '不可用',
        ),
        'value' => 1,
        'caption_class' => 'inline',
      ),
    ),
  ),
);
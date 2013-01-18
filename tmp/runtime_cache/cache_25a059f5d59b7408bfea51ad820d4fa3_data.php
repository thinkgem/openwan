<?php return array (
  'expired' => 1281338338,
  'data' => 
  array (
    0 => 
    array (
      'id' => 
      array (
        'name' => 'id',
        'scale' => NULL,
        'type' => 'int',
        'length' => '11',
        'ptype' => 'int4',
        'not_null' => true,
        'pk' => true,
        'auto_incr' => false,
        'binary' => false,
        'unsigned' => false,
        'default' => NULL,
        'has_default' => NULL,
        'desc' => '计数器编号',
      ),
      'file_id' => 
      array (
        'name' => 'file_id',
        'scale' => NULL,
        'type' => 'int',
        'length' => '11',
        'ptype' => 'int4',
        'not_null' => true,
        'pk' => false,
        'auto_incr' => false,
        'binary' => false,
        'unsigned' => false,
        'default' => NULL,
        'has_default' => NULL,
        'desc' => '文件编号',
      ),
    ),
    1 => 
    array (
      'id' => true,
      'file_id' => true,
    ),
  ),
);
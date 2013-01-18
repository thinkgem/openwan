<?php return array (
  'expired' => 1358513971,
  'data' => 
  array (
    'admin' => 
    array (
      'type' => 'simple',
      'name' => 'admin',
      'pattern' => 'admin/:controller/:action/*',
      'pattern_parts' => 
      array (
        0 => 'static',
        1 => 'var',
        2 => 'var',
        3 => 'wildcard',
      ),
      'config' => 
      array (
        'controller' => '([a-z][a-z0-9]*)*',
        'action' => '([a-z][a-z0-9]*)*',
      ),
      'vars' => 
      array (
        1 => 'controller',
        2 => 'action',
      ),
      'varnames' => 
      array (
        'namespace' => 'admin',
        'controller' => 'default',
        'action' => 'index',
        'module' => 'default',
      ),
      'defaults' => 
      array (
        'namespace' => true,
        'controller' => true,
        'action' => true,
        'module' => true,
      ),
      'static_parts' => 
      array (
        0 => 'admin',
        3 => '*',
      ),
      'static_optional' => 
      array (
        0 => false,
        3 => false,
      ),
    ),
    '_default_' => 
    array (
      'type' => 'simple',
      'name' => '_default_',
      'pattern' => ':controller/:action/*',
      'pattern_parts' => 
      array (
        0 => 'var',
        1 => 'var',
        2 => 'wildcard',
      ),
      'config' => 
      array (
        'controller' => '([a-z][a-z0-9]*)*',
        'action' => '([a-z][a-z0-9]*)*',
      ),
      'vars' => 
      array (
        0 => 'controller',
        1 => 'action',
      ),
      'varnames' => 
      array (
        'controller' => 'default',
        'action' => 'index',
        'module' => 'default',
        'namespace' => 'default',
      ),
      'defaults' => 
      array (
        'controller' => true,
        'action' => true,
        'module' => true,
        'namespace' => true,
      ),
      'static_parts' => 
      array (
        2 => '*',
      ),
      'static_optional' => 
      array (
        2 => false,
      ),
    ),
  ),
);
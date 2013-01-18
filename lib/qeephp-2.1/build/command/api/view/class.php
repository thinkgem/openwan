<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('contents'); ?>

<div class="apidoc-class">

  <h1>类 - <?php echo $class->name; ?></h1>

  <ul class="nav clearfix">
    <li><a href="<?php echo $index_url; ?>">所有包</a></li>

    <?php if(!empty($class->properties)): ?>
    <li><a href="#properties">类属性</a></li>
    <?php endif; ?>

    <?php if(!empty($class->methods)): ?>
    <li><a href="#methods">类方法</a></li>
    <?php endif; ?>

    <li class="more"><a href="/docs/">阅读更多文档...</a></li>

  </ul>

  <?php $this->_element('class_summary', array('class' => $class)); ?>

  <a name="properties"></a>

  <?php
  $this->_element('property_summary', array(
      'class'     => $class, 
      'protected' => false
  ));

  $this->_element('property_summary', array(
      'class'     => $class, 
      'protected' => true
  ));
  ?>

  <a name="methods"></a>
  <?php
  $this->_element('method_summary', array(
      'class'     => $class,
      'protected' => false
  ));

  $this->_element('method_summary', array(
      'class'     => $class,
      'protected' => true
  ));

  $this->_element('property_details', array('class' => $class));

  $this->_element('method_details', array('class' => $class));
  ?>

</div>

<?php $this->_endblock(); ?>


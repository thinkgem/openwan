<?php if (!$class->native_properties_count) return; ?>

<div class="details">

  <h2>属性详细说明</h2>

  <?php foreach($class->properties as $property): ?>
  <?php if($property->is_inherited) continue; ?>

  <a name="<?php echo h($property->name); ?>"></a>

  <div class="name property">
    $<?php echo h($property->name); ?>

    <span class="tags">属性</span>
  </div>

  <div class="signature">
    <?php // echo $this->renderPropertySignature($property); ?>
  </div>

  <div class="formatted">
    <?php echo Command_API::formatting($property->description); ?>
  </div>

  <?php endforeach; ?>

</div>


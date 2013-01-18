
<h2>目录</h2>

<?php foreach ($book->chapters as $chapter): ?>

<h3><a href="<?php echo $book->url($chapter); ?>"><?php echo h($chapter->subject); ?></a></h3>

<?php if ($chapter->has_groups): ?>

<ul class="grouped-sections">

  <?php foreach ($chapter->groups as $group_name => $sections): ?>

  <li>
    <?php echo h($group_name); ?>:
  </li>

  <?php foreach ($sections as $section): ?>
  <li>
    <a href="<?php echo $book->url($section); ?>" subject="<?php echo h($section->subject); ?>"><?php echo h($section->subject); ?></a>
  </li>
  <?php endforeach; ?>

  <?php endforeach; ?>

</ul>

<?php else: ?>

<ul>
  <?php foreach ($chapter->sections as $section): ?>
  <li>
    <?php if ($section->group_name): echo h($section->group_name) . ': '; endif; ?><a href="<?php echo $book->url($section); ?>" subject="<?php echo h($section->subject); ?>"><?php echo h($section->subject); ?></a>
  </li>
  <?php endforeach; ?>

</ul>

<?php endif; ?>

<?php endforeach; ?>


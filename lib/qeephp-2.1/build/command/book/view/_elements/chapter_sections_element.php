
<?php
$book = $chapter->book;

?>

<ul class="sections">
  <?php foreach($chapter->sections as $section): ?>
  <li>
  <?php if ($section->has_contents): ?>
    <a href="<?php echo $book->url($section); ?>"><?php echo h($section->subject); ?></a>
  <?php else: ?>
    <s><?php echo h($section->subject); ?></s>
  <?php endif; ?>
  </li>
  <?php endforeach; ?>

</ul>


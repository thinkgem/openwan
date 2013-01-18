<?php $this->_extends('_layouts/chm_layout'); ?>

<?php $this->_block('contents'); ?>

<div class="guide-section">

  <div class="guide-header">
    <span class="nav">
      <a href="http://qeephp.com/docs/">文档索引</a>
      &raquo;
      <?php echo h($book->subject); ?>
    </span>
  </div>

  <div class="guide-section-details formatted">

    <?php echo Command_Book::formatting($book->contents); ?>

    <hr />

    <h2>目录</h2>

    <div id="columns-chapters">

      <?php $nu = 0; $all_nodes = array(); foreach($book->chapters as $chapter): $nu++; $all_nodes[] = $chapter; ?>

      <h3><a href="<?php echo $book->url($chapter); ?>"><?php echo $nu . '. ' . h($chapter->subject); ?></a></h3>

      <ul class="sections">
        <?php foreach($chapter->sections as $section): $all_nodes[] = $section; ?>
        <li>
        <?php if ($section->has_contents): ?>
          <a href="<?php echo $book->url($section); ?>"><?php echo h($section->subject); ?></a>
        <?php else: ?>
          <s><?php echo h($section->subject); ?></s>
        <?php endif; ?>
        </li>
        <?php endforeach; ?>

      </ul>

      <?php endforeach; ?>

    </div>

    <hr />

    <h2>最近更新</h2>

    <table width="100%" border="0" cellpadding="5">
      <tr>
<?php

$top10 = array();
foreach ($all_nodes as $offset => $node)
{
    $top10[$offset] = $node->last_modified;
}
arsort($top10, SORT_STRING);

$len = 0;
foreach ($top10 as $offset => $l):
    $node = $all_nodes[$offset];
?>

        <td>[<?php echo date('Y-m-d H:i', $node->last_modified); ?>] - <a href="<?php echo $book->url($node); ?>"><?php echo h($node->subject); ?></a></td>

<?php if ($len % 3 == 2): ?></tr><tr><?php endif; ?>
<?php $len++; ?>
<?php if ($len >= 15) { break; }; endforeach; ?>

      </tr>

    </table>

  </div>

</div>

<div class="hide">
<img src="css/table_data_bg.jpg" />
</div>

<?php $this->_endblock(); ?>


<?php $this->_extends('_layouts/index_layout'); ?>

<?php $this->_block('contents'); ?>

<div class="guide-section">

  <div class="guide-header">
    <span class="nav">
      <a href="/docs/">文档索引</a>
      &raquo;
      <?php echo h($book->subject); ?>
    </span>
  </div>

  <div class="guide-section-details formatted">

    <?php echo Command_Book::formatting($book->contents); ?>

    <hr />

    <h2>目录</h2>

    <div id="columns-chapters">

      <?php $i = 0; $nu = 0; $all_sections = array(); foreach($book->chapters as $chapter): $nu++; ?>

      <?php if ($i == 0): ?><div class="column-chapter-<?php echo $book->columns; ?> clearfix"><?php endif; ?>

      <div class="chapter-summary">

        <h3><a href="<?php echo $book->url($chapter); ?>"><?php echo $nu . '. ' . h($chapter->subject); ?></a></h3>

<?php if ($chapter->has_groups): $this->_element('chapter_grouped_sections', array('chapter' => $chapter)); else: $this->_element('chapter_sections', array('chapter' => $chapter)); endif; ?>

      </div>

      <?php $i++; ?>

      <?php if ($i == $book->columns): ?></div><?php $i = 0; endif; ?>

      <?php endforeach; ?>

      <?php if ($i): ?></div><?php endif; ?>

    </div>

    <hr />

    <h2>最近更新</h2>

    <table width="100%" border="0" cellpadding="5">
      <tr>
<?php

$top10 = array();
foreach ($book->sections as $offset => $section)
{
    $top10[$offset] = $section->last_modified;
}
arsort($top10, SORT_STRING);

$len = 0;
foreach ($top10 as $offset => $l):
    $section = $book->sections[$offset];
?>

        <td>[<?php echo date('Y-m-d H:i', $section->last_modified); ?>] - <a href="<?php echo $book->url($section); ?>"><?php echo h($section->subject); ?></a></td>

<?php if ($len % 3 == 2): ?></tr><tr><?php endif; ?>
<?php $len++; ?>
<?php if ($len >= 15) { break; }; endforeach; ?>

      </tr>

    </table>

  </div>

</div>

<?php $this->_endblock(); ?>


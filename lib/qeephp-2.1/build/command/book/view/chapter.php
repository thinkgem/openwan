<?php $this->_extends('_layouts/index_layout'); ?>

<?php $this->_block('contents'); ?>

<div class="guide-chapter">

  <div class="guide-header">
    <span class="nav">
      <a href="/docs/">文档索引</a>
      &raquo;
      <a href="<?php echo $book->url($book); ?>"><?php echo h($book->subject); ?></a>
      &raquo;
      <?php echo h($chapter->subject); ?>
    </span>
  </div>


  <div class="guide-section">
    <div class="guide-sidebar">
      <?php $this->_control('chapters', 'chapters', array('book' => $book)); ?>
    </div>

    <div class="guide-contents formatted">
        <?php echo Command_Book::formatting($chapter->contents); ?>
        <?php if (count($chapter->sections)): ?>
        <h4>本章内容列表</h4>
        <ul>
        <?php foreach ($chapter->sections as $section): ?>
          <li><a href="<?php echo $book->url($section); ?>"><?php echo h($section->subject); ?></a></li>
        <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>

    <div class="nofloat"></div>
  </div>

  <div class="guide-footer">

    <table border="0" width="100%">
      <tr>
        <td align="left" width="200">
          <?php if ($chapter->prev()): ?>
          &laquo;
          <a href="<?php echo $book->url($chapter->prev()); ?>"><?php echo h($chapter->prev()->subject); ?></a>
          <?php endif; ?>

        </td>

        <td align="center">
          <a href="<?php echo $book->url($book); ?>">返回索引页</a>
        </td>

        <td align="right" width="200">
          <?php if ($chapter->next()): ?>
          <a href="<?php echo $book->url($chapter->next()); ?>"><?php echo h($chapter->next()->subject); ?></a>
          &raquo;
          <?php endif; ?>
        </td>
      </tr>
    </table>

  </div>

</div>

<?php $this->_endblock(); ?>



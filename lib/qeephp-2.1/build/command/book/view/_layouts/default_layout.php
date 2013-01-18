
   <!-- 左侧栏 -->

    <div id="subpage_sidebar2" class="left guide-chapters-index">

      <?php $this->_control('chapters', 'chapters-nav', array('book' => $book));  ?>

    </div>

    <!-- /左侧栏 -->

    <!-- 右侧栏 -->

    <div id="col4" class="right contents">

      <?php $this->_block('contents'); ?><?php $this->_endblock(); ?>

    </div>

    <!-- /右侧栏 -->



<script type="text/javascript">
$(document).ready(function() {
    $("a.toggle").toggle(function(){
        $(this).text($(this).text().replace(/隐藏/,'显示'));
        var a=$(this).parents(".summary");
        a.find(".inherited").hide();
    },function(){
        $(this).text($(this).text().replace(/显示/,'隐藏'));
        $(this).parents(".summary").find(".inherited").show();
    });
});
</script>


   <!-- 左侧栏 -->

    <div id="subpage_sidebar" class="left apidoc-classes-index">

      <?php $this->_control('classes', 'classes-nav', array(
          'packages' => $packages,
          'class_url' => $class_url,
          'index_url' => $index_url,
      )); ?>

    </div>

    <!-- /左侧栏 -->

    <!-- 右侧栏 -->

    <div id="col3" class="right contents">

      <?php $this->_block('contents'); ?><?php $this->_endblock(); ?>

    </div>

    <!-- /右侧栏 -->


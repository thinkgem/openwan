
<!-- header -->

<script type="text/javascript">
$(document).ready(function() {
    $("a.toggle").toggle(function(){
        $(this).text($(this).text().replace(/Hide/,'Show'));
        var a=$(this).parents(".summary");
        a.find(".inherited").hide();
    },function(){
        $(this).text($(this).text().replace(/Show/,'Hide'));
        $(this).parents(".summary").find(".inherited").show();
    });
});
</script>

<!-- /header -->


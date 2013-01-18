<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('head'); ?>
  <script type="text/javascript">
        $(document).ready(function(){
            init();
        });
        function init(){
            leftNav(/leftNav\(['"](.+?)['"],(\s)this\);/.exec($('#leftNav li[class=current] a').attr('onclick'))[1]);
        }
        function leftNav(url, e){
            $(e).parent().parent().children().removeClass('current');
            $(e).parent().addClass('current');
            loading('#ajax');
            $.get(url, null, function(data){
                $('#ajax').html(data);
            });
        }
    </script>
<?php $this->_endblock(); ?>

<?php $this->_block('contents'); ?>

    <div class="w200 left">
        <div class="box">
            <div class="p10">
                <h2 class="t3"><?php echo $pathway->getTitle();?></h2>
            </div>
        </div>
        <div class="box mt10">
            <div class="p10">
                <ul class="ul" id="leftNav">
                    <?php foreach(Q::ini('appini/nav/left/dataManager') as $key => $nav): if ($_app->authorizedUDI($currentUser['roles'], $nav['udi'])):?>
                        <li<?php echo $key == 0 ? ' class="current"':'';?>><a href="javascript:" onclick="leftNav('<?php echo url($nav['udi']);?>', this);"><?php echo $nav['title'];?></a></li>
                    <?php endif;endforeach;?>
                </ul>
            </div>
        </div>
    </div>
    <div class="w770 left ml10" id="ajax"></div>
    
<?php $this->_endblock(); ?>


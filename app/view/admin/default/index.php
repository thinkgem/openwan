<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('head'); ?>
    <script type="text/javascript">        
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
                    <ul class="ul">
                        <?php foreach(Q::ini('appini/nav/top') as $nav): if ($_app->authorizedUDI($currentUser['roles'],$nav['udi'])):?>
                            <li><a href="<?php echo url($nav['udi']);?>"><?php echo $nav['title'];?></a></li>
                        <?php endif;endforeach;?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="w770 left ml10 hide">
            <div class="box">
                <div class="p10">                    
                    <?php dump($currentUser);?>
                    <table class="tb">
                        <thead><tr><td>快速入门</td></tr></thead>
                        <tfoot>
                            <tr><td></td></tr>
                        </tfoot>
                        <tbody>
                            <tr><td>1.素材上传</td></tr>
                            <tr><td>&nbsp&nbsp&nbsp</td></tr>
                            <tr><td>2.视频编目</td></tr>
                            <tr><td>&nbsp&nbsp&nbsp</td></tr>
                            <tr><td>3.审核发布</td></tr>
                            <tr><td>&nbsp&nbsp&nbsp</td></tr>
                            <tr><td>4.检索下载</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        

<?php $this->_endblock(); ?>

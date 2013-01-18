<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('head'); ?>
    <script type="text/javascript" src="<?php echo $_BASE_DIR;?>js/flowplayer/flowplayer-3.1.4.min.js"></script>
    <script type="text/javascript">
        function category(id){
            $('#category_id').val(id);
            $('#category_form').submit();
        }
        function putout(id){
            $('#list').hide();
            $('#view').show();
            loading('#view');
            $.get('<?php echo url('admin::fileputout/putout');?>', {id:id}, function(data){
                $('#view').html(data);
            });
        }
    </script>
<?php $this->_endblock(); ?>

<?php $this->_block('contents'); ?>

        <form id="category_form" action="<?php echo url("{$_ctx->namespace}::{$_ctx->controller_name}/{$_ctx->action_name}");?>"><input type="hidden" name="type" value="<?php echo $type;?>"/><input type="hidden" name="category_id" id="category_id"/></form>

        <div class="w200 left">
            <div class="box">
                <div class="p10">
                    <h2 class="t3"><?php echo $pathway->getTitle();?></h2>
                </div>
            </div>
            <div class="box mt10">
                <div class="p10">
                    <ul class="ul" id="leftNav">
                        <?php foreach(Q::ini('appini/nav/left/filePutout') as $key => $nav): if ($_app->authorizedUDI($currentUser['roles'], $nav['udi'])):?>
                        <li<?php echo "type/$type" == (isset($nav['args'])?$nav['args']:'') ? ' class="current"':'';?>><a href="<?php echo url($nav['udi'], isset($nav['args'])?$nav['args'].'/category_id/'.$category->id:'');?>"><?php echo $nav['title'];?></a></li>
                        <?php endif;endforeach;?>
                    </ul>
                </div>
            </div>
            <?php $this->_control('category', 'category', array('title' => $pathway->getTitle()));?>
        </div>
        <div id="list">
            <div class="w428 left ml10">
                <div class="box">
                    <div class="t1">
                        <?php echo $pathway->getPathway();?>
                    </div>
                    <div class="p10">
                        <table class="tb">
                            <thead><tr><td nowrap="true">名称</td><td nowrap="true">类型</td><td nowrap="true">状态</td><td nowrap="true">时间</td><td nowrap="true">操作</td></thead>
                            <?php if(isset($files) && count($files) > 0):?>
                            <tfoot><tr><td colspan="5"><?php $this->_control('pagination', 'pagination', array('pagination' => $pagination, 'url_args' => array('type' => $type,'category_id' => $category->id))); ?></td></tr></tfoot>
                            <?php endif;?>
                            <tbody>
                                <?php if(!isset($files) || count($files) <= 0):?>
                                <tr><td colspan="5">没有内容</td></tr>
                                <?php else:?>
                                    <?php foreach($files as $v):?>
                                <tr style="cursor:pointer;" onclick="preview(this, '<?php echo url('admin::filecatalog/preview', array('id'=>$v->id));?>', '<?php echo $v->title;?>');" ondblclick="putout(<?php echo $v->id?>);">
                                    <td title="<?php echo $v->title;//path;?>"><?php echo Helper_Util::substr($v->title,16);?></td>
                                    <td nowrap="true"><?php echo $v->ext;?></td>
                                    <td nowrap="true"><?php echo $v->status_formatted;?></td>
                                    <td nowrap="true"><?php echo $v->upload_at_formatted;?></td>
                                    <td nowrap="true"><a href="javascript:" onclick="putout(<?php echo $v->id?>);">审核</a></td>
                                </tr>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="w332 left ml10">
                <div class="box">
                    <div class="t2">
                        文件预览
                    </div>
                    <div class="p5">
                        <?php $this->_element('preview'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="view" class="w770 left ml10 hide"></div>

<?php $this->_endblock(); ?>


<!--

                            <tr><td class="lbl" nowrap="true" colspan="4" style="font-size:14px">编目属性</td></tr>
                            <tr>
                                <td class="lbl tr" nowrap="true">允许下载</td><td>
                                    <input type="radio" class="radio" value="1" id="is_download_1" name="is_download"><label class="inline" for="is_download_1">&nbsp;是&nbsp;&nbsp;&nbsp;</label>
                                    <input type="radio" class="radio" value="0" id="is_download_2" name="is_download"><label class="inline" for="is_download_2">&nbsp;否&nbsp;&nbsp;&nbsp;</label>
                                </td>
                                <td class="lbl tr" nowrap="true">浏览等级</td><td><select class="txt"><option value=""></option></select></td>
                            </tr>
                            <tr><td class="lbl" nowrap="true" colspan="4" style="font-size:14px">可浏览的用户组:</td></tr>
                            <tr><td class="lbl" nowrap="true" colspan="4">用户组树</td></tr>

-->
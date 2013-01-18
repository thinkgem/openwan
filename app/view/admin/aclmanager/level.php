        <div class="w770 left" id="levelList">
            <div class="box">
                <div class="t1">
                    <?php echo $pathway->getPathway();?>
                </div>
                <div class="p10 pt0">
                    <form id="level_form" name="level_form" action="<?php echo url('admin::aclmanager/level');?>" method="post">
                        <table class="tb">
                            <thead><tr><td>删除</td><td>名称</td><td>说明</td><td>秘密数字</td><td>可用性</td><td>操作</td></tr></thead>
                            <tfoot>
                                <tr><td><input type="checkbox" name="chk" id="chkall" class="tm"/>&nbsp;<label for="chkall" class="pointer">删?</label></td><td colspan="6"><table width="100%"><tfoot><tr><td><input class="btn" type="submit" value="提交"/></td><td><?php $this->_control('pagination', 'pagination', array('pagination' => $pagination, 'show_count' => true)); ?></td></tr></tfoot></table></td></tr>
                            </tfoot>
                            <tbody id="tbody">
                                <?php foreach ($levels as $v):?>
                                <tr><td><?php if($v->id>5):?><input type="checkbox" name="chk[]" value="<?php echo $v->id;?>" class="tm"/><?php endif;?></td><td><input type="hidden" name="id[]" value="<?php echo $v->id;?>"/><input type="text" name="name[]" value="<?php echo $v->name;?>" class="txt w150"/></td><td><input type="text" name="description[]" value="<?php echo $v->description;?>" class="txt w200"/></td><td><input type="text" name="weight[]" value="<?php echo $v->weight;?>" class="txt w70"/></td><td><select name="enabled[]" class="txt w70"><option value="1"<?php echo $v->enabled==1?' selected':'';?>>可用</option><option value="0"<?php echo $v->enabled==0?' selected':'';?>>不可用</option></select></td><td><a href="javascript:" onclick="levelView(<?php echo $v->id;?>)">详情</a>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
                                <?php endforeach;?>
                            </tbody>
                            <tbody>
                                <tr><td></td><td colspan="5">&nbsp;<a href="javascript:" onclick="levelAdd()" class="add">添加用户浏览等级</a></td></tr>
                            </tbody>
                        </table>
                    </form>
                    <script type="text/javascript">//<!--
                        $(function() {
                            $('#pagination a').each(function(){
                                $(this).click(function(){
                                    loading('#ajax');
                                    $.get($(this).attr('href'), null, function(data){
                                       $('#ajax').html(data);
                                    });
                                    $(this).attr('href','javascript:');
                                });
                            });
                            $("#chkall").click(function() {
                                if ($(this).attr("checked") == true) { // 全选
                                    $("input[name='chk[]']").each(function() {
                                        $(this).attr("checked", true);
                                    });
                                } else { // 取消全选
                                    $("input[name='chk[]']").each(function() {
                                        $(this).attr("checked", false);
                                    });
                                }
                            });
                            $('#level_form').bind('submit', function() {
                                if(confirm('确认提交修改吗？')){
                                    loading('#ajax');
                                   $(this).ajaxSubmit(function(data){$('#ajax').html(data);});
                                }
                                return false;
                            });
                        });
                        function levelAdd() {
                            $('#tbody').append('<tr><td></td><td><input type="hidden" name="id[]" value=""/><input type="text" name="name[]" value="" class="txt w150"/>*</td><td><input type="text" name="description[]" value="" class="txt w200"/>*</td><td><input type="text" name="weight[]" value="0" class="txt w70"/></td><td><select name="enabled[]" class="txt w70"><option value="1" selected>可用</option><option value="0">不可用</option></select></td><td></td></tr>');
                        }
                        function levelView(id) {
                            $('#levelList').hide();
                            $('#levelView').show();
                            loading("#levelView");
                            $.get('<?php echo url('admin::aclmanager/levelView');?>', {id:id}, function(data){
                                $('#levelView').html(data);
                            });
                        }
                    //--></script>
                </div>
            </div>
        </div>
        <div class="w770 left hide" id="levelView"></div>
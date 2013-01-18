        <div class="w770 left" id="roleList">
            <div class="box">
                <div class="t1"><?php echo $pathway->getPathway();?></div>
                <div class="p10 pt0">
                    <form id="role_form" name="role_form" action="<?php echo url('admin::aclmanager/role');?>" method="post">
                        <table class="tb">
                            <thead><tr><td>删除</td><td>名称</td><td>说明</td><td>权重</td><td>可用性</td><td>操作</td></tr></thead>
                            <tfoot>
                                <tr><td><input type="checkbox" name="chk" id="chkall" class="tm"/>&nbsp;<label for="chkall" class="pointer">删?</label></td><td colspan="6"><table width="100%"><tfoot><tr><td><input class="btn" type="submit" value="提交"/></td><td><?php $this->_control('pagination', 'pagination', array('pagination' => $pagination, 'show_count' => true)); ?></td></tr></tfoot></table></td></tr>
                            </tfoot>
                            <tbody id="tbody">
                                <?php foreach ($roles as $v):?>
                                <tr><td><?php if($v->id>6):?><input type="checkbox" name="chk[]" value="<?php echo $v->id;?>" class="tm"/><?php endif;?></td><td><input type="hidden" name="id[]" value="<?php echo $v->id;?>"/><input type="text" name="name[]" value="<?php echo $v->name;?>" class="txt w150"<?php if($v->id<=6):?> readonly="true"<?php endif;?>/></td><td><input type="text" name="description[]" value="<?php echo $v->description;?>" class="txt w200"<?php if($v->id<=6):?> readonly="true"<?php endif;?>/></td><td><input type="text" name="weight[]" value="<?php echo $v->weight;?>" class="txt w70"/></td><td><select name="enabled[]" class="txt w70"><option value="1"<?php echo $v->enabled==1?' selected':'';?>>可用</option><?php if($v->id!=1):?><option value="0"<?php echo $v->enabled==0?' selected':'';?>>不可用</option><?php endif?></select></td><td><a href="javascript:" onclick="roleView(<?php echo $v->id;?>)">详情</a>&nbsp;&nbsp;<a href="javascript:" <?php if($v->id!=1):?>onclick="roleBind(<?php echo $v->id;?>)">绑定权限</a><?php endif;?>&nbsp;&nbsp;</td></tr>
                                <?php endforeach;?>
                            </tbody>
                            <tbody>
                                <tr><td></td><td colspan="5">&nbsp;<a href="javascript:" onclick="roleAdd()" class="add">添加角色</a>&nbsp;&nbsp;&nbsp;&nbsp;<a style="color:#e33;font-weight:normal;" href="javascript:" onclick="$.get('<?php echo url('admin::aclmanager/MakeAclFile');?>','',function(data){if(data !=null && data.length<=20)alert(data);else alert('没有权限！');});">更新权限文件</a></td></tr>
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
                            $('#role_form').bind('submit', function() {
                                if(confirm('确认提交修改吗？')){
                                    loading('#ajax');
                                    $(this).ajaxSubmit(function(data){$('#ajax').html(data);});
                                    if(confirm('修改成功，是否更新权限文件？')){
                                        $.get('<?php echo url('admin::aclmanager/MakeAclFile');?>','',function(data){
                                            if(data !=null && data.length<=20)alert(data);else alert('没有权限！');
                                        });
                                    }
                                }
                                return false;
                            });
                        });
                        function roleAdd() {
                            $('#tbody').append('<tr><td></td><td><input type="hidden" name="id[]" value=""/><input type="text" name="name[]" value="" class="txt w150"/>*</td><td><input type="text" name="description[]" value="" class="txt w200"/>*</td><td><input type="text" name="weight[]" value="0" class="txt w70"/></td><td><select name="enabled[]" class="txt w70"><option value="1" selected>可用</option><option value="0">不可用</option></select></td><td></td></tr>');
                        }
                        function roleView(id) {
                            $('#roleList').hide();
                            $('#roleView').show();
                            loading("#roleView");
                            $.get('<?php echo url('admin::aclmanager/roleView');?>', {id:id}, function(data){
                                $('#roleView').html(data);
                            });
                        }
                        function roleBind(id) {
                            $('#roleList').hide();
                            $('#roleView').show();
                            loading("#roleView");
                            $.get('<?php echo url('admin::aclmanager/roleBind');?>', {id:id}, function(data){
                                $('#roleView').html(data);
                            });
                        }
                    //--></script>
                </div>
            </div>
        </div>
        <div class="w770 left hide" id="roleView"></div>
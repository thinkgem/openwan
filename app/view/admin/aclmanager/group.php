        <div class="w770 left" id="groupList">
            <div class="box">
                <div class="t1"><?php echo $pathway->getPathway();?></div>
                <div class="p10 pt0">
                    <form id="group_form" name="group_form" action="<?php echo url('admin::aclmanager/group');?>" method="post">
                        <table class="tb">
                            <thead><tr><td>删除</td><td>名称</td><td>说明</td><td>用户磁盘配额</td><td>权重</td><td>可用性</td><td>操作</td></tr></thead>
                            <tfoot>
                                <tr><td><input type="checkbox" name="chk" id="chkall" class="tm"/>&nbsp;<label for="chkall" class="pointer">删?</label></td><td colspan="6"><table width="100%"><tfoot><tr><td><input class="btn" type="submit" value="提交"/></td><td><?php $this->_control('pagination', 'pagination', array('pagination' => $pagination, 'show_count' => true)); ?></td></tr></tfoot></table></td></tr>
                            </tfoot>
                            <tbody id="tbody">
                                <?php foreach ($groups as $v):?>
                                <tr><td><?php if($v->id>3):?><input type="checkbox" name="chk[]" value="<?php echo $v->id;?>" class="tm"/><?php endif;?></td><td><input type="hidden" name="id[]" value="<?php echo $v->id;?>"/><input type="text" name="name[]" value="<?php echo $v->name;?>" class="txt w100"<?php if($v->id==1):?> readonly="true"<?php endif;?>/></td><td><input type="text" name="description[]" value="<?php echo $v->description;?>" class="txt w150"/></td><td><input type="text" name="quota[]" value="<?php echo $v->quota;?>" class="txt w70"/> MB</td><td><input type="text" name="weight[]" value="<?php echo $v->weight;?>" class="txt w70"/></td><td><select name="enabled[]" class="txt w70"><option value="1"<?php echo $v->enabled==1?' selected':'';?>>可用</option><?php if($v->id>1):?><option value="0"<?php echo $v->enabled==0?' selected':'';?>>不可用</option><?php endif;?></select></td><td><a href="javascript:" onclick="groupView(<?php echo $v->id;?>)">详情</a>&nbsp;&nbsp;<?php if($v->id>1):?><a href="javascript:" onclick="groupBind(<?php echo $v->id;?>)">绑定分类/角色</a>&nbsp;&nbsp;<?php endif;?></td></tr>
                                <?php endforeach;?>
                            </tbody>
                            <tbody>
                                <tr><td></td><td colspan="5">&nbsp;<a href="javascript:" onclick="groupAdd()" class="add">添加用户组</a></td></tr>
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
                            $('#group_form').bind('submit', function() {
                                if(confirm('确认提交修改吗？')){
                                    loading('#ajax');
                                    $(this).ajaxSubmit(function(data){$('#ajax').html(data);});
                                }
                                return false;
                            });
                        });
                        function groupAdd() {
                            $('#tbody').append('<tr><td></td><td><input type="hidden" name="id[]" value=""/><input type="text" name="name[]" value="" class="txt w100"/>*</td><td><input type="text" name="description[]" value="" class="txt w150"/></td><td><input type="text" name="quota[]" value="1000" class="txt w70"/> MB</td><td><input type="text" name="weight[]" value="0" class="txt w70"/></td><td><select name="enabled[]" class="txt w70"><option value="1" selected>可用</option><option value="0">不可用</option></select></td><td></td></tr>');
                        }
                        function groupView(id) {
                            $('#groupList').hide();
                            $('#groupView').show();
                            loading("#groupView");
                            $.get('<?php echo url('admin::aclmanager/groupView');?>', {id:id}, function(data){
                                $('#groupView').html(data);
                            });
                        }
                        function groupBind(id) {
                            $('#groupList').hide();
                            $('#groupView').show();
                            loading("#groupView");
                            $.get('<?php echo url('admin::aclmanager/groupBind');?>', {id:id}, function(data){
                                $('#groupView').html(data);
                            });
                        }
                    //--></script>
                </div>
            </div>
        </div>
        <div class="w770 left hide" id="groupView"></div>
        <div class="w770 left" id="userList">
            <div class="box">
                <div class="t1">
                    <?php echo $pathway->getPathway();?>
                </div>
                <div class="p10 pt0">
                <?php if(!isset($users)):?>
                    <form id="user_form" name="user_form" action="<?php echo url('admin::aclmanager/user');?>" method="post">
                    <table class="tb">
                        <thead><tr><td colspan="3">搜索用户</td></tr></thead>
                        <tfoot>
                            <tr><td colspan="3"><input type="submit" value="提交" class="btn"/></td></tr>
                        </tfoot>
                        <tbody>
                            <tr><td><label class="lbl">用户名：</label></td><td>
                                    <input type="text" name="username" class="txt w200"/>
                                </td><td><span class="desc">可使用通配符 *，多个用户名用半角逗号“,”分隔</span></td></tr>
                            <tr><td><label class="lbl">昵称：</label></td><td>
                                    <input type="text" name="nickname" class="txt w200"/>
                                </td><td><span class="desc">可使用通配符 *，多个用户名用半角逗号“,”分隔</span></td></tr>
                            <tr><td><label class="lbl">用户组：</label></td><td>
                                    <select size="9" multiple="multiple" name="groups[]" class="txt w260">
                                        <option value="all">无限制</option>
                                    <?php foreach($groups as $v):?>
                                        <option value="<?php echo $v->id;?>"><?php echo $v->name;?></option>
                                    <?php endforeach;?>
                                    </select>
                                </td><td><span class="desc">设置允许参与搜索的用户组，可以按住 CTRL 多选</span></td></tr>
                            <tr><td><label class="lbl">秘密：</label></td><td>
                                    <select size="9" multiple="multiple" name="levels[]" class="txt w260">
                                        <option value="all">无限制</option>
                                    <?php foreach($levels as $v):?>
                                        <option value="<?php echo $v->id;?>"><?php echo $v->name;?></option>
                                    <?php endforeach;?>
                                    </select>
                                </td><td><span class="desc">设置允许参与搜索的秘密，可以按住 CTRL 多选</span></td></tr>
                        </tbody>
                    </table>
                    </form>
                    <script type="text/javascript">//<!--
                        $(function() {
                            $('#user_form').bind('submit', function() {
                                loading('#ajax');
                                $(this).ajaxSubmit(function(data){$('#ajax').html(data);});
                                return false;
                            });
                        });
                    //--></script>
                <?php else:?>
                    <table class="tb">
                    <?php if(count($users)==0):?>
                        <tfoot>
                            <tr><td colspan="6" align="center" valign="middle" height="50">没搜索到用户 <a href="javascript:" onclick="user();">重新搜索</a></td></tr>
                        </tfoot>
                    <?php else:?>
                        <thead><tr><td>编号</td><td>名称</td><td>昵称</td><td>用户组</td><td>秘密</td><td>可用性</td><td>操作</td></tr></thead>                        
                        <tbody>
                            <?php foreach ($users as $v):?>
                            <tr><td><?php echo $v->id;?></td><td><?php echo $v->username;?></td><td><?php echo $v->nickname;?></td><td><?php echo $v->group->name?></td><td><?php echo $v->level->name?></td><td><?php echo $v->enabled==1?'可用':'不可用';?></td><td><a href="javascript:" onclick="userEdit(<?php echo $v->id;?>)">详情/编辑</a>&nbsp;&nbsp;<a href="javascript:" onclick="userDel(<?php echo $v->id;?>,this)">删除</a>&nbsp;&nbsp;</td></tr>
                            <?php endforeach;?>
                        </tbody>
                        <tfoot>
                            <tr><td colspan="6"><?php $this->_control('pagination', 'pagination', array('pagination' => $pagination, 'show_count' => true)); ?></td></tr>
                        </tfoot>
                    <?php endif;?>
                    </table>
                    <script type="text/javascript">//<!--
                        $(function() {
                            $('#pagination a').each(function(){
                                $(this).click(function(){
                                    loading('#ajax');
                                    $.post($(this).attr('href'), null, function(data){
                                       $('#ajax').html(data);
                                    });
                                    $(this).attr('href','javascript:');
                                });
                            });
                        });
                        function userEdit(id) {
                            $('#userList').hide();
                            $('#userView').show();
                            loading("#userView");
                            $.get('<?php echo url('admin::aclmanager/userEdit');?>', {id:id}, function(data){
                                $('#userView').html(data);
                            });
                        }
                        function userDel(id,e) {
                            if(confirm('确认要删除该用户吗？')){
                                $('#userList').hide();
                                $('#userView').show();
                                loading("#userView");
                                $.get('<?php echo url('admin::aclmanager/userDel');?>', {id:id}, function(data){
                                    if(data=='true'){
                                        alert('删除成功!');
                                        $(e).parent().parent().remove();
                                    }else{
                                        alert('删除失败！\n如下原因：\n1.你删除的为根用户\n2.你删除的为当前用户');
                                    }
                                    $('#userView').hide();
                                    $('#userList').show();
                                });
                            }
                        }
                    //--></script>
                <?php endif;?>
                </div>
            </div>
        </div>
        <div class="w770 left hide" id="userView"></div>
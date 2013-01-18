            <div class="box">
                <div class="t1">
                    <span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:" onclick="roleList()">返回列表</a><?php echo $pathway->getPathway();?>
                </div>
                <div class="p10 pt0">
                    <table class="tb">
                        <thead><tr><td>名称： <?php echo $role['name']?>&nbsp;&nbsp;&nbsp;&nbsp;说明： <?php echo $role['description']?>&nbsp;&nbsp;&nbsp;&nbsp;<a style="color:#e33;font-weight:normal;" href="javascript:" onclick="$.get('<?php echo url('admin::aclmanager/MakeAclFile');?>','',function(data){if(data !=null && data.length<=20)alert(data);else alert('没有权限！');});">更新权限文件</a><td></tr></thead>
                    </table>
                    <script type="text/javascript">
                        var data={};
                        data['-1_root'] = 'text: 绑定权限树;'
                    <?php
                        if (!empty($permissions)){
                            foreach ($permissions as $key => $value){
                                // namespace 级别
                                echo "data['root_{$key}'] = 'text: {$key}; url: javascript:;'\n";
                                foreach ($value as $key2 => $value2 ){
                                    // controller 级别
                                    echo "data['{$key}_{$key}{$key2}'] = 'text: {$key2}; url: javascript:;'\n";
                                    foreach ($value2 as $key3 => $value3){
                                        // action 级别
                                        $checked = in_array($value3['id'],$permission_ids)?'true':'false';
                                        $rbac = $value3['rbac'] == 'ACL_EVERYONE' ? '任何用户' : $value3['rbac'] == 'ACL_HAS_ROLE' ? '任何角色用户' : $value3['rbac'] == 'ACL_NO_ROLE' ? '没有角色用户' : '无';
                                        echo "data['{$key}{$key2}_{$value3['id']}'] = 'text: {$key3} | {$value3['aliasname']} | {$rbac}; checked: {$checked}; url: javascript:;'\n";
                                    }
                                }
                            }
                        }
                    ?>
                        var tree = new MzTreeView(data);
                        tree.autoFocused = false;
                        tree.useCheckbox = true;
                        tree.linkFocus = false;
                        tree.linkCheckbox = true;
                        document.getElementById('tree').innerHTML = tree.render();
                        //tree.expandLevel(1);
                        tree.expandAll('root');
                        function submit(){
                            if(confirm('确认提交修改吗？')){
                                loading('#roleView');
                                var s = '', nodes = tree.nodes;
                                for(var i in nodes) {
                                    if(nodes[i].checked){
                                        if (/^\d+$/.test(nodes[i].id))
                                            s += nodes[i].id + ',' ;
                                    }
                                }
                                $.post('<?php echo url('aclmanager/rolebind')?>', {id: '<?php echo $role['id'];?>', permission_ids: s}, function(data){$('#roleView').html(data);});
                                if(confirm('修改成功，是否更新权限文件？')){
                                    $.get('<?php echo url('admin::aclmanager/MakeAclFile');?>','',function(data){
                                        if(data !=null && data.length<=20)alert(data);else alert('没有权限！');
                                    });
                                }
                            }
                        }
                        function roleList() {
                            $('#roleList').show();
                            $('#roleView').hide();
                        }
                    //--></script>
                    <div id="tree" style="margin:10px;overflow-y:hidden;overflow-x:auto;*overflow-x:scroll;"></div>
                    <table class="tb">
                        <tfoot><tr><td colspan="4"><input class="btn" type="button" value="提交" onclick="submit()"/><input class="btn" type="button" value="返回列表" onclick="roleList()"/></td></tr></tfoot>
                    </table>
                </div>
            </div>
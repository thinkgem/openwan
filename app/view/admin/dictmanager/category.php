        <div class="w770 left" id="treeview">
            <div class="box">
                <div class="t1">
                    <span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:tree.collapseAll('1');tree.expandLevel(1)">折叠</a><span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:tree.expandAll('1');">展开</a><?php echo $pathway->getPathway();?>
                </div>
                <div class="p10">
                    <script type="text/javascript">//<!--
                        function addNode(id){
                            $('#treeview').removeClass('w770').addClass('w380');
                            loading('#edit');
                            $.get('<?php echo url('admin::dictmanager/categoryAdd');?>', {id:id}, function(data){
                                $('#edit').html(data);
                            });
                        }
                        function editNode(id){
                            $('#treeview').removeClass('w770').addClass('w380');
                            loading('#edit');
                            $.get('<?php echo url('admin::dictmanager/categoryEdit');?>', {id:id}, function(data){
                                try{
                                    data = eval('('+data+')');
                                    alert(data.msg);
                                    init();
                                }catch(e){
                                    $('#edit').html(data);
                                }
                            });
                        }
                        function delNode(id){
                            if(confirm('确认要删除该分类以及所有子分类吗？')){
                                $('#edit').html('');
                                $('#treeview').removeClass('w380').addClass('w770');
                                $.get('<?php echo url('admin::dictmanager/categoryDel');?>', {id:id}, function(data){
                                    if(data == 'true'){
                                        alert('删除成功！');
                                        init();
                                    }else{
                                        alert(data);
                                    }
                                });
                            }
                        }
                        var data={};
                    <?php foreach ($category as $v): $path_count = count(Q::normalize($v->path));?>
                        data['<?php echo $v->parent_id?>_<?php echo $v->id?>'] = 'text: <?php echo str_replace(';', '%3B', str_replace('%', '%25', $v->name));?> <a style="color:#1B5AA2%3B" href="javascript:addNode(<?php echo $v->id?>)%3B">添加子分类</a><?php if($path_count>1):?> <a style="color:#1B5AA2%3B" href="javascript:delNode(<?php echo $v->id?>)%3B">删除</a><?php endif;?>; hint: <?php echo str_replace('%', '%25', str_replace(';', '%3B', $v->description!=''?$v->description:$v->name));?>; url: javascript:editNode(<?php echo $v->id?>);';
                    <?php endforeach;?>
                        var tree = new MzTreeView(data);
                        document.getElementById('tree').innerHTML = tree.render();
                        tree.expandLevel(1);
                    //--></script>
                    <div id="tree" style="overflow-y:hidden;overflow-x:auto;*overflow-x:scroll;"></div>
                </div>
            </div>
        </div>
        <div class="w380 left ml10" id="edit"></div>
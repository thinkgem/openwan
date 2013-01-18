        <div class="w770 left" id="treeview">
            <div class="box">
                <div class="t1">
                    <span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:tree.collapseAll('1');tree.expandLevel(1)">折叠</a><span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:tree.expandAll('1');">展开</a><?php echo $pathway->getPathway();?>
                </div>
                <div class="p10" id="data">
                    <script type="text/javascript">//<!--
                        var data={};
                    <?php foreach ($category as $v): $path_count = count(Q::normalize($v->path));?>
                        data['<?php echo $v->parent_id?>_<?php echo $v->id?>'] = 'text: <?php echo str_replace(';', '%3B', str_replace('%', '%25', $v->name));?>';
                    <?php endforeach;?>
                        var tree = new MzTreeView(data);
                        tree.autoFocused = false;
                        tree.useCheckbox = true;
                        tree.linkFocus = false;
                        tree.linkCheckbox = true;
                        tree.isParentCheckbox = true;
                        document.getElementById('tree').innerHTML = tree.render();
                        tree.expandAll('1');

                        function getChecked(tree){
                            var s = '', nodes = tree.nodes;
                            for(var i in nodes) {
                                if(nodes[i].checked){
                                    if (/^\d+$/.test(nodes[i].id))
                                        s += nodes[i].id + ',' ;
                                }
                            }
                            return s;
                        }
                        function migration(isOut){
                            if(confirm('确认'+(isOut?'迁出':'迁入')+'选择的分类数据吗？')){
                                loading('#data');
                                var category_ids = getChecked(tree);
                                var url = isOut ? '<?php echo url('datamanager/migrationOut')?>' : '<?php echo url('datamanager/migrationIn')?>';
                                $.post(url, {category_ids: category_ids}, function(data){$('#data').html(data);});
                            }
                        }
                    //--></script>
                    <div id="tree" style="overflow-y:hidden;overflow-x:auto;*overflow-x:scroll;"></div>
                    <table class="tb">
                        <tfoot><tr><td colspan="4"><input class="btn" type="button" value="迁出" onclick="migration(true)"/><input class="btn" type="button" value="迁入" onclick="migration(false)"/></td></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>
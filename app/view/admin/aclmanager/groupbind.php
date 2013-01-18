            <div class="box">
                <div class="t1">
                    <span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:" onclick="groupList()">返回列表</a><?php echo $pathway->getPathway();?>
                </div>
                <div class="p10 pt0">
                    <table class="tb">
                        <thead><tr><td>名称： <?php echo $group->name;?>&nbsp;&nbsp;&nbsp;&nbsp;说明： <?php echo $group->description;?><td></tr></thead>
                    </table>
                    <script type="text/javascript">//<!--
                        var datac={};
                        <?php
                            foreach ($category as $v){
                                $checked = in_array($v->id,$category_ids)?'true':'false';
                                echo "datac['{$v->parent_id}_{$v->id}'] = 'text:{$v->name}; checked: {$checked}; url: javascript:;'\n";
                            }
                        ?>
                        var treec = new MzTreeView(datac);
                        treec.autoFocused = false;
                        treec.useCheckbox = true;
                        treec.linkFocus = false;
                        treec.linkCheckbox = true;
                        treec.isParentCheckbox = true;
                        document.getElementById('treec').innerHTML = treec.render();
                        //treec.expandLevel(2);
                        treec.expandAll('1');

                        var datar={};
                        datar['-1_root'] = 'text: 绑定角色;'
                        <?php
                            foreach ($role as $v){
                                $checked = in_array($v->id,$role_ids)?'true':'false';
                                echo "datar['root_{$v->id}'] = 'text:{$v->name} | {$v->description}; checked: {$checked}; url: javascript:;'\n";
                            }
                        ?>
                        var treer = new MzTreeView(datar);
                        treer.autoFocused = false;
                        treer.useCheckbox = true;
                        treer.linkFocus = false;
                        treer.linkCheckbox = true;
                        document.getElementById('treer').innerHTML = treer.render();
                        //treer.expandLevel(1);
                        treer.expandAll('root');

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
                        function submit(){
                            if(confirm('确认提交修改吗？')){
                                loading('#roleView');
                                var role_ids = getChecked(treer);
                                var category_ids = getChecked(treec);
                                $.post('<?php echo url('aclmanager/groupbind')?>', {id: '<?php echo $group->id;?>', role_ids: role_ids, category_ids: category_ids}, function(data){$('#groupView').html(data);});
                            }
                        }
                        function groupList() {
                            $('#groupList').show();
                            $('#groupView').hide();
                        }
                    //--></script>
                    <div id="treec" class="left" style="margin:10px;overflow-y:hidden;overflow-x:auto;*overflow-x:scroll;"></div>
                    <div id="treer" class="left" style="margin:10px;overflow-y:hidden;overflow-x:auto;*overflow-x:scroll;"></div>
                    <table class="tb">
                        <tfoot><tr><td colspan="4"><input class="btn" type="button" value="提交" onclick="submit()"/><input class="btn" type="button" value="返回列表" onclick="groupList()"/></td></tr></tfoot>
                    </table>
                </div>
            </div>
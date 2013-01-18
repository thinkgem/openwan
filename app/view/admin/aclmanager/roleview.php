            <div class="box">
                <div class="t1">
                    <span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:" onclick="roleList()">返回列表</a><?php echo $pathway->getPathway();?>
                </div>
                <div class="p10 pt0">
                    <table class="tb">
                        <tfoot><tr><td colspan="4"><input class="btn" type="button" value="返回列表" onclick="roleList()"/></td></tr></tfoot>
                        <tbody>
                            <tr><td class="lbl" nowrap="true">名称：</td><td><?php echo $role->name;?></td></tr>
                            <tr><td class="lbl">说明：</td><td><?php echo $role->description;?></td></tr>
                            <tr><td class="lbl">权重：</td><td><?php echo $role->weight;?></td></tr>
                            <tr><td class="lbl">用户组数：</td><td><?php echo count($role->groups);?></td></tr>
                            <tr><td class="lbl">用户组：</td><td><?php foreach($role->groups as $v)echo $v->name.', ';?></td></tr>
                        </tbody>
                    </table>
                    <script type="text/javascript">//<!--
                        function roleList() {
                            $('#roleList').show();
                            $('#roleView').hide();
                        }
                    //--></script>
                </div>
            </div>
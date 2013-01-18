            <div class="box">
                <div class="t1">
                    <span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:" onclick="groupList()">返回列表</a><?php echo $pathway->getPathway();?>
                </div>
                <div class="p10 pt0">
                    <table class="tb">
                        <tfoot><tr><td colspan="4"><input class="btn" type="button" value="返回列表" onclick="groupList()"/></td></tr></tfoot>
                        <tbody>
                            <tr><td class="lbl" nowrap="true">名称：</td><td><?php echo $group->name;?></td></tr>
                            <tr><td class="lbl">说明：</td><td><?php echo $group->description;?></td></tr>
                            <tr><td class="lbl">权重：</td><td><?php echo $group->weight;?></td></tr>
                            <tr><td class="lbl">用户磁盘配额：</td><td><?php echo $group->quota;?> MB</td></tr>
                            <tr><td class="lbl">角色：</td><td><?php foreach($group->roles as $v)echo $v->name.', ';?></td></tr>
                            <tr><td class="lbl">用户数：</td><td><?php echo count($group->users);?></td></tr>
                            <tr><td class="lbl">用户：</td><td><?php foreach($group->users as $v)echo $v->nickname.', ';?></td></tr>
                        </tbody>
                    </table>
                    <script type="text/javascript">//<!--
                        function groupList() {
                            $('#groupList').show();
                            $('#groupView').hide();
                        }
                    //--></script>
                </div>
            </div>
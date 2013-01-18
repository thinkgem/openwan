            <div class="box">
                <div class="t1">
                    <span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:" onclick="levelList()">返回列表</a><?php echo $pathway->getPathway();?>
                </div>
                <div class="p10 pt0">
                    <table class="tb">
                        <tfoot><tr><td colspan="4"><input class="btn" type="button" value="返回列表" onclick="levelList()"/></td></tr></tfoot>
                        <tbody>
                            <tr><td class="lbl" nowrap="true">名称：</td><td><?php echo $level->name;?></td></tr>
                            <tr><td class="lbl">说明：</td><td><?php echo $level->description;?></td></tr>
                            <tr><td class="lbl">权重：</td><td><?php echo $level->weight;?></td></tr>
                            <tr><td class="lbl">用户数：</td><td><?php echo count($level->users);?></td></tr>
                            <tr><td class="lbl">用户：</td><td><?php foreach($level->users as $v)echo $v->nickname.', ';?></td></tr>
                        </tbody>
                    </table>
                    <script type="text/javascript">//<!--
                        function levelList() {
                            $('#levelList').show();
                            $('#levelView').hide();
                        }
                    //--></script>
                </div>
            </div>
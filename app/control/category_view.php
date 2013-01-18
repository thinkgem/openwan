            <div class="box mt10">
                <?php /*<div class="t1">
                    <span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:tree.collapseAll('1');tree.expandLevel(1)">折叠</a><span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:tree.expandAll('1');">展开</a><?php echo $title;?>
                </div>*/?>
                <div class="p10">
                    <a class="right" style="color:#aaa;" href="javascript:tree.collapseAll('1');tree.expandLevel(1)">折叠</a><span class="right">&nbsp;&nbsp;</span><a class="right" style="color:#aaa;" href="javascript:tree.expandAll('1');">展开</a>
                    <script type="text/javascript">//<!--
                        var data={};
                        <?php foreach ($category as $v):?>
                        data['<?php echo $v->parent_id?>_<?php echo $v->id?>'] = 'text: <?php echo str_replace(';', '%3B', str_replace('%', '%25', $v->name));?>; url: javascript:category(<?php echo $v->id?>);';
                        <?php endforeach;?>
                        var tree = new MzTreeView(data);
                        <?php if(!isset($_ctx->category_id)):?>
                        tree.autoFocused=false;
                        <?php endif;?>
                        document.write(tree.render());
                        tree.expandLevel(1);
                        tree.autoFocused=true;
                    //--></script>
                </div>
            </div>
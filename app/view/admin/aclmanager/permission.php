       <div class="w770 left" id="levelList">
            <div class="box">
                <div class="t1">
                    <?php echo $pathway->getPathway();?>
                </div>
                <div class="p10">
                    <!-- 二级联动 Start -->
                    <script type="text/javascript">//<!--
                        var subcat = new Array();
                        // subcat[0] = new Array('10','1','=1')   ==  subcat[ 数组自增索引 ] = new Array(' 主键上级索引 ',' 显示内容 ',' 值 ')  在这里显示内容跟值一样，所以去掉一个
                        <?php
                            $i = 0;
                            foreach ($ncArr as $keys=>$values){
                                foreach ($values as $value){
                                    echo 'subcat['.$i.'] = new Array("'.$keys.'","'.$value.'")'."\n";
                                    $i++;
                                }
                            }
                        ?>
                        function changeSelect(locationid){
                            document.select_form.list_controller.length = 0;
                            document.select_form.list_controller.options[0] = new Option('==选择控制器==','');
                            for (i=0; i<subcat.length; i++){
                                if (subcat[i][0] == locationid){
                                    document.select_form.list_controller.options[document.select_form.list_controller.length] = new Option(subcat[i][1], subcat[i][1], subcat[i][1] == '<?php if (isset($url_args['list_controller'])) echo $url_args['list_controller'];?>');
                                }
                            }
                            document.select_form.list_controller.options[document.select_form.list_controller.length] = new Option('全部控制器','');
                        }
                        $(document).ready(function(){
                            changeSelect(document.select_form.list_namespace.value);
                            $('#select_form').bind('submit', function() {
                                loading('#ajax');
                                $(this).ajaxSubmit(function(data){$('#ajax').html(data);});
                                return false;
                            });
                        });
                    //--></script>
                    <form id="select_form" name="select_form" action="<?php echo url('admin::aclmanager/permission'); ?>" method="get">
                        选择要显示的模块：
                        <select name="list_namespace" onchange="changeSelect(this.value)" class="txt">
                            <option value="">==命名空间==</option>
                            <?php
                                foreach ($ncArr as $key=>$value){
                                    $url_args['list_namespace'] == $key ? $seleted = ' selected="selected"':$seleted = '';
                                    echo '<option value="'.$key.'" '. $seleted.'>'.$key.'</option>';
                                }
                            ?>
                        </select>
                        <select name="list_controller" onchange="$('#select_form').submit()" class="txt">
                            <option>==选择控制器==</option>
                        </select>&nbsp;&nbsp;
                        <a style="color:#20537A;" href="javascript:" onclick="$.get('<?php echo url('admin::aclmanager/permissionRefresh');?>','',function(data){if(data !=null && data.length<=20)alert(data);else alert('没有权限！');permission();});">更新权限列表</a>&nbsp;&nbsp;
                        <a style="color:#e33;" href="javascript:" onclick="$.get('<?php echo url('admin::aclmanager/MakeAclFile');?>','',function(data){if(data !=null && data.length<=20)alert(data);else alert('没有权限！');});">更新权限文件</a>&nbsp;&nbsp;
                    </form>
                    <!-- 二级联动 Over -->
                    <form id="permission_form" name="permission_form" action="<?php echo url('admin::aclmanager/permission'); ?>" method="post">
                        <table class="tb" align="center">
                            <thead><tr><td>系统角色</td><td>命名空间</td><td>控制器</td><td>动作</td><td>名称</td></tr></thead>
                            <tfoot><tr><td colspan="5"><table width="100%"><tfoot><tr><td><input class="btn" type="submit" value="提交"/></td><td><?php $this->_control('pagination', 'pagination', array('pagination' => $pagination,'url_args' => $url_args)); ?></td></tr></tfoot></table></td></tr></tfoot>
                            <tbody>
                            <?php foreach ($permissions as $value): ?>
                                <tr>
                                    <td>
                                        <select name="rbac[<?php echo h($value->id); ?>]" class="txt w100">
                                            <option label="无" value="ACL_NULL" style="color:#555;" <?php if ($value->rbac == 'ACL_NULL') echo 'selected="selected"'; ?>>无</option>
                                            <option label="任何用户" value="ACL_EVERYONE" style="color:green;" <?php if ($value->rbac == 'ACL_EVERYONE') echo 'selected="selected"'; ?>>任何用户</option>
                                            <option label="任何角色的用户" value="ACL_HAS_ROLE" style="color:blue;" <?php if ($value->rbac == 'ACL_HAS_ROLE') echo 'selected="selected"'; ?>>任何角色的用户</option>
                                            <option label="没有角色的用户"  value="ACL_NO_ROLE" style="color:red;" <?php if ($value->rbac == 'ACL_NO_ROLE') echo 'selected="selected"'; ?>>没有角色的用户</option>
                                        </select>
                                    </td>
                                    <td><?php echo h($value->namespace); ?></td>
                                    <td><?php echo h($value->controller); ?></td>
                                    <td><?php echo h($value->action); ?></td>
                                    <td><input id='aliasname_<?php echo h($value->id); ?>' name='aliasname[<?php echo h($value->id); ?>]' type='text' maxlength="64" value="<?php echo nl2br(h($value->aliasname)); ?>" class="txt w200"/></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </form>
                    <script type="text/javascript">//<!--
                        $(document).ready(function(){
                            $('#pagination a').each(function(){
                                $(this).click(function(){
                                    loading('#ajax');
                                    $.get($(this).attr('href'), null, function(data){
                                       $('#ajax').html(data);
                                    });
                                    $(this).attr('href','javascript:');
                                });
                            });
                            $('#permission_form').bind('submit', function() {
                                if(confirm('确认提交修改吗？')){
                                    loading('#ajax');
                                    $(this).ajaxSubmit(function(data){$('#ajax').html(data);});
                                    if(confirm('修改成功，是否更新权限文件？')){
                                        $.get('<?php echo url('admin::aclmanager/MakeAclFile');?>','',function(data){
                                            if(data !=null && data.length<=20)alert(data);else alert('没有权限！');
                                        });
                                    }
                                }
                                return false;
                            });
                        });
                    //--></script>
                </div>
            </div>
        </div>
        <div class="w770 left hide" id="levelView">

        </div>
            <div class="box">
                <div class="t1">
                    <span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:" onclick="list()">返回列表</a><?php echo $pathway->getPathway();?>
                </div>
                <div class="p10">                    
                    <form id="catalog_form" action="<?php echo url('admin::filecatalog/catalog',array('type'=>$file->type,'id'=>$file->id));?>" method="post">
                    <table class="tb">
                        <tfoot><tr><td colspan="4"><input class="btn" type="submit" value="提交文件"/><input class="btn" type="button" value="返回列表" onclick="list()"/></td></tr></tfoot>
                        <tbody>
                            <tr><td class="lbl" nowrap="true" colspan="4" style="font-size:14px">基本</td></tr>
                            <tr>
                                <td class="lbl tr" nowrap="true">标题：</td><td><input type="text" name="title" value="<?php echo $file->title;?>" class="txt w250"/></td>
                                <td class="lbl tr" nowrap="true">分类：</td><td>
                                    <select class="txt w255" name="category_id">
                                        <?php foreach($category as $v):?>
                                        <option value="<?php echo $v['id'];?>" <?php if($v['id']==$file->category_id) echo 'selected';?>><?php echo $v['id']==1 ? $v['name'] : $v['levelstr'].$v['name'];?></option>
                                        <?php endforeach?>
                                    </select></td>
                            </tr>
                            <tr>
                                <td class="lbl tr" nowrap="true">状态：</td><td><input type="text" readonly="true" value="<?php echo $file->status_formatted;?>" class="txt w250"/></td>
                                <td class="lbl tr" nowrap="true">类型：</td><td><input type="text" readonly="true" value="<?php echo $file->type_formatted;?>" class="txt w250"/></td>
                            </tr>
                            <tr>
                                <td class="lbl tr" nowrap="true">文件名：</td><td><input type="text" readonly="true" value="<?php echo $file->name?>" class="txt w250"/></td>
                                <td class="lbl tr" nowrap="true">文件类型：</td><td><input type="text" readonly="true" value="<?php echo $file->ext;?>" class="txt" style="width:80px;"/>
                                    <span class="lbl tr">&nbsp;文件大小：</span><input type="text" readonly="true" value="<?php echo Helper_Util::formattedFileSize($file->size);?>" class="txt" style="width:88px;"/></td>
                            </tr>
                            <tr>
                                <td class="lbl tr" nowrap="true">上传者：</td><td><input type="text" readonly="true" value="<?php echo $file->upload_username;?>" class="txt w250"/></td>
                                <td class="lbl tr" nowrap="true">上传时间：</td><td><input type="text" readonly="true" value="<?php echo $file->upload_at_formatted;?>" class="txt w250"/></td>
                            </tr>
                            <tr>
                                <td class="lbl tr" nowrap="true">编目者：</td><td><input type="text" readonly="true" value="<?php echo $file->catalog_username;?>" class="txt w250"/></td>
                                <td class="lbl tr" nowrap="true">编目时间：</td><td><input type="text" readonly="true" value="<?php echo $file->catalog_at_formatted;?>" class="txt w250"/></td>
                            </tr>
                            <tr>
                                <td class="lbl tr" nowrap="true">审核者：</td><td><input type="text" readonly="true" value="<?php echo $file->putout_username;?>" class="txt w250"/></td>
                                <td class="lbl tr" nowrap="true">审核时间：</td><td><input type="text" readonly="true" value="<?php echo $file->putout_at_formatted;?>" class="txt w250"/></td>
                            </tr>
                            <?php foreach($file->catalogInfo as $k => $v): ?>
                                <?php if(!is_numeric($k) && is_array($v)):?>
                                    <tr><td class="lbl" nowrap="true" colspan="4" style="font-size:14px"><?php echo $k;?></td></tr>
                                    <?php $i=0; foreach($v as $_k => $_v): $i=$i+1;?>
                                    <?php if($i%2!=0):?><tr><?php endif;?>
                                        <td class="lbl tr" nowrap="true"><?php echo $_k;?>：</td><td><input type="text" name="catalog[<?php echo $k;?>][<?php echo $_k;?>]" value="<?php echo $_v;?>" class="txt w250"/></td>
                                    <?php if($i%2==0):?></tr><?php endif;?>
                                    <?php endforeach;?>
                                    <?php if($i%2!=0) echo '<td class="lbl tr" nowrap="true"></td><td></td></tr>';//补单元格?>
                                <?php endif;?>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                    </form>
                    <script type="text/javascript">//<!--
                        $(document).ready(function() {
                            $('#catalog_form').bind('submit', function() {
                                if(confirm('确认编目文件吗？')){
                                    loading('#view','正在编目请稍后......');
                                    $(this).ajaxSubmit(function(data){
                                        alert(data);
                                        document.location.reload();
                                    });
                                }
                                return false;
                            });
                        });
                        function list() {
                            $('#list').show();
                            $('#view').hide();
                        }
                    //--></script>
                </div>
            </div>

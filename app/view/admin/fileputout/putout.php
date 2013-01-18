            <div class="box">
                <div class="t1">
                    <span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:" onclick="list()">返回列表</a><?php echo $pathway->getPathway();?>
                </div>
                <div class="p10">
                    <form id="catalog_form" action="<?php echo url('admin::fileputout/putout',array('type'=>$file->type,'id'=>$file->id));?>" method="post">
                    <table class="tb">
                        <tfoot><tr><td colspan="4"><input class="btn" type="submit" value="提交文件"/><input class="btn" type="button" value="返回列表" onclick="list()"/></td></tr></tfoot>
                        <tbody>
                            <tr><td class="lbl" nowrap="true" colspan="4" style="font-size:14px">基本</td></tr>
                            <tr>
                                <td class="lbl tr" nowrap="true">标题：</td><td><input type="text" value="<?php echo $file->title;?>" class="txt w250"/></td>
                                <td class="lbl tr" nowrap="true">分类：</td><td><input type="text" value="<?php echo $file->category_name;?>" class="txt w250"/></td>
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
                                        <td class="lbl tr" nowrap="true"><?php echo $_k;?>：</td><td><input type="text" readonly="true" value="<?php echo $_v;?>" class="txt w250"/></td>
                                    <?php if($i%2==0):?></tr><?php endif;?>
                                    <?php endforeach;?>
                                    <?php if($i%2!=0) echo '<td class="lbl tr" nowrap="true"></td><td></td></tr>';//补单元格?>
                                <?php endif;?>
                            <?php endforeach;?>
                            <tr><td class="lbl" nowrap="true" colspan="4" style="font-size:14px">发布选项</td></tr>                            
                            <tr>
                                <td class="lbl tr" nowrap="true">浏览级别：</td><td colspan="3">
                                    <select class="txt w250" name="level">
                                        <?php foreach($levels as $v):?>
                                        <option value="<?php echo $v->weight;?>" <?php if($v->weight==$file->level) echo 'selected';?>><?php echo $v->name;?></option>
                                        <?php endforeach;?>
                                    </select>
                                </td>
                            </tr>
                            <tr>                                
                                <td class="lbl tr" nowrap="true">可浏览用户组：</td><td colspan="2">
                                    <select class="txt w260" name="groups[]" multiple="multiple" size="9">
                                        <option value="all" <?php echo $file->groups=='' ? 'selected' : '' ?>>全部组</option>
                                        <?php foreach($groups as $v):?>
                                            <option value="<?php echo $v->id?>" <?php echo $file->groups!='' && in_array($v->id, split(',',$file->groups)) ? 'selected' : '' ?>><?php echo $v->name?></option>
                                        <?php endforeach;?>
                                    </select>
                                </td>
                                <td><span class="desc">按住 CTRL 多选</span></td>
                            </tr>
                            <tr>
                                <td class="lbl tr" nowrap="true">下载：</td><td colspan="3">
                                    <input type="radio" class="radio" value="1" id="is_download_1" name="is_download" <?php echo $file->is_download?'checked':''?>>
                                    <label class="inline" for="is_download_1" >允许 &nbsp;&nbsp;</label>
                                    <input type="radio" class="radio" value="0" id="is_download_2" name="is_download" <?php echo !$file->is_download?'checked':''?>>
                                    <label class="inline" for="is_download_2">拒绝 &nbsp;&nbsp;</label>
                                </td>                                
                            </tr>
                            <tr>
                                <td class="lbl tr" nowrap="true">状态：</td><td colspan="3">
                                    <input type="radio" class="radio" value="2" id="status_1" name="status" <?php echo $file->status==1||$file->status==2?'checked':''?>>
                                    <label class="inline" for="status_1">发布 &nbsp;&nbsp;</label>
                                    <input type="radio" class="radio" value="3" id="status_2" name="status" <?php echo $file->status==3?'checked':''?>>
                                    <label class="inline" for="status_2">打回 &nbsp;&nbsp;</label>
                                    <input type="radio" class="radio" value="4" id="status_3" name="status" <?php echo $file->status==4?'checked':''?>>
                                    <label class="inline" for="status_3">删除 &nbsp;&nbsp;</label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </form>
                    <script type="text/javascript">//<!--
                        $(document).ready(function() {
                            $('#catalog_form').bind('submit', function() {
                                if(confirm('确认提交文件吗？')){
                                    loading('#view','正在发布请稍后......');
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

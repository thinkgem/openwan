<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('head'); ?>
    <script type="text/javascript" src="<?php echo $_BASE_DIR;?>js/flowplayer/flowplayer-3.1.4.min.js"></script>
<?php $this->_endblock(); ?>

<?php $this->_block('contents'); ?>
        <div class="box">
            <div class="t1">
                <?php echo $pathway->getPathway();?>
            </div>
            <div class="p10">                
                <table class="tb">
                    <tbody>
                        <tr><td class="lbl" nowrap="true" colspan="4" style="font-size:14px">基本</td></tr>
                        <tr>
                            <td class="lbl tr" nowrap="true">标题：</td><td><input type="text" value="<?php echo $file->title;?>" class="txt w350"/></td>
                            <td class="lbl tr" nowrap="true">分类：</td><td><input type="text" value="<?php echo $file->category_name;?>" class="txt w350"/></td>
                        </tr>
                        <tr>
                            <td class="lbl tr" nowrap="true">状态：</td><td><input type="text" readonly="true" value="<?php echo $file->status_formatted;?>" class="txt w350"/></td>
                            <td class="lbl tr" nowrap="true">类型：</td><td><input type="text" readonly="true" value="<?php echo $file->type_formatted;?>" class="txt w350"/></td>
                        </tr>
                        <tr>
                            <td class="lbl tr" nowrap="true">文件名：</td><td><input type="text" readonly="true" value="<?php echo $file->name?>" class="txt w350"/></td>
                            <td class="lbl tr" nowrap="true">文件类型：</td><td><input type="text" readonly="true" value="<?php echo $file->ext;?>" class="txt" style="width:138px;"/>
                                <span class="lbl tr">&nbsp;文件大小：</span><input type="text" readonly="true" value="<?php echo Helper_Util::formattedFileSize($file->size);?>" class="txt" style="width:130px;"/></td>
                        </tr>
                        <tr>
                            <td class="lbl tr" nowrap="true">上传者：</td><td><input type="text" readonly="true" value="<?php echo $file->upload_username;?>" class="txt w350"/></td>
                            <td class="lbl tr" nowrap="true">上传时间：</td><td><input type="text" readonly="true" value="<?php echo $file->upload_at_formatted;?>" class="txt w350"/></td>
                        </tr>
                        <tr>
                            <td class="lbl tr" nowrap="true">编目者：</td><td><input type="text" readonly="true" value="<?php echo $file->catalog_username;?>" class="txt w350"/></td>
                            <td class="lbl tr" nowrap="true">编目时间：</td><td><input type="text" readonly="true" value="<?php echo $file->catalog_at_formatted;?>" class="txt w350"/></td>
                        </tr>
                        <tr>
                            <td class="lbl tr" nowrap="true">审核者：</td><td><input type="text" readonly="true" value="<?php echo $file->putout_username;?>" class="txt w350"/></td>
                            <td class="lbl tr" nowrap="true">审核时间：</td><td><input type="text" readonly="true" value="<?php echo $file->putout_at_formatted;?>" class="txt w350"/></td>
                        </tr>
                        <?php foreach($file->catalogInfo as $k => $v): ?>
                            <?php if(!is_numeric($k) && is_array($v)):?>
                                <tr><td class="lbl" nowrap="true" colspan="4" style="font-size:14px"><?php echo $k;?></td></tr>
                                <?php $i=0; foreach($v as $_k => $_v): $i=$i+1;?>
                                <?php if($i%2!=0):?><tr><?php endif;?>
                                    <td class="lbl tr" nowrap="true"><?php echo $_k;?>：</td><td><input type="text" readonly="true" value="<?php echo $_v;?>" class="txt w350"/></td>
                                <?php if($i%2==0):?></tr><?php endif;?>
                                <?php endforeach;?>
                                <?php if($i%2!=0) echo '<td class="lbl tr" nowrap="true"></td><td></td></tr>';//补单元格?>
                            <?php endif;?>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="view" class="w770 left ml10 hide"></div>

<?php $this->_endblock(); ?>
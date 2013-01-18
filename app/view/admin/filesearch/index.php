<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('head'); ?>
    <script type="text/javascript" src="<?php echo $_BASE_DIR;?>js/flowplayer/flowplayer-3.1.4.min.js"></script>
<?php $this->_endblock(); ?>

<?php $this->_block('contents'); ?>

    <div class="search">
        
        <form id="search_form" action="<?php echo url("{$_ctx->namespace}::{$_ctx->controller_name}/{$_ctx->action_name}");?>">
        <p><?php foreach(Q::ini('appini/nav/left/fileSearch') as $key => $nav): if ($_app->authorizedUDI($currentUser['roles'], $nav['udi'])):?>
            &nbsp;&nbsp;
            <?php if("type/$type" == (isset($nav['args'])?$nav['args']:'')):?>
                <b><?php echo $nav['title'];?></b>
            <?php else:?>
                <a href="<?php echo url($nav['udi'], (isset($nav['args'])?$nav['args']:'').'/query/'.$query);?>"><?php echo $nav['title'];?></a>
           <?php endif;?>
           <?php endif;endforeach;?>
            <input type="hidden" name="type" value="<?php echo $type;?>"/>
        </p>
        <p><input type="text" name="query" value="<?php echo $query?>" class="txt input">&nbsp;<input type="submit" value="搜 索" class="btn submit"></p>
        </form>
        
        <p class="status"><span><?php echo $pathway->getTitle();?>列表</span> <span class="right">搜索 <?php echo $query?> 获得约 <?php echo isset($result)?$result['total']:0 ?> 条结果，以下是第 <?php echo isset($result)?$result['start'].'-'.$result['end']:0?> 条。 （用时 <?php echo isset($result)?$result['time']:0 ?> 秒）</span></p>

        <ul class="result">
            <?php if(isset($files)):foreach($files as $v):?>
            <li>
                <a class="title" target="_blank" href="<?php echo url('admin::fileSearch/view', array('id'=>$v->id));?>"><?php echo Helper_Util::substr($v->title,40);?></a>
                <p>文件名:<?php echo $v->name;?>;
                   文件类型:<?php echo $v->ext;?>;
                   文件大小:<?php echo $v->size_formatted;?>;                   
                   上传者:<?php echo $v->upload_username;?>;
                   上传时间:<?php echo $v->upload_at_formatted;?>;
                   编目者:<?php echo $v->catalog_username;?>;
                   编目时间:<?php echo $v->catalog_at_formatted;?>;
                   审核者:<?php echo $v->putout_username;?>;
                   审核时间:<?php echo $v->putout_at_formatted;?>
                   <?php echo $v->catalog_info_formatted;?>
                <p class="ctrl"> 2009-1-1 <?php echo $v->category_name?> <a href="#" onclick="preview(this, '<?php echo url('admin::filecatalog/preview', array('id'=>$v->id));?>', '<?php echo $v->title;?>', '<?php echo $v->type?>');">预览</a> <?php if($v->is_download==1):?><a href="<?php echo url('admin::fileSearch/download', array('id'=>$v->id));?>">下载</a><?php endif;?></p>
            </li>
            <?php endforeach;?>
            <?php else:?>
            <li>没有搜索到结果</li>
            <?php endif;?>
        </ul>        
        <div class="w332 mt10 box right">
            <div class="p5">
                <?php $this->_element('preview'); ?>
            </div>
        </div>
        <div class="clear"></div>
        <?php if(isset($pagination)) $this->_control('pagination', 'pagination', array('pagination' => $pagination, 'url_args' => array('type' => $type,'query' => $query))); ?>
    </div>
    

<?php $this->_endblock(); ?>

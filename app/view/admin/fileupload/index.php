<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('head'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $_BASE_DIR;?>css/swfupload.css"/>
    <script type="text/javascript" src="<?php echo $_BASE_DIR;?>js/swfupload/swfupload.js"></script>
    <script type="text/javascript" src="<?php echo $_BASE_DIR;?>js/swfupload/swfupload.swfobject.js"></script>
    <script type="text/javascript" src="<?php echo $_BASE_DIR;?>js/swfupload/swfupload.queue.js"></script>
    <script type="text/javascript" src="<?php echo $_BASE_DIR;?>js/swfupload/fileprogress.js"></script>
    <script type="text/javascript" src="<?php echo $_BASE_DIR;?>js/swfupload/handlers.js"></script>
    <script type="text/javascript">
        var swfu;
        SWFUpload.onload = function () {
                var settings = {
                        flash_url : "<?php echo $_BASE_DIR;?>js/swfupload/swfupload.swf",
                        upload_url: "<?php echo url('admin::fileupload/index')?>",
                        post_params: {
                            "PHPSESSID" : "<?php echo session_id(); ?>",
                            "category_id" : "<?php echo $category->id?>",
                            "category_name" : "<?php echo $category->name?>"
                        },
                        file_post_name: "filedata",
                        file_size_limit : "<?php echo Q::ini('appini/upload/fileSizeLimit');?>",
                        file_types : "<?php echo  Q::ini('appini/upload/videoFileTypes').Q::ini('appini/upload/audioFileTypes').Q::ini('appini/upload/imageFileTypes').Q::ini('appini/upload/richFileTypes');;?>",
                        file_types_description : "<?php echo Q::ini('appini/upload/fileTypesDescription');?>",
                        file_upload_limit : 100,
                        file_queue_limit : 0,
                        custom_settings : {
                                progressTarget : "fsUploadProgress",
                                cancelButtonId : "btnCancel"
                        },
                        debug: false,

                        // Button Settings
                        button_placeholder_id : "spanButtonPlaceholder",
                        button_width: 62,
                        button_height: 26,
                        button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
                        button_cursor: SWFUpload.CURSOR.HAND,

                        // The event handler functions are defined in handlers.js
                        swfupload_loaded_handler : swfUploadLoaded, //上传组件加载
                        file_queued_handler : fileQueued, //添加文件到队列
                        file_queue_error_handler : fileQueueError,//添加到队列错误
                        file_dialog_complete_handler : fileDialogComplete,//打开对话框
                        upload_start_handler : uploadStart,//开始上传文件
                        upload_progress_handler : uploadProgress,//文件上传的进度
                        upload_error_handler : uploadError,//文件上传错误
                        upload_success_handler : uploadSuccess,//文件上传成功
                        upload_complete_handler : uploadComplete,//文件上传完成
                        queue_complete_handler : queueComplete,	//全部文件上传完成

                        // SWFObject settings
                        minimum_flash_version : "9.0.28",
                        swfupload_pre_load_handler : swfUploadPreLoad, //开始加载上传组件
                        swfupload_load_failed_handler : swfUploadLoadFailed //加载上传组件失败
                };
                <?php if(!$category->isNewRecord() && $category->id != 1): ?>swfu = new SWFUpload(settings);<?php endif;?>
        }
        function category(id){//分类树单击事件
            $('#category_id').val(id);
            $('#category_form').submit();
        }
    </script>
<?php $this->_endblock(); ?>

<?php $this->_block('contents'); ?>

        <form id="category_form" action="<?php echo url('admin::fileupload/index');?>"><input type="hidden" name="category_id" id="category_id"/></form>

        <div class="w200 left">
            <div class="box">
                <div class="p10">
                    <h2 class="t3"><?php echo $pathway->getTitle();?></h2>
                </div>
            </div>
            <?php $this->_control('category', 'category', array('title' => $pathway->getTitle()));?>
        </div>
        <div class="w385 left ml10">
            <div class="box">
                <div class="t1">
                    <span class="right">&nbsp;&nbsp;</span><?php echo $pathway->getPathway();?>
                </div>
                <div class="p10">                    
                    <div id="divSWFUploadUI">
                        <p>
                            <span id="spanButtonPlaceholder"></span>
                            <input id="btnBrowse" type="button" value="浏览" class="btn" style="width: 61px; height: 23px; display:none;" />
                            <input id="btnUpload" type="button" value="上传" class="btn" style="width: 61px; height: 23px;" onclick="<?php if($category->id != 1): ?>alert('请选择上载分类,如果已选择分类请刷新页面！');<?php else:?>alert('不能上传到顶级目录');<?php endif;?>" />
                            <input id="btnCancel" type="button" value="全部取消" class="btn" style="width: 66px; height: 23px;" />
                            <span>上传分类：<strong><?php echo $category->name==''?'未选择上载分类':$category->name;?></strong></span>
                        </p>
                        <br style="clear: both;" />
                        <div id="fsUploadProgress"><span id="divStatus" class="mt10"></span></div>
                    </div>
                    <noscript class="message">
                            不能加载上传组件. 你的浏览器不支持脚本运行.
                    </noscript>
                    <div id="divLoadingContent" class="message" style="display: none;">
                            正在加载上传组件请等待...
                    </div>
                    <div id="divLongLoading" class="message" style="display: none;">
                            加载上传组件已超时. 请刷新页面并确保浏览器已经开启对JavaScript的支持，并且已经安装可以工作的Flash插件版本.
                    </div>
                    <div id="divAlternateContent" class="message" style="display: none;">
                            很抱歉，上传组件无法加载，您可能需要安装或升级Flash播放器。访问 <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe website</a>下载Flash播放器.
                    </div>
                </div>
            </div>
        </div>
        <div class="w375 left ml10">            
            <div class="box">
                <div class="t2">历史上传</div>
                <div class="p10 pt0">
                    <table class="tb">
                        <thead><tr><td>名称</td><td>状态</td><td>时间</td></thead>
                        <tbody>
                            <?php if(!isset($files) || count($files) <= 0):?>
                            <tr><td colspan="3">无记录</td></tr>
                            <?php else:?>
                                <?php foreach($files as $v):?>
                                <tr><td><?php echo Helper_Util::substr($v->title,18);?></td><td nowrap="true"><?php echo $v->status_formatted;?></td><td nowrap="true"><?php echo $v->upload_at_formatted;?></td></tr>
                                <?php endforeach;?>
                            <?php endif;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        

<?php $this->_endblock(); ?>

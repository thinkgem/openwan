<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>SWFUpload Demos - Demo</title>
<link href="default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../swfupload.js"></script>
<script type="text/javascript" src="../swfupload.swfobject.js"></script>
<script type="text/javascript" src="../swfupload.queue.js"></script>
<script type="text/javascript" src="../fileprogress.js"></script>
<script type="text/javascript" src="../handlers.js"></script>
<script type="text/javascript">
    var swfu;
    SWFUpload.onload = function () {
            var settings = {
                    flash_url : "../swfupload.swf",
                    upload_url: "upload.php",
                    post_params: { "post_name1": "post_value1", "post_name2": "post_value2" },
                    file_post_name: "Filedata",
                    file_size_limit : "100 MB",
                    file_types : "*.jpg;*.gif;*.png;*.zip;*.rar",
                    file_types_description : "所有支持的文件",
                    file_upload_limit : 100,
                    file_queue_limit : 0,
                    custom_settings : {
                            progressTarget : "fsUploadProgress",
                            cancelButtonId : "btnCancel"
                    },
                    debug: false,

                    // Button Settings
                    button_placeholder_id : "spanButtonPlaceholder",
                    button_width: 61,
                    button_height: 22,
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
            swfu = new SWFUpload(settings);
    }

</script>
</head>
<body>
    
<div id="header">
	<h1 id="logo"><a href="../">SWFUpload</a></h1>
	<div id="version">v2.2.0</div>
</div>

<div id="content">	
        <div id="divSWFUploadUI">
                <p>
                        <span id="spanButtonPlaceholder"></span>
                        <input id="btnBrowse" type="button" value="浏览" style="width: 61px; height: 22px; font-size: 9pt;" />
                        <input id="btnUpload" type="button" value="全部上传" style="margin-left: 2px; height: 22px; font-size: 9pt;" />
                        <input id="btnCancel" type="button" value="全部取消" style="margin-left: 2px; height: 22px; font-size: 9pt;" />
                </p>
                <p id="divStatus">0 个文件已上传</p>
                <div id="fsUploadProgress" class="fieldset flash"></div>
                <br style="clear: both;" />
        </div>
        <noscript class="message">
                不能加载 SWFUpload. 你的浏览器不支持脚本运行.
        </noscript>
        <div id="divLoadingContent" class="message" style="display: none;">
                正在加载 SWFUpload 请等待...
        </div>
        <div id="divLongLoading" class="message" style="display: none;">
                加载上传组件已超时. 请确保浏览器已经开启对JavaScript的支持，并且已经安装可以工作的Flash插件版本.
        </div>
        <div id="divAlternateContent" class="message" style="display: none;">
                很抱歉，SWFUpload无法加载，您可能需要安装或升级Flash播放器。访问 <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe website</a>下载Flash播放器.
        </div>
    </div>
</body>
</html>

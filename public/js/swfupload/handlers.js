/* Demo Note:  This demo uses a FileProgress class that handles the UI for displaying the file name and percent complete.
The FileProgress class is not part of SWFUpload.
*/


/* **********************
   Event Handlers
   These are my custom event handlers to make my
   web application behave the way I went when SWFUpload
   completes different tasks.  These aren't part of the SWFUpload
   package.  They are part of my application.  Without these none
   of the actions SWFUpload makes will show up in my application.
   ********************** */

function swfUploadPreLoad() {
    var self = this;
    var loading = function () {
        //document.getElementById("divSWFUploadUI").style.display = "none";
        document.getElementById("divLoadingContent").style.display = "";
        var longLoad = function () {
            document.getElementById("divLoadingContent").style.display = "none";
            document.getElementById("divLongLoading").style.display = "";
        };
        this.customSettings.loadingTimeout = setTimeout(function () {
            longLoad.call(self)
        },
        15 * 1000
        );
    };
	
    this.customSettings.loadingTimeout = setTimeout(function () {
        loading.call(self);
    },
    1*1000
    );
}

function swfUploadLoaded() { 
    var self = this;
    clearTimeout(this.customSettings.loadingTimeout);
    //document.getElementById("divSWFUploadUI").style.visibility = "visible";
    //document.getElementById("divSWFUploadUI").style.display = "block";
    document.getElementById("divLoadingContent").style.display = "none";
    document.getElementById("divLongLoading").style.display = "none";
    document.getElementById("divAlternateContent").style.display = "none";
	
    //document.getElementById("btnBrowse").onclick = function () { self.selectFiles(); };
    document.getElementById("btnUpload").onclick = function () {
        self.startUpload();
    };
    document.getElementById("btnCancel").onclick = function () {
        self.cancelQueue();
    };
}

function swfUploadLoadFailed() {
    clearTimeout(this.customSettings.loadingTimeout);
    //document.getElementById("divSWFUploadUI").style.display = "none";
    document.getElementById("divLoadingContent").style.display = "none";
    document.getElementById("divLongLoading").style.display = "none";
    document.getElementById("divAlternateContent").style.display = "";
}
 
function fileQueued(file) {
    try {
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setStatus("等待上传...");
        progress.toggleCancel(true, this);
    } catch (ex) {
        this.debug(ex);
    }
}

function fileQueueError(file, errorCode, message) {
    try {
        if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
            alert("已经添加队列中太多的文件.\n" + (message === 0 ? " 你已经达到上传限制." : (message > 1 ? "你可以选择 " + message + " 个文件." : "")));
            return;
        }
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setError();
        progress.toggleCancel(false);
        switch (errorCode) {
            case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
                progress.setStatus("上传文件过大");
                this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                break;
            case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
                progress.setStatus("不能上载 0 字节的文件");
                this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                break;
            case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
                progress.setStatus("无效的文件类型");
                this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                break;
            default:
                if (file !== null) {
                    progress.setStatus("未知错误");
                }
                this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                break;
        }
    } catch (ex) {
        this.debug(ex);
    }
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
    try {
//        if (numFilesSelected > 0) {
//            document.getElementById(this.customSettings.cancelButtonId).disabled = false;
//        }
        /* I want auto start the upload and I can do that here */
        this.startUpload();
    } catch (ex)  {
        this.debug(ex);
    }
}

function uploadStart(file) {
    try {
        /* I don't want to do any file validation or anything,  I'll just update the UI and
            return true to indicate that the upload should start.
            It's important to update the UI here because in Linux no uploadProgress events are called. The best
            we can do is say we are uploading.
        */
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setStatus("正在上传...");
        progress.toggleCancel(true, this);
    }
    catch (ex) {}
	
    return true;
}

function uploadProgress(file, bytesLoaded, bytesTotal) {
    try {
        var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setProgress(percent);
        progress.setStatus("正在上传...");
    } catch (ex) {
        this.debug(ex);
    }
}

function uploadSuccess(file, serverData) {
    try {
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        if (serverData.length >= 4 && serverData.substring(0, 4) == 'true'){
            progress.setStatus("上传完成, 正在生成预览文件...");
            $.get(serverData.substring(5,serverData.length),function(){
                progress.setComplete();
                progress.setStatus("上传完成");
            });
        }else{
            progress.setError();
            progress.setStatus(serverData);
        }
        progress.toggleCancel(false);
    } catch (ex) {
        this.debug(ex);
    }
}

function uploadError(file, errorCode, message) {
    try {
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setError();
        progress.toggleCancel(false);

        switch (errorCode) {
            case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
                progress.setStatus("HTTP 错误: " + message);
                this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
                break;
            case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
                progress.setStatus("上传失败");
                this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                break;
            case SWFUpload.UPLOAD_ERROR.IO_ERROR:
                progress.setStatus("服务器 (IO) 错误");
                this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
                break;
            case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
                progress.setStatus("安全错误");
                this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
                break;
            case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
                progress.setStatus("上传超出限制");
                this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                break;
            case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
                progress.setStatus("验证失败 跳过上传");
                this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                break;
            case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
                // If there aren't any files left (they were all cancelled) disable the cancel button
                if (this.getStats().files_queued === 0) {
                //document.getElementById(this.customSettings.cancelButtonId).disabled = true;
                }
                progress.setStatus("已取消");
                progress.setCancelled();
                break;
            case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
                progress.setStatus("已停止");
                break;
            default:
                progress.setStatus("未知错误: " + errorCode);
                this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                break;
        }
    } catch (ex) {
        this.debug(ex);
    }
}

function uploadComplete(file) {
//    if (this.getStats().files_queued === 0) {
//        document.getElementById(this.customSettings.cancelButtonId).disabled = true;
//    }
}

function queueComplete(numFilesUploaded) {
    var status = document.getElementById("divStatus");
    status.innerHTML = " 本次 " + numFilesUploaded + " 个文件, 上传完成.";
}

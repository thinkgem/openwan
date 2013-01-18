    <script type="text/javascript">
        function preview(e, file, title, type){
            $(e).parent().children().removeClass('current');
            $(e).addClass('current');
            if (title && title.length > 20){
                title = title.substring(0, 20)+'...';
            }
            if (!type) type = '<?php echo $type;?>';
            if (type=='1'||type=='2'){//视频 音频
                $('#preview').html('<a id="player" style="width:320px;height:264px;display:block;" href="'+file+'"></a><span>正在预览：'+title+'</span>');
                flowplayer("player", "<?php echo $_BASE_DIR;?>js/flowplayer/flowplayer-3.2.1.swf",{clip:{autoPlay: false,autoBuffering: true}});
            }else if(type=='3'){//图片
                $('#preview').html('<img src="'+file+'" alt="'+title+'" style="width:320px;height:240px;"/><span>正在预览：'+title+'</span>');
            }else if(type=='4'){//富媒体
                $.get(file, function(data){
                    $('#preview').html('<textarea class="txt" style="width:315px;height:320px;">'+data+'</textarea><span>正在预览：'+title+'</span>');
                });
            }
        }
        window.onload = window.onbeforeunload = function(){
            $('#preview').html('<p style="text-align:center;line-height:24px;">请选择预览文件</p>');
        }
    </script>
    <div id="preview" style="line-height:24px;"></div>
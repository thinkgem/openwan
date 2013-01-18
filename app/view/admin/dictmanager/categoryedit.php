            <div class="box">
                <div class="t1">
                    <span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:" onclick="$('#edit').html('');$('#treeview').removeClass('w380').addClass('w770');">关闭</a><?php echo $pathway->getTitle();?>
                </div>
                <div class="p10">
                    <script type="text/javascript">//<!--
                        $(document).ready(function() {
                            $('#category_form').bind('submit', function() {
                                loading('#edit');
                                $(this).ajaxSubmit(function(data){
                                    try{
                                        data = eval('('+data+')');
                                        alert(data.msg);
                                        init();
                                        //editNode(data.id);
                                    }catch(e){
                                        $('#edit').html(data);
                                    }
                                });
                                return false;
                            });
                        });
                    //--></script>
                    <?php $this->_element('form', array('form' => $form)); ?>
                </div>
            </div>
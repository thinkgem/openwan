            <div class="box">                
                <div class="t1">
                    <span class="right">&nbsp;&nbsp;</span><a class="f12 right" href="javascript:" onclick="userList()">返回列表</a><?php echo $pathway->getPathway();?>
                </div>
                <div class="p10 pt0">
                    <script type="text/javascript">//<!--
                        $(document).ready(function() {
                            $('#user_form').bind('submit', function() {
                                loading('#userView');
                                $(this).ajaxSubmit(function(data){
                                    try{
                                        data = eval('('+data+')');
                                        alert(data.msg);
                                        userList();
                                    }catch(e){
                                        $('#userView').html(data);
                                    }
                                });
                                return false;
                            });
                        });
                        function userList() {
                            $('#userList').show();
                            $('#userView').hide();
                        }
                    //--></script>
                    <?php $this->_element('form', array('form' => $form)); ?>
                </div>
            </div>
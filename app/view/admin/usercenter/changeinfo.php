            <div class="box">
                <div class="t1">
                    <?php echo $pathway->getPathway();?>
                </div>
                <div class="p10">
                    <script type="text/javascript">//<!--
                        $(document).ready(function() {
                            $('#user_form').bind('submit', function() {
                                loading('#ajax');
                                $(this).ajaxSubmit(function(data){
                                    try{
                                        data = eval('('+data+')');
                                        alert(data.msg);
                                        init();
                                    }catch(e){
                                        $('#ajax').html(data);
                                    }
                                });
                                return false;
                            });
                        });
                    //--></script>
                    <?php $this->_element('form', array('form' => $form)); ?>
                </div>
            </div>
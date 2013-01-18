<?php $this->_extends('_layouts/login_layout'); ?>

<?php $this->_block('contents'); ?>

    <form method="post" name="login" id="loginform" action="<?php echo url('admin::default/login'); ?>">
        <?php if(isset($error)):?>
        <p style="text-align:center;font-size:14px;">
          <label class="error"><?php echo $error?></label>
        </p>
        <?php endif;?>
        <p class="logintitle">用户名: </p>
        <p class="loginform">
          <input name="username" tabindex="1" type="text" class="txt" />          
        </p>
        <p class="logintitle">密　码:</p>
        <p class="loginform">
          <input name="password" tabindex="2" type="password" class="txt" />
        </p>        
        <p class="logintitle">界　面:</p>
        <p class="loginform">
          <select name="theme" tabindex="3" class="txt">
            <option value="default">默认界面</option>
          </select>
        </p>        
        <p class="loginnofloat">
          <input name="submit" value="提交"  tabindex="4" type="submit" class="btn" />
        </p>
  </form>
  <script type="text/JavaScript">document.getElementById('loginform').username.focus();</script>
    
<?php $this->_endblock(); ?>
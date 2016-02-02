<div class="innerContent">
  
  <?php if ( isset( $error ) ) { ?>
  		<p class="settingSaved"><?php echo $error; ?></p>
  <?php } ?>
  
  <form id="loginform" method="post" action="<?php echo $this->anchor(); ?>">
  
  	<p class="clearfix"><label for="user"><?php echo $label_user ?></label><input class="text" type="text" name="user" id="user" value="<?php echo $user; ?>" /></p>
  	<p class="clearfix"><label for="pass"><?php echo $label_pass ?></label><input class="text" type="password" name="pass" id="pass" value="<?php echo $pass; ?>" /></p>
  	<p class="clearfix"><input class="button" type="submit" name="submit" value="<?php echo $submit; ?>" /> <!-- <input type="submit" name="remind" value="<?php echo $remind; ?>" /> --></p>
  	
  </form>
  
</div>
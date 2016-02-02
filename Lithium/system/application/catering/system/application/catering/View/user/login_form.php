<form id="loginform" method="post" action="<?php echo $this->anchor(); ?>">
<table border="0">
	<tr><td><?php echo $label_user ?></td><td><input type="text" name="user" value="<?php echo $user; ?>" /></td></tr>
	<tr><td><?php echo $label_pass ?></td><td><input type="password" name="pass" value="<?php echo $pass; ?>" /></td></tr>
	<tr><td> </td><td><input type="submit" name="submit" value="<?php echo $submit; ?>" /> <!-- <input type="submit" name="remind" value="<?php echo $remind; ?>" /> --></td></tr>
	<?php if ( isset( $error ) ) { ?>
		<tr><td colspan="2"><?php echo $error; ?></td></tr>
	<?php } ?>
</table>
</form>
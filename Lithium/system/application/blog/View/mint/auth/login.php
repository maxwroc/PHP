<form action="" method="post">
<?php if ( ! empty( $aErrorMessages ) ) { ?>
	<p class="error">
		<?php foreach ( $aErrorMessages as $sMessage ) echo $sMessage, '<br />'; ?>
	</p>
<?php } ?>
	<p><label for="email"><?php echo $sLoginLabel; ?></label><br /><input type="text" id="email" name="email" /></p>
	<p><label for="password"><?php echo $sPasswordLabel; ?></label><br /><input type="password" id="password" name="password" /></p>
	<p><input type="submit" value="<?php echo $sSubmitLabel; ?>" /></p>
</form>
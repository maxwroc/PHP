<div class="innerContent">
	<?php if ( isset( $error ) ) { ?>
		<p class="settingSaved"><?php echo $error; ?></p>
	<?php } ?>
	<form method="post" action="<?php echo $this->anchor( '/user/register/' ); ?>">
		<p class="clearfix">
			<label for="user_name"><?php echo $label_user; ?></label>
			<input class="text" type="text" name="user_name" id="user_name" value="<?php echo $user_name; ?>" />
		</p>
		<p class="clearfix">
			<label for="user_pass"><?php echo $label_pass; ?></label>
			<input class="text" type="password" name="user_pass" id="user_pass" value="<?php echo $user_pass; ?>" />
		</p>
		<p class="clearfix">
			<label for="user_email"><?php echo $label_email; ?></label>
			<input class="text" type="text" name="user_email" id="user_email" value="<?php echo $user_email; ?>" />
		</p>
		<p>&nbsp;</p>
		<p class="clearfix">
			<label for="account_name"><?php echo $label_accountname; ?></label>
			<input class="text" type="text" name="account_name" id="account_name" value="<?php echo $account_name; ?>" />
		</p>
		<p>&nbsp;</p>
		<p class="clearfix">
			<label for="submit">&nbsp;</label>
			<input class="button" type="submit" name="submit" id="submit" value="<?php echo $submit; ?>" /> 
			<!-- <input type="submit" name="remind" value="<?php echo $remind; ?>" /> -->
		</p>
	</form>
</div>

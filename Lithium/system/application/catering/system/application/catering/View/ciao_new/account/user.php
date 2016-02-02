<h3><?php echo $section_title; ?></h3>

<a href="<?php echo $this->anchor( '/account/users/' ); ?>"><?php echo $user_list; ?></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo $this->anchor( '/account/user/' ); ?>"><?php echo $add_user; ?></a>

<br />
<br />
<?php if ( isset( $bPrintForm ) ) { ?>
	<form method="post" action="<?php echo $this->anchor( '/account/user/' ); ?>">
	<table border="0">
	
		<tr><td><?php echo $label_name; ?></td><td><input type="text" name="name" value="<?php echo $name; ?>" /></td></tr>
		<tr><td><?php echo $label_email; ?></td><td><input type="text" name="email" value="<?php echo $email; ?>" /></td></tr>
		
		<?php if ( isset( $user_id ) ) { ?>
			<tr><td><?php echo $label_since; ?></td><td><input type="text" name="since" value="<?php echo $since; ?>" disabled="disabled" /></td></tr>
			<tr><td><?php echo $label_last; ?></td><td><input type="text" name="last" value="<?php echo $last; ?>" disabled="disabled" /></td></tr>
		<?php } else { ?>
			<tr><td><?php echo $label_pass; ?></td><td><input type="password" name="pass" value="" /></td></tr>
		<?php } ?>
		
		<tr><td><?php echo $label_role; ?></td><td><select name="role">
		<?php foreach ( $aRoles as $aRole ) { ?>
			<option value="<?php echo $aRole['role_id']; ?>"<?php echo ( $aRole['role_id'] == $role ? ' selected="selected"' : '' ); ?>><?php echo $aRole['name'] ?></option>
		<?php } ?>
		</select></td></tr>
		
		<tr><td> </td><td> </td></tr>
		
		<tr>
			<td> </td>
			<td>
				<input type="submit" name="submit" value="<?php echo $submit; ?>" />
			<?php if ( isset( $user_id ) ) { ?>
				&nbsp;&nbsp;
				<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
				<input type="button" name="delete" value="<?php echo $delete; ?>" onclick="location.href='<?php echo $this->anchor( '/account/user/delete/%s/', $user_id ); ?>'" />
			<?php } ?>
			</td>
		</tr>
		
		<?php if ( isset( $error ) ) { ?>
			<tr><td colspan="2"><?php echo $error; ?></td></tr>
		<?php } ?>
		
	</table>
	</form>
<?php } elseif ( isset( $info ) ) { ?>
	<p><?php echo $info; ?></p>
<?php } ?>

<?php if ( isset( $question ) ) { ?>
<p style="text-align: center; border: solid 2px red; padding: 10px;">
	<?php echo $question; ?><br /><br />
	<input type="button" value="<?php echo $yes; ?>" onclick="location.href='<?php echo $this->anchor( 'account/user/delete/%s/%s/', $user_id, $token ); ?>'" />&nbsp;&nbsp;
	<input type="button" value="<?php echo $no; ?>" onclick="location.href='<?php echo $this->anchor( 'account/users/' ); ?>'" />
</p>
<?php } ?>
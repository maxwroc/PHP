<?php if ( isset( $sInfo ) ) { ?>
	<p><?php echo $sInfo; ?></p>
<?php } ?>

<?php echo $aChangePassForm[ 'sTitle' ]; ?>
<form method="post" action="<?php echo $this->anchor(); ?>">
<label for="oldpass"><?php echo $aChangePassForm[ 'sOldPass' ]; ?></label> <input type="password" name="oldpass" value="" /><br />
<label for="newpass"><?php echo $aChangePassForm[ 'sNewPass' ]; ?></label> <input type="password" name="newpass" value="" /><br />
<label for="newpassconfirm"><?php echo $aChangePassForm[ 'sNewPassConfirm' ]; ?></label> <input type="password" name="newpassconfirm" value="" /><br />
<input type="submit" name="submit" value="<?php echo $aChangePassForm[ 'sSubmit' ]; ?>" />
</form>
<br />
<br />
<?php echo $aLayoutForm[ 'sTitle' ]; ?>
<form method="post" action="<?php echo $this->anchor(); ?>">
<select name="layout">
<?php foreach ( $aLayoutForm[ 'aOptions' ] as $aOption ) { ?>
	<option value="<?php echo $aOption[ 'value' ]; ?>"<?php echo ( $aOption[ 'value' ] == $aLayoutForm[ 'value' ] ? ' selected="selected"' : '' ); ?>>
		<?php echo $aOption[ 'text' ]; ?>
	</option>
<?php } ?>
</select>
<input type="submit" name="submit" value="<?php echo $aLayoutForm[ 'sSubmit' ]; ?>" />
</form>
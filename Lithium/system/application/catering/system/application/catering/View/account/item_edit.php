<?php if ( isset( $bPrintForm ) ) { ?>
	<form method="post" action="<?php echo $this->anchor(); ?>">
	<table border="0">
		<?php foreach( $aInputs as $aInput ) { ?>
		
			<?php switch ( $aInput[ 'type' ] ) { 
				
				case 'select' : ?>
				<tr><td><?php echo $aInput[ 'label' ]; ?></td><td><select name="<?php echo $aInput[ 'name' ]; ?>"<?php echo ( isset( $aInput[ 'class' ] ) ? ' class="' . $aInput[ 'class' ] . '"' : '' ); ?>>
				<?php foreach ( $aInput[ 'items' ] as $aItem ) { ?>
					<option value="<?php echo $aItem[ 'value' ]; ?>"<?php echo ( $aItem[ 'value' ] == $aInput[ 'value' ] ? ' selected="selected"' : '' ); ?>>
						<?php echo $aItem[ 'name' ] ?>
					</option>
				<?php } ?>
				</select></td></tr>
				<?php break; ?>
				
			<?php default : ?>
				<tr><td><?php echo $aInput[ 'label' ]; ?></td><td><input type="<?php echo $aInput[ 'type' ]; ?>" name="<?php echo $aInput[ 'name' ]; ?>" value="<?php echo $aInput[ 'value' ]; ?>"<?php echo ( isset( $aInput[ 'disabled' ] ) ? ' disabled="disabled"' : '' ); ?><?php echo ( isset( $aInput[ 'class' ] ) ? ' class="' . $aInput[ 'class' ] . '"' : '' ); ?> /></td></tr>
			<?php } ?>
		<?php } ?>

		
		
		
		<tr><td> </td><td> </td></tr>
		
		<tr>
			<td> </td>
			<td>
			<?php if ( isset( $submit ) ) { ?>
				<input type="submit" name="submit" value="<?php echo $submit; ?>" />
			<?php } ?>
			<?php if ( isset( $sTextDelete ) ) { ?>
				&nbsp;&nbsp;
				<input type="hidden" name="item_id" value="<?php echo $item_id; ?>" />
				<input type="button" name="delete" value="<?php echo $sTextDelete; ?>" onclick="location.href='<?php echo $this->anchor( $sLinkDelete ); ?>'" />
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

<?php if ( isset( $sQuestion ) ) { ?>
<p style="text-align: center; border: solid 2px red; padding: 10px;">
	<?php echo $sQuestion; ?><br /><br />
	<input type="button" value="<?php echo $sTextYes; ?>" onclick="location.href='<?php echo $this->anchor( $sLinkYes ); ?>'" />&nbsp;&nbsp;
	<input type="button" value="<?php echo $sTextNo; ?>" onclick="location.href='<?php echo $this->anchor( $sLinkNo ); ?>'" />
</p>
<?php } ?>
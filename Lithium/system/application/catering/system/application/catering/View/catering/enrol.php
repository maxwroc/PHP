<form method="post" action="<?php echo $this->anchor(); ?>">
<table class="enrol"><tbody>
<?php $iCount = 0; ?>
<?php foreach ( $aWeek as $sDate => $aDay ) { ?>
	<tr>
		<td class="top"><?php echo $aDay[ 'sWeekday' ]; ?></td>
		<td class="top"><?php echo $sDate; ?></td>
		<td>
		<table cellspacing="0" cellpadding="0"><tbody>
		<?php foreach ( $aDay[ 'aMeals' ] as $aMeal ) { ?>
		<tr>
			<td class="top">
			<?php if ( $aMeal[ 'optional' ] ) { ?>
			<input type="checkbox" name="day<?php echo $iCount; ?>[]" value="<?php echo $aMeal[ 'meal_id' ]; ?>"<?php echo ( $aMeal[ 'bDisabled' ] ? ' disabled="disabled"' : ''  ); ?><?php echo ( $aMeal[ 'bChecked' ] ? ' checked="checked"' : ''  ); ?> />
			<?php } else { ?>
			<input type="radio" name="day<?php echo $iCount; ?>[]" value="<?php echo $aMeal[ 'meal_id' ]; ?>"<?php echo ( $aMeal[ 'bDisabled' ] ? ' disabled="disabled"' : ''  ); ?><?php echo ( $aMeal[ 'bChecked' ] ? ' checked="checked"' : ''  ); ?> />
			<?php } ?>
			</td>
			<td>
			<?php echo $aMeal[ 'fname' ]; ?> <?php echo $aMeal[ 'name' ]; ?> 
			<?php if ( isset( $aMeal[ 'price' ] ) ) { ?>
			[<?php echo $aMeal[ 'price' ]; ?>]
			<?php } ?>
			</td>
		</tr>
		<?php } ?>
		</tbody></table>
		</td>
	</tr>
<?php $iCount++; } ?>
<tr>
	<td class="end"><input type="button" value="<?php echo $sPrevText; ?>" onclick="location.href='<?php echo $this->anchor( $sPrevLink ); ?>'" class="button" /> </td>
	<td class="end">
	<?php if ( $iCount > 0 ) { ?>
		<input type="submit" name="submit" value="<?php echo $submit; ?>" class="button" />
	<?php } ?>
	</td>
	<td class="right end"><input type="button" class="button" name="next" value="<?php echo $sNextText ?>" onclick="location.href='<?php echo $this->anchor( $sNextLink ); ?>'" class="button" /></td>
</tr>
</tbody></table>
</form>
<div class="fulllen right"></div>
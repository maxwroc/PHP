<?php if ( ! empty( $aCalendar ) ) { ?>
<table class="calendar"><tbody>

<tr>
	<td><input type="button" value="&lt;&lt;" onclick="javascript:location.href='<?php echo $this->anchor( $sPrevLink ); ?>';" /></td>
	<td colspan="6" class="center"><?php echo $sMonthName; ?></td>
	<td><input type="button" value="&gt;&gt;" onclick="javascript:location.href='<?php echo $this->anchor( $sNextLink ); ?>';" /></td>
</tr>

<tr>
<?php foreach( $aWeekDays as $sDay ) { ?>
	<th><?php echo $sDay; ?></th>
<?php } // foreach ?>
</tr>

<?php foreach( $aCalendar as $aWeek ) { ?>
	<tr>
	<?php foreach( $aWeek as $aDay ) { ?>
		<td>
		
		<?php 
			$aDayClasses[0] = 'CallendarSelectedDay'; 
			$aDayClasses[1] = 'CallendarInactiveDay'; 
		?>
		
		<?php if ( is_array( $aDay ) ) { ?>
			<input type="button" value="<?php echo $aDay[ 'sText' ] ?>" onclick="javascript:location.href='<?php echo $this->anchor( $aDay[ 'sLink' ] ); ?>';"<?php echo isset( $aDay[ 'iClass' ] ) ? ' class="' . $aDayClasses[ $aDay[ 'iClass' ] ] . '"' : ''; ?> />
		<?php } elseif ( ! empty( $aDay ) ) { ?>
			<input type="button" value="[<?php echo $aDay; ?>]" />
		<?php } else { ?>
			&nbsp;
		<?php } // if ?>
		</td>
	<?php } // foreach ?>
	</tr>
<?php } // foreach ?>
</tbody></table>
<?php } // if ?>
<?php if ( isset( $calendar ) ) { ?>
<div align="center">
<?php echo $calendar ?>
</div>
<br />
<br />
<?php } // if ?>

<?php if ( isset( $aForm ) ) { ?>
	<form action="<?php echo $this->anchor(); ?>" method="post" />
	<?php echo $aForm[ 'sPeriod' ]; ?> 
	<label for="from"><?php echo $aForm[ 'sFrom' ]; ?></label> <input type="text" name="from" value="<?php echo $aForm[ 'sDateFrom' ] ?>" /> 
	<label for="to"><?php echo $aForm[ 'sTo' ] ?></label> <input type="text" name="to" value="<?php echo $aForm[ 'sDateTo' ] ?>" /> 
	<input type="submit" name="submit" value="<?php echo $aForm[ 'sSubmit' ] ?>" /> 
	</form>
<?php } // if $aForm ?>

<?php if ( isset( $error ) ) { ?>
	<p><?php echo $error ?></p> 
<?php } ?>

<?php if ( isset( $aSummary ) ) { ?>
<br />
<br />
<table class="itemlist"><tbody>
<?php $i=0; foreach( $aSummary as $iKeyX => $aRow ) { ?>
	<?php if ( ! ($i++ % 2) ) { ?>
	<tr class="itemlist0">
	<?php } else { ?>
	<tr class="itemlist1">
	<?php } ?>
	
	<?php foreach( $aRow as $iKeyY => $sColumn ) { ?>
		<?php if ( ( $iKeyX == 0 ) OR ( $iKeyY == 0 ) ) { ?>
			<th><?php echo $sColumn; ?></th>
		<?php } else { ?>
			<td><?php echo $sColumn; ?></td>
		<?php } ?>
	<?php } ?>
	
	</tr>
<?php } ?>
</tbody></table>
<?php } // if $aSummary ?>


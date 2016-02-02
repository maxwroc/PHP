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
<tr>
<?php foreach( $aSummary[ 'aColumns' ] as $sColumn ) { ?>
	<th><?php echo $sColumn; ?></th>
<?php } ?>
</tr>
<?php for( $i = 0; $i < count( $aSummary[ 'aPositions' ] ); $i++ ) { ?>
	<?php if ( ! ($i % 2) ) { ?>
	<tr class="itemlist0">
	<?php } else { ?>
	<tr class="itemlist1">
	<?php } ?>
	<td><?php echo $aSummary[ 'aPositions' ][ $i ][ 'name' ]; ?></td>
	<td><?php echo $aSummary[ 'aPositions' ][ $i ][ 'single_price' ]; ?></td>
	<td><?php echo $aSummary[ 'aPositions' ][ $i ][ 'quantity' ]; ?></td>
	<td><?php echo $aSummary[ 'aPositions' ][ $i ][ 'price' ]; ?></td>
</tr>
<?php } ?>
<tr>
<?php foreach( $aSummary[ 'aFooter' ] as $sValue ) { ?>
	<td><?php echo $sValue; ?></td>
<?php } ?>
</tr>
</tbody></table>
<?php } // if $aSummary ?>


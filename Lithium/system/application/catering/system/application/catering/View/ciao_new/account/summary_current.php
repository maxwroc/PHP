<script type="text/javascript">
	function changeRange(oInput) {
		
		if ( oInput.id == 'from' ) {
			return {maxDate: $('#to').datepicker( 'getDate' )};
		} else if ( oInput.id == 'to' ) {
			return {minDate: $('#from').datepicker( 'getDate' )};
		}
		
	}
	
	$(function() {
		$(".datepicker").datepicker({
			beforeShow: changeRange,
			dateFormat: 'yy-mm-dd'
		});
	});
</script>

<div class="innerContent">
<?php if ( isset( $calendar ) ) { ?>
<div align="center">
<?php echo $calendar ?>
</div>
<br />
<br />
<?php } // if ?>

<?php if ( isset( $aForm ) ) { ?>
	<?php if ( isset( $error ) ) { ?>
		<p class="settingSaved"><?php echo $error; ?></p>
	<?php } ?>
	
	<form action="<?php echo $this->anchor(); ?>" method="post" />
	<?php echo $aForm[ 'sPeriod' ]; ?> <br/>
	<label for="from"><?php echo $aForm[ 'sFrom' ]; ?></label> <input type="text" id="from" name="from" class="datepicker" value="<?php echo $aForm[ 'sDateFrom' ] ?>" /><div class="calendar_icon">&nbsp;</div> <br/>
	<label for="to"><?php echo $aForm[ 'sTo' ] ?></label> <input type="text" id="to" name="to" class="datepicker" value="<?php echo $aForm[ 'sDateTo' ] ?>" /> <br/>
	<label for="separate_day"><?php echo $aForm[ 'sSeparateDay' ] ?></label> <input type="checkbox" id="separate_day" name="separate_day"<?php echo $aForm[ 'bSeparateDay' ] ? ' checked="checked"' : ''; ?>" /> <br/>
	<input class="button" type="submit" name="submit" value="<?php echo $aForm[ 'sSubmit' ] ?>" /> 
	</form>
<?php } // if $aForm ?>
</div>

<?php if ( isset( $aSummary ) ) { ?>
	<?php foreach( $aSummary as $aRow ) { ?>
		<br />
		<div class="topBar"><?php echo $aRow[ 'sDate' ]; ?><?php echo isset( $aRow[ 'sWeekDay' ] ) ? ', ' . $aRow[ 'sWeekDay' ] : ''; ?></div>
		<table class="itemlist"><tbody>
		<tr>
		<?php foreach( $aRow[ 'aColumns' ] as $sColumn ) { ?>
			<th><?php echo $sColumn; ?></th>
		<?php } ?>
		</tr>
		<?php for( $i = 0; $i < count( $aRow[ 'aPositions' ] ); $i++ ) { ?>
			<?php if ( ! ($i % 2) ) { ?>
			<tr class="itemlist0">
			<?php } else { ?>
			<tr class="itemlist1">
			<?php } ?>
			<td><?php echo $aRow[ 'aPositions' ][ $i ][ 'name' ]; ?></td>
			<td><?php echo $aRow[ 'aPositions' ][ $i ][ 'single_price' ]; ?></td>
			<td><?php echo $aRow[ 'aPositions' ][ $i ][ 'quantity' ]; ?></td>
			<td><?php echo $aRow[ 'aPositions' ][ $i ][ 'price' ]; ?></td>
		</tr>
		<?php } ?>
		<tr>
		<?php foreach( $aRow[ 'aFooter' ] as $sValue ) { ?>
			<td><?php echo $sValue; ?></td>
		<?php } ?>
		</tr>
		</tbody></table>
	<?php } ?>
<?php } // if $aSummary ?>
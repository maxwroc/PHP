
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

<?php if ( isset( $error ) ) { ?>
	<p class="settingSaved"><?php echo $error ?></p> 
<?php } ?>

<?php if ( isset( $calendar ) ) { ?>
<div align="center">
<?php echo $calendar ?>
</div>
<br />
<br />
<?php } // if ?>

<?php if ( isset( $aForm ) ) { ?>
	<form action="<?php echo $this->anchor(); ?>" method="post" />
	<p>
  <?php echo $aForm[ 'sPeriod' ]; ?> 
 </p>
 <p>
  	<label style="float:none;" for="from"><?php echo $aForm[ 'sFrom' ]; ?></label> 
  	<input type="text" name="from" id="from" class="datepicker" value="<?php echo $aForm[ 'sDateFrom' ]; ?>" />
  	
	<label style="float:none;" for="to"><?php echo $aForm[ 'sTo' ]; ?></label> 
	<input type="text" name="to" id="to" class="datepicker" value="<?php echo $aForm[ 'sDateTo' ] ?>" /> 
	
	<input class="button" type="submit" name="submit" value="<?php echo $aForm[ 'sSubmit' ] ?>" /> 
	<input class="button" type="submit" name="submit" value="<?php echo $aForm[ 'sSubmitExcel' ] ?>" /> 
 </p>
	</form>
<?php } // if $aForm ?>



</div>

<?php if ( isset( $aSummary ) ) { ?>
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
			<td><?php echo $sColumn; ?></td>
		<?php } else { ?>
			<td><?php echo $sColumn; ?></td>
		<?php } ?>
	<?php } ?>
	
	</tr>
<?php } ?>
</tbody></table>
<?php } // if $aSummary ?>


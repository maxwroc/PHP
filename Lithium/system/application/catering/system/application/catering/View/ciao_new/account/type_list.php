<script type="text/javascript">
function showAddForm() {
	document.getElementById( 'AddButton' ).style.display='none';
	document.getElementById( 'AddForm' ).style.display='';
}
</script>

<div class="innerContent">

<input class="button" id="AddButton" type="button" value="<?php echo $sAddType; ?>" onclick="javascript:showAddForm();" />
<form method="post" action="<?php echo $this->anchor(); ?>" id="AddForm" style="display: none;">

  <p class="clearfix"><label for="name"><?php echo $sName ?></label> <input class="text" type="text" name="name" id="name" value=""  /></p>
	
  <p class="clearfix" style="margin-top:5px;"><input class="button" type="submit" name="submit" value="<?php echo $submit; ?>"  /></p>
	
</form>
</div>
<br />
<?php if ( ! empty( $aList ) ) { ?>
<table class="itemlist" width="100%" cellspacing="0" cellpadding="0">
<tbody>
	<tr>
	<?php foreach( $aColumns as $sColumn ) { ?>
		<th><?php echo $sColumn; ?></th>
	<?php } ?>
	</tr>

<?php for( $i = 0; $i < count( $aList ); $i++ ) { ?>
	<tr class="itemlist<?php echo ($i % 2 ); ?>">
	
	<?php foreach( $aList[ $i ] as $aProperties ) { ?>
		<td<?php echo isset( $aProperties[ 'sId' ] ) ? ' id="' . $aProperties[ 'sId' ] . '"' : '' ?>>
		
		<?php if ( isset( $aProperties['sOnclick'] ) ) { ?>
			<input type="button" class="button" name="name" value="Edytuj" onclick="<?php echo $aProperties['sOnclick'] ?>" />
			<?php continue; ?>
		<?php } ?>
		
		<?php if( isset( $aProperties[ 'sLink' ] ) ) { ?>
			<a href="<?php echo $this->anchor( $aProperties[ 'sLink' ] ); ?>">
		<?php } ?>
		<?php echo $aProperties[ 'sText' ]; ?>
		<?php if( isset( $aProperties[ 'sLink' ] ) ) { ?>
			</a>
		<?php } ?>
		
		</td>
	<?php } ?>
	</tr>
<?php } ?>

</tbody></table>
<?php } // if ?>




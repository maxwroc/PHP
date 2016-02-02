<script type="text/javascript">
function showAddForm() {
	document.getElementById( 'AddButton' ).style.display='none';
	document.getElementById( 'AddForm' ).style.display='';
}
</script>
<input id="AddButton" type="button" value="<?php echo $sAddType; ?>" onclick="javascript:showAddForm();" />
<form method="post" action="<?php echo $this->anchor(); ?>" id="AddForm" style="display: none;">
<table><tbody>
	<tr>
		<td><?php echo $sName ?></td>
		<td><input type="text" name="name" value=""  /></td>
	</tr>
	<tr>
		<td> </td>
		<td><input type="submit" name="submit" value="<?php echo $submit; ?>"  /></td>
	</tr>
</tbody></table>
</form>
<?php if ( ! empty( $aList ) ) { ?>
<table><tbody>
	<tr>
	<?php foreach( $aColumns as $sColumn ) { ?>
		<th><?php echo $sColumn; ?></th>
	<?php } ?>
	</tr>

<?php for( $i = 0; $i < count( $aList ); $i++ ) { ?>
	<?php if ( ! $i % 2 ) { ?>
	<tr class="itemlist0">
	<?php } else { ?>
	<tr class="itemlist1">
	<?php } ?>
	
	<?php foreach( $aList[ $i ] as $aProperties ) { ?>
		<td>
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
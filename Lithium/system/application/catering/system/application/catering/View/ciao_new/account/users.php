<?php if ( ! empty( $aUsers ) ) { ?>
<table class="itemlist"><tbody>
	<tr class="itemlist_header">
		<th><?php echo $aColumn[ 0 ]; ?></th><th><?php echo $aColumn[ 1 ]; ?></th><th><?php echo $aColumn[ 2 ]; ?></th>
	</tr>
	
	<?php for( $i = 0; $i < count( $aUsers ); $i++ ) { ?>
	<?php if ( ! ($i % 2) ) { ?>
	<tr class="itemlist0">
	<?php } else { ?>
	<tr class="itemlist1">
	<?php } ?>
		<td><a href="<?php echo $this->anchor( '/account/user/%s/', $aUsers[ $i ][ 'user_id' ] ); ?>"><?php echo $aUsers[ $i ][ 'name' ]; ?></a></td><td><?php echo $aUsers[ $i ][ 'email' ]; ?></td><td><?php echo $aUsers[ $i ][ 'role_id' ]; ?></td>
	</tr>
	<?php } ?>
	
</tbody></table>
<?php } ?>
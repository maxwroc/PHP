<?php if ( ! empty( $aList ) ) { ?>
<table class="itemlist"><tbody>
	<tr class="itemlist_header">
	<?php foreach( $aColumns as $sColumn ) { ?>
		<th><?php echo $sColumn; ?></th>
	<?php } ?>
	</tr>
	
<?php for( $i = 0; $i < count( $aList ); $i++ ) { ?>
	<?php if ( ! ($i % 2) ) { ?>
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
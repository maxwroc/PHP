<?php if ( ! empty( $aList ) ) { ?>
<table class="itemlist" cellpadding="0" cellspacing="0"><tbody>
	<tr class="itemlist_header">
	<?php foreach( $aColumns as $mColumn ) { ?>
		<th>
		<?php if ( is_array( $mColumn ) ) { ?>
			<a href="<?php echo $mColumn['url']; ?>"><?php echo $mColumn['text']; ?></a> 
			<?php if ( ! empty( $mColumn['img'] ) ) { ?>
				<img src="<?php echo $mColumn['img']; ?>" alt="" />
			<?php } ?>
		<?php } else { ?>
			<?php echo $mColumn; ?>
		<?php } ?>
		</th>
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

<?php if ( ! empty( $mPagination ) ) { echo $mPagination; } // Pagination ?>

<?php } // if ?>
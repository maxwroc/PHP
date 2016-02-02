<h3><?php echo $section_title; ?></h3>

<a href="<?php echo $this->anchor( $sLink ); ?>"><?php echo $sLinkText; ?></a>
<br />
<br />

<?php if ( ! empty( $aList ) ) { ?>
<h4><?php echo $list_header ?>:</h4>
<table><tbody>
	<tr>
	<?php foreach( $aColumns as $sColumn ) { ?>
		<th><?php echo $sColumn; ?></th>
	<?php } ?>
	</tr>
<?php foreach( $aList as $aItem ) { ?>
	<tr>
	<?php foreach( $aItem as $aProperties ) { ?>
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
<?php } // foreach list ?>
</tbody></table>
<?php } else { ?>
	<p><?php echo $no_meals; ?></p>
<?php } ?>
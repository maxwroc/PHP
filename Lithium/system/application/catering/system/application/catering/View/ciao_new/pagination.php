<div class="clearfix">
<div id="pagination">
	<span class="label"><?php echo $sLabel; ?>:</span>
	<ul>
	<?php if ( ! empty( $aFirst['url'] ) ) { ?>
		<li class="first">
			<a href="<?php echo $aFirst['url']; ?>"><?php echo $aFirst['text']; ?></a> ...
		</li>
	<?php } ?>
	<?php foreach ( $aPages as $aPage ) { ?>
		<li>
			<?php if ( empty( $aPage['url'] ) ) { ?>
				<?php echo $aPage['text']; ?>
			<?php } else { ?>
				<a href="<?php echo $aPage['url']; ?>"><?php echo $aPage['text']; ?></a>
			<?php } ?>
		</li>
	<?php } ?>
	<?php if ( ! empty( $aLast['url'] ) ) { ?>
		<li class="last">
			... <a href="<?php echo $aLast['url']; ?>"><?php echo $aLast['text']; ?></a>
		</li>
	<?php } ?>
	</ul>
	&nbsp;
	<span class="prev_next">
	<?php if ( ! empty( $aPrevious['url'] ) ) { ?>
		&laquo; <a href="<?php echo $aPrevious['url']; ?>"><?php echo $aPrevious['text']; ?></a>
	<?php } ?>
	&nbsp;
	<?php if ( ! empty( $aNext['url'] ) ) { ?>
		<a href="<?php echo $aNext['url']; ?>"><?php echo $aNext['text']; ?> &raquo;</a>
	<?php } ?>
	</span>
</div>
</div>
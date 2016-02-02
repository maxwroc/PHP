<div class="innerContent">
<?php if ( isset( $bPrintForm ) ) { ?>
	<form method="post" action="<?php echo $this->anchor( ( isset( $sAction ) ? $sAction : '' ) ); ?>">
	
		<?php foreach( $aInputs as $aInput ) { ?>
		
			<?php switch ( $aInput[ 'type' ] ) { 
				
				case 'select' : ?>
			  <p class="clearfix">
          <label><?php echo $aInput[ 'label' ]; ?></label>
          <select name="<?php echo $aInput[ 'name' ]; ?>"<?php echo ( isset( $aInput[ 'class' ] ) ? ' class="' . $aInput[ 'class' ] . '"' : '' ); ?>>
  				<?php foreach ( $aInput[ 'items' ] as $aItem ) { ?>
  					<option value="<?php echo $aItem[ 'value' ]; ?>"<?php echo ( $aItem[ 'value' ] == $aInput[ 'value' ] ? ' selected="selected"' : '' ); ?>>
  						<?php echo $aItem[ 'name' ] ?>
  					</option>
  				<?php } ?>
  				</select>
        </p>
				<?php break; ?>
				
			<?php default : ?>
			<p class="clearfix">
        <label><?php echo $aInput[ 'label' ]; ?></label>
        <input class="<?php echo $aInput[ 'type' ]; ?>" type="<?php echo $aInput[ 'type' ]; ?>" name="<?php echo $aInput[ 'name' ]; ?>" value="<?php echo $aInput[ 'value' ]; ?>"<?php echo ( isset( $aInput[ 'disabled' ] ) ? ' disabled="disabled"' : '' ); ?><?php echo ( isset( $aInput[ 'class' ] ) ? ' class="' . $aInput[ 'class' ] . '"' : '' ); ?> />
      </p>
			<?php } ?>
		<?php } ?>

			<?php if ( isset( $submit ) ) { ?>
				<input class="button" type="submit" name="submit" value="<?php echo $submit; ?>" />
			<?php } ?>
			<?php if ( isset( $sTextDelete ) ) { ?>
				&nbsp;&nbsp;
				<input type="hidden" name="item_id" value="<?php echo $item_id; ?>" />
				<input class="button" type="button" name="delete" value="<?php echo $sTextDelete; ?>" onclick="location.href='<?php echo $this->anchor( $sLinkDelete ); ?>'" />
			<?php } ?>
		
		<?php if ( isset( $error ) ) { ?>
		<p>
      <?php echo $error; ?>
    </p>
		<?php } ?>
	</form>
<?php } elseif ( isset( $info ) ) { ?>
	<p><?php echo $info; ?></p>
<?php } ?>

<?php if ( isset( $sQuestion ) ) { ?>
<p style="text-align: center; border: solid 2px red; padding: 10px;">
	<?php echo $sQuestion; ?><br /><br />
	<input class="button" type="button" value="<?php echo $sTextYes; ?>" onclick="location.href='<?php echo $this->anchor( $sLinkYes ); ?>'" />&nbsp;&nbsp;
	<input class="button" type="button" value="<?php echo $sTextNo; ?>" onclick="location.href='<?php echo $this->anchor( $sLinkNo ); ?>'" />
</p>
<?php } ?>

</div>

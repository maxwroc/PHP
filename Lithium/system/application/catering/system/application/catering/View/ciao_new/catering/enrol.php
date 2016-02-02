<br />
<form method="post" action="<?php echo $this->anchor(); ?>">
  <div class="innerContent" style="text-align:center;">
  	<input class="button" style="font-size:10px;padding:1px;" type="button" value="<?php echo $sPrevText; ?>" onclick="location.href='<?php echo $this->anchor( $sPrevLink ); ?>'" />  
<?php if ( isset( $submit ) ) { ?>
  	<input class="button" style="margin:0 10px;font-weight:bold" type="submit" name="submit" value="<?php echo $submit; ?>" />
<?php } ?>
  	<input class="button" style="font-size:10px;padding:1px;" type="button" class="button" name="next" value="<?php echo $sNextText ?>" onclick="location.href='<?php echo $this->anchor( $sNextLink ); ?>'" />
  </div>
  <?php $iCount = 0; ?>
  <?php foreach ( $aWeek as $sDate => $aDay ) { ?>
    <div class="topBar">
      <?php echo $aDay[ 'sWeekday' ]; ?>, <?php echo $sDate; ?>
    </div>
		<table class="itemlist" width="100%" cellspacing="0" cellpadding="0">
      <tbody>
  		  <?php $i=0; foreach ( $aDay[ 'aMeals' ] as $aMeal ) { ?>
  		    <?php if ( ! ( $i % 2 ) ) { ?>
          <tr class="itemlist0">
          <?php } else { ?>
          <tr class="itemlist1">
          <?php } ?>
            <td>
      			<?php echo isset( $aMeal[ 'fname' ] ) ? $aMeal[ 'fname' ] : ''; ?> 
    			  </td>
      			<td>
      			<?php if ( $aMeal[ 'bChecked' ] ) { ?>
      				<b><?php echo $aMeal[ 'name' ]; ?></b> 
      			<?php } else { ?>
      				<?php echo $aMeal[ 'name' ]; ?>
      			<?php } ?> 
      			
      			<?php if ( isset( $aMeal[ 'price' ] ) ) { ?>
      		  [<?php echo $aMeal[ 'price' ]; ?>]
    			  <?php } ?>
    			  </td>
    			   
            <td style="width:20px;">
      			<?php if ( $aMeal[ 'optional' ] ) { ?>
      			<input type="checkbox" name="day<?php echo $iCount; ?>[]" value="<?php echo $aMeal[ 'meal_id' ]; ?>"<?php echo ( $aMeal[ 'bDisabled' ] ? ' disabled="disabled" style="visibility:hidden"' : ''  ); ?><?php echo ( $aMeal[ 'bChecked' ] ? ' checked="checked"' : ''  ); ?> />
      			<?php } else { ?>
      			<input type="radio" name="day<?php echo $iCount; ?>[]" value="<?php echo $aMeal[ 'meal_id' ]; ?>"<?php echo ( $aMeal[ 'bDisabled' ] ? ' disabled="disabled" style="visibility:hidden"' : ''  ); ?><?php echo ( $aMeal[ 'bChecked' ] ? ' checked="checked"' : ''  ); ?> />
      			<?php } ?>
      			</td>
  		    </tr>
  		  <?php $i++; } ?>
		  </tbody>
    </table>
<?php $iCount++; } ?>
<div class="innerContent" style="text-align:center;border-top:0px none;">
	<input class="button" style="font-size:10px;padding:1px;" type="button" value="<?php echo $sPrevText; ?>" onclick="location.href='<?php echo $this->anchor( $sPrevLink ); ?>'" /> 
<?php if ( isset( $submit ) ) { ?>
	<input class="button" style="margin:0 10px;font-weight:bold" type="submit" name="submit" value="<?php echo $submit; ?>" />
<?php } ?>
	<input class="button" style="font-size:10px;padding:1px;" type="button" class="button" name="next" value="<?php echo $sNextText ?>" onclick="location.href='<?php echo $this->anchor( $sNextLink ); ?>'" />
</div>
</form>

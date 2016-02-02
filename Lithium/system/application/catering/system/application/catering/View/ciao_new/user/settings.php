<div class="innerContent">
  
    <?php if ( isset( $sInfo ) ) { ?>
    	<p class="settingSaved" style=""><?php echo $sInfo; ?></p>
    <?php } ?>
  
  <?php echo $aChangePassForm[ 'sTitle' ]; ?>
  <form method="post" action="<?php echo $this->anchor(); ?>">
    <p class="clearfix"><label for="oldpass"><?php echo $aChangePassForm[ 'sOldPass' ]; ?></label> <input class="text" type="password" name="oldpass" id="oldpass" value="" /></p>
    <p class="clearfix"><label for="newpass"><?php echo $aChangePassForm[ 'sNewPass' ]; ?></label> <input class="text" type="password" name="newpass" id="newpass" value="" /></p>
    <p class="clearfix"><label for="newpassconfirm"><?php echo $aChangePassForm[ 'sNewPassConfirm' ]; ?></label> <input class="text" type="password" name="newpassconfirm" id="newpassconfirm" value="" /></p>
    <p class="clearfix"><input class="button" type="submit" name="submit" value="<?php echo $aChangePassForm[ 'sSubmit' ]; ?>" /></p>
  </form>
  
  <div style="margin-top:15px;">
    <?php echo $aLayoutForm[ 'sTitle' ]; ?>
    <form method="post" action="<?php echo $this->anchor(); ?>">
      <select name="layout">
      <?php foreach ( $aLayoutForm[ 'aOptions' ] as $aOption ) { ?>
      	<option value="<?php echo $aOption[ 'value' ]; ?>"<?php echo ( $aOption[ 'value' ] == $aLayoutForm[ 'value' ] ? ' selected="selected"' : '' ); ?>>
      		<?php echo $aOption[ 'text' ]; ?>
      	</option>
      <?php } ?>
      </select>
      <input class="button" type="submit" name="submit" value="<?php echo $aLayoutForm[ 'sSubmit' ]; ?>" />
    </form>
  </div>

</div>
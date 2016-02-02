<script type="text/javascript">
function showAddForm() {
	document.getElementById( 'AddButton' ).style.display='none';
	document.getElementById( 'AddForm' ).style.display='';
}

var FieldEditManager = new function() {
	
	this.sEdit = 'Edit';
	this.sSave = 'Save';
	this.sCancel = 'Cancel';
	
	this.SaveAction = null;
	
	this.SetString = function( sName, sValue ) {
		eval( 'this.s' + sName + ' = "' + sValue + '"' );
	}
	
	this.SetSaveAction = function( oFunction ) {
		this.SaveAction = oFunction;
	}
	
	this.ShowEditField = function( idValue, idButton, iFieldId ) {
		oValue = document.getElementById( idValue );
		oButton = document.getElementById( idButton );
		
		var sOldValue = oValue.innerHTML;
		
		oButton.innerHTML = '<input type="button" class="button" name="save" value="'+this.sSave+'" onclick="FieldEditManager.Save( \''+idValue+'\', \''+idButton+'\' )" /> <input type="button" class="button" name="cancel" value="'+this.sCancel+'" onclick="FieldEditManager.CancelEditing( \''+idValue+'\', \''+idButton+'\' )" />';
		
		oValue.innerHTML = '<input type="text" class="text" name="name" id="'+idValue+'_current" value="' + sOldValue + '" />';
		oValue.innerHTML += '<input type="hidden" name="old_val" id="'+idValue+'_old" value="' + sOldValue + '" />';
	};
	
	this.CancelEditing = function( idValue, idButton ) {
		oValue = document.getElementById( idValue );
		oOldValue = document.getElementById( idValue + "_old" );
		oButton = document.getElementById( idButton );
		oValue.innerHTML = oOldValue.value;
		oButton.innerHTML = '<input type="button" class="button" name="edit" value="' + this.sEdit + '" onclick="FieldEditManager.ShowEditField( \''+idValue+'\', \''+idButton+'\' )" />';
	}
	
	this.Save = function( idValue, idButton ) {
		oValue = document.getElementById( idValue + '_current' ).value;
		this.SaveAction( idValue, oValue, idButton );
	}
	
}


FieldEditManager.SetString( 'Edit', 'Edytuj' );
FieldEditManager.SetString( 'Save', 'Zapisz' );
FieldEditManager.SetString( 'Cancel', 'Anuluj' );

FieldEditManager.SetSaveAction( xajax_typesSaveAjax );

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
	
	<?php $iCount = 0; ?>
	<?php foreach ( $aList as $aRow ) { ?>
		<tr class="itemlist<?php echo ( $iCount % 2 ); ?>">
			<td id="<?php echo $aRow['id']['id']; ?>" width="30"><?php echo $aRow['id']['value']; ?></td>
			<td id="<?php echo $aRow['name']['id']; ?>" width=""><?php echo $aRow['name']['value']; ?></td>
			<td id="<?php echo $aRow['action']['id']; ?>" width="120" align="right">
				<input type="button" class="button" name="name" value="<?php echo $aRow['action']['value']; ?>" onclick="FieldEditManager.ShowEditField( '<?php echo $aRow['name']['id']; ?>', '<?php echo $aRow['action']['id']; ?>' )" />
			</td>
		</tr>
		<?php $iCount++; ?>
	<?php } //foreach ?>
	</tbody></table>
<?php } // if ?>




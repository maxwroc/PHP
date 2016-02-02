<div class="innerContent">

<?php if ( isset( $calendar ) ) { ?>

<?php echo $calendar ?>
<br />
<br />
<?php } // if ?>

<?php if ( ! empty( $aAddMeals[ 'aCurrentWeek' ] ) ) { ?>
	
	<?php if ( isset( $bShowForm ) ) { ?> 
		<form method="post" action="<?php echo $this->anchor( $aAddMeals[ 'action' ] ); ?>" id="AddMealForm">
	<?php } else { ?>
		<script type="text/javascript">
		function showAddForm() {
			document.getElementById( 'AddMealButton' ).style.display='none';
			document.getElementById( 'AddMealForm' ).style.display='';
		}
		</script>
		<input class="button" type="button" name="show" value="<?php echo $aAddMeals[ 'sAddMeal' ] ?>" onclick="javascript:showAddForm();" id="AddMealButton" />
		<form method="post" action="<?php echo $this->anchor( $aAddMeals[ 'action' ] ); ?>" id="AddMealForm" style="display: none;">
	<?php } ?>
	
	
	
	
		<p class="clearfix"><label><?php echo $aAddMeals[ 'sDate' ]; ?></label>
      <select name="date">
  		  <?php foreach( $aAddMeals[ 'aCurrentWeek' ] as $sDay ) { ?>
  			<option value="<?php echo $sDay; ?>"><?php echo $sDay; ?></option>
  		  <?php } ?>
  		</select>
		</p>
		
		<p class="clearfix"><label for="name"><?php echo $aAddMeals[ 'sName' ]; ?></label><input class="text" type="text" id="name" name="name" value="<?php echo ( isset( $sName ) ? $sName : '' ); ?>" class="StandardWidth" /></p>

	  <?php foreach( $aAddMeals[ 'aCourses' ] as $sType => $aCourses ) { ?>
	  
    <p class="clearfix"><label><?php echo $sType; ?></label>
  		<select name="courses[]" class="StandardWidth" onchange="javascript:countPrice();">
  			<option value="0"><?php echo $aAddMeals[ 'sNull' ]; ?></option>
  		<?php foreach( $aCourses as $aCourse ) { ?>
  			<?php $aCourseIds[ $aCourse[ 'course_id' ] ] = $aCourse[ 'price' ]; ?>
  			<option value="<?php echo $aCourse[ 'course_id' ]; ?>"<?php echo ( ( isset( $aChoosedCourses ) AND in_array( $aCourse[ 'course_id' ], $aChoosedCourses ) ) ? ' selected="selected"' : '' ); ?>><?php echo $aCourse[ 'name' ]; ?> [<?php echo $aCourse[ 'price' ]; ?>]</option>
  		<?php } ?>
  		</select>
  	</p>
	<?php } // foreach ?>
	
	<script type="text/javascript">
		function countPrice() {
			
			var aPrices = new Array(<?php echo count( $aCourseIds ); ?>);
	<?php foreach ( $aCourseIds as $iId => $fTmpPrice ) { ?>
			aPrices[<?php echo $iId; ?>]=<?php echo $fTmpPrice; ?>;
	<?php } ?>
			
			var fValue = 0;
			
			var aItems = document.getElementsByName( 'courses[]' );
			for ( var i=0; i < aItems.length; i++ ) {
				fTmp = 0;
				fTmp = fValue + ( isNaN(aItems[i].value + 1) ? 0 : aPrices[aItems[i].value] );
				if ( ! isNaN( fTmp ) ) {
					fValue += aPrices[aItems[i].value];
				}
			}
			
			document.getElementById( 'price' ).value = fValue;
		}
	</script>
	
    <p class="clearfix"><label for="price"><?php echo $aAddMeals[ 'sPrice' ]; ?></label><input class="text" type="text" name="price" id="price" value="<?php echo ( isset( $fPrice ) ? $fPrice : '' ); ?>" class="StandardWidth" /></p>
			<input class="button" type="submit" name="submit" value="<?php echo $aAddMeals[ 'submit' ] ?>" />
		<?php if ( isset( $aAddMeals[ 'sDeleteText' ] ) ) { ?>
			<input class="button" type="button" name="delete" value="<?php echo $aAddMeals[ 'sDeleteText' ] ?>" onclick="javascript:location.href='<?php echo $this->anchor( $aAddMeals[ 'sDeleteLink' ] ); ?>';" />
		<?php } ?>
	</form>
	
	
	
	<?php if ( isset( $error ) ) { ?>
		<p><?php echo $error; ?></p>
	<?php } ?>

<?php } elseif ( isset( $info ) ) { ?>
	<p><?php echo $info; ?></p>
<?php } else { ?>
	<div style="text-align: center;"><input type="button" class="button" name="send_mail" value="<?php echo $aAddMeals[ 'sSendMail' ]; ?>" onclick="location.href='<?php echo $this->anchor( $aAddMeals[ 'sSendMailLink' ] ); ?>'" /></div>
<?php } ?>

<?php if ( isset( $sQuestion ) ) { ?>
<p style="text-align: center; border: solid 2px red; padding: 10px;">
	<?php echo $sQuestion; ?><br /><br />
	<input class="button" type="button" value="<?php echo $sTextYes; ?>" onclick="location.href='<?php echo $this->anchor( $sLinkYes ); ?>'" />&nbsp;&nbsp;
	<input class="button" type="button" value="<?php echo $sTextNo; ?>" onclick="location.href='<?php echo $this->anchor( $sLinkNo ); ?>'" />
</p>
<?php } ?>

</div>

<br />

<?php if ( isset( $list ) ) { ?>
	<?php echo $list ?>
<?php } // if ?>

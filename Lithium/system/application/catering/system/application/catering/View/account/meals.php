<?php if ( isset( $calendar ) ) { ?>
<div align="center">
<?php echo $calendar ?>
</div>
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
		<input type="button" name="show" value="<?php echo $aAddMeals[ 'sAddMeal' ] ?>" onclick="javascript:showAddForm();" id="AddMealButton" />
		<form method="post" action="<?php echo $this->anchor( $aAddMeals[ 'action' ] ); ?>" id="AddMealForm" style="display: none;">
	<?php } ?>
	<table><tbody>
	<tr>
		<td><?php echo $aAddMeals[ 'sDate' ]; ?></td>
		<td>
		<select name="date">
		<?php foreach( $aAddMeals[ 'aCurrentWeek' ] as $sDay ) { ?>
			<option value="<?php echo $sDay; ?>"><?php echo $sDay; ?></option>
		<?php } ?>
		</select>
		</td>
	</tr>
	<tr>
		<td><?php echo $aAddMeals[ 'sName' ]; ?></td>
		<td><input type="text" name="name" value="<?php echo ( isset( $sName ) ? $sName : '' ); ?>" class="StandardWidth" /></td>
	</tr>
	<?php foreach( $aAddMeals[ 'aCourses' ] as $sType => $aCourses ) { ?>
	<tr>
		<td><?php echo $sType; ?></td>
		<td>
		<select name="courses[]" class="StandardWidth">
			<option value="0"><?php echo $aAddMeals[ 'sNull' ]; ?></option>
		<?php foreach( $aCourses as $aCourse ) { ?>
			<option value="<?php echo $aCourse[ 'course_id' ]; ?>"<?php echo ( ( isset( $aChoosedCourses ) AND in_array( $aCourse[ 'course_id' ], $aChoosedCourses ) ) ? ' selected="selected"' : '' ); ?>><?php echo $aCourse[ 'name' ]; ?> [<?php echo $aCourse[ 'price' ]; ?>]</option>
		<?php } ?>
		</select>
		</td>
	</tr>
	<?php } // foreach ?>
	<tr>
		<td><?php echo $aAddMeals[ 'sPrice' ]; ?></td>
		<td><input type="text" name="price" value="<?php echo ( isset( $fPrice ) ? $fPrice : '' ); ?>" class="StandardWidth" /></td>
	</tr>
	<tr>
		<td> </td>
		<td>
			<input type="submit" name="submit" value="<?php echo $aAddMeals[ 'submit' ] ?>" />
		<?php if ( isset( $aAddMeals[ 'sDeleteText' ] ) ) { ?>
			<input type="button" name="delete" value="<?php echo $aAddMeals[ 'sDeleteText' ] ?>" onclick="javascript:location.href='<?php echo $this->anchor( $aAddMeals[ 'sDeleteLink' ] ); ?>';" />
		<?php } ?>
		</td>
	</tr>
	</tbody></table>
	</form>
	
	<?php if ( isset( $error ) ) { ?>
		<p><?php echo $error; ?></p>
	<?php } ?>

<?php } elseif ( isset( $info ) ) { ?>
	<p><?php echo $info; ?></p>
<?php } ?>

<?php if ( isset( $sQuestion ) ) { ?>
<p style="text-align: center; border: solid 2px red; padding: 10px;">
	<?php echo $sQuestion; ?><br /><br />
	<input type="button" value="<?php echo $sTextYes; ?>" onclick="location.href='<?php echo $this->anchor( $sLinkYes ); ?>'" />&nbsp;&nbsp;
	<input type="button" value="<?php echo $sTextNo; ?>" onclick="location.href='<?php echo $this->anchor( $sLinkNo ); ?>'" />
</p>
<?php } ?>


<?php if ( isset( $list ) ) { ?>
	<br />
	<br />
	<?php echo $list ?>
<?php } // if ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="<?php $this->file( 'style.css', 'css' ); ?>" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php 
foreach ( $aMeta as $sMeta ) {
	echo $sMeta;
} 
?>
<title><?php echo $title ?></title>
</head><body>

<div id="header">
<table style="border: 2px solid rgb(192, 192, 192); width: 800px; margin-top: 10px;" align="center" cellpadding="0" cellspacing="0">
	<tbody><tr>
		<td colspan="2" style="border-bottom: 2px solid rgb(192, 192, 192); height: 20px; text-align: center;">
			&nbsp;&nbsp;
		</td>
	</tr>
	<tr style="background-color: rgb(189, 215, 239);">
		<td style="border-bottom: 2px solid rgb(192, 192, 192); height: 75px;" class="txt10">
			<img src="<?php $this->file( 'ciao_logo.png', 'image' ); ?>">
		</td>
		<td style="border-bottom: 2px solid rgb(192, 192, 192); text-align: right;" class="txt10">
			<?php echo ( isset( $header_username ) ? $header_username : '' ); ?>&nbsp;&nbsp;
		</td>
	</tr>	
	<tr style="background-color: rgb(255, 255, 255);">
		<td colspan="2" style="border-bottom: 0px solid rgb(192, 192, 192); height: 20px;" class="txt10">
			&nbsp;&nbsp;
		</td>
	</tr>
</tbody></table>
</div>
<p> </p>
<table align="center">
	<tbody><tr valign="top">
		<td>
			
		<?php foreach( $menu as $sTitle => $aItems ) { ?>
			
			<table style="border: 2px solid rgb(192, 192, 192); width: 150px; margin-right: 7px;" cellpadding="0" cellspacing="0">
				<tbody>
				<tr style="background-color: rgb(189, 215, 239);">
					<td style="border-bottom: 2px solid rgb(192, 192, 192); height: 20px;" class="txt12t" align="right">
						<b><?php echo $sTitle; ?></b>
					</td>
				</tr>
				<tr>
					<td style="border-bottom: 0px solid rgb(192, 192, 192); height: 20px; padding: 10px;" class="txt10" align="right">
					<?php foreach( $aItems as $aPosition ) { ?>
							<a href="<?php echo $this->anchor( $aPosition[ 'sTarget' ] ); ?>"><?php echo $aPosition[ 'sText' ]; ?></a><br />
					<?php } ?>
					</td>
				</tr>	
			</tbody></table>
			<br />
		<?php } ?>
		</td>
		
		<td>
			
<div id="enrolContainer">

<table style="border: 2px solid rgb(192, 192, 192); width: 640px;" cellpadding="0" cellspacing="0">
	<tbody><tr style="background-color: rgb(189, 215, 239);">
		<td colspan="4" style="border-bottom: 2px solid rgb(192, 192, 192); height: 20px;" class="txt12t" align="right">
			<b><?php echo $sSectionTitle ?></b>
		</td>
	</tr>
	<tr><td class="content"><?php echo $content; ?></td></tr>
</tbody></table>

</div>		</td>
	</tr>
</tbody></table>
</body></html>
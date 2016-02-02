<?php

//header("Pragma: public");
//header("Expires: 0");
//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//header("Content-Type: application/force-download");
header('Content-Type: application/xls');;
header('Content-Disposition: attachment; filename="zonk.xls"');

?>
<?php if ( isset( $aSummary ) ) { ?>
<html>
<head>
<title>Excel Spreadsheet</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    
<style type="text/css">
	table {
		font-size: 8pt; 
		font-family: Arial, sans-serif;
	}
	td.header {
		mso-rotate:90;
		font-weight: bold;
	}
	tr.itemlist0 td {
		background: #D2D2D2;
	}
	tr.itemlist1 td {
		background: #FFFFFF;
	}
	td {
		text-align: right;
	}
	td.left {
		text-align: left;
	}
	td.finalsum {
		background: #FF0000;
	}
</style>
</head>
<body>
<table class="itemlist" border="1"><tbody>
<?php $i=0; foreach( $aSummary as $iKeyX => $aRow ) { ?>
	<?php if ( ! ($i++ % 2) ) { ?>
	<tr class="itemlist0">
	<?php } else { ?>
	<tr class="itemlist1">
	<?php } ?>
	
	<?php foreach( $aRow as $iKeyY => $sColumn ) { ?>
		
		
		<?php if ( $iKeyX == 0 ) { ?>
			<td class="header"><?php echo $sColumn; ?></td>
		<?php } elseif ( $iKeyY == 0 ) { ?>
			<td class="left"><?php echo $sColumn; ?></td>
		<?php } else { ?>
			<td><?php echo $sColumn; ?></td>
		<?php } ?>
	<?php } ?>
	
	</tr>
<?php } ?>
</tbody></table>
</body>
</html>
<?php } // if $aSummary ?>
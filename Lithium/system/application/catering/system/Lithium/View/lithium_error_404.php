<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $title; ?></title>
<style type="text/css">
<!--
	body {
		font-family: Verdana;
		font-size: 10pt;
	}
	
	h1 {
		font-size: 14pt;
		text-weight: bold;
	}
	
	h3 {
		font-size: 12pt;
	}
-->
</style>
</head>
<body>
<h1>Lithium 404 error:</h1>
<h3><?php echo $message; ?></h3>
<?php if ( isset( $description ) ) { ?>
<p><?php echo $description; ?></p>
<?php } ?>
<?php if ( isset( $file ) AND isset( $line ) ) { ?>
	<p>File: <?php echo $file; ?> (<?php echo $line; ?>)</p>
<?php } ?>
<hr />
<p class="small">Lithium <?php echo $version; ?></p>
</body>
</html>
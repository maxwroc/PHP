<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="<?php $this->file( 'raspberry/controls.css', 'css' ); ?>" type="text/css">
<link rel="stylesheet" href="<?php $this->file( 'raspberry/rasp.css', 'css' ); ?>" type="text/css">
<title>Family site</title>
<?php foreach ( $aResources as $sResource => $sType ) { ?>
<script src="<?php $this->file( $sResource, $sType ); ?>" type="text/javascript"></script>
<?php } ?>
</head>
<body>
<div id="wrapper">
	<div id="container">
		<?php echo $content; ?>
		<?php echo empty( $debug ) ? '' : '<pre>' . $debug . '</pre>' ?>
	</div>
</div>
</body>
</head>
</html>
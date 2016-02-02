<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="<?php $this->file( 'raspberry/controls.css', 'css' ); ?>" type="text/css">
<link rel="stylesheet" href="<?php $this->file( 'raspberry/rasp.css', 'css' ); ?>" type="text/css">
<title>Raspberry Manager</title>
<?php echo $headers; ?>
<script src="<?php $this->file( 'raphael/raphael-min.js', 'lib' ); ?>" type="text/javascript"></script>
<script src="<?php $this->file( 'raspberry/main.js', 'js' ); ?>" type="text/javascript"></script>
<?php foreach ( $aResources as $sResource => $sType ) { ?>
  <?php if ( $sType == 'ext' ) { ?>
  <script src="<?php echo $sResource; ?>" type="text/javascript"></script>
  <?php } else { ?>
  <script src="<?php $this->file( $sResource, $sType ); ?>" type="text/javascript"></script>
  <?php } ?>
<?php } ?>
</head>
<body>
<div id="wrapper">
	<div id="container">
		<div id="raspico"></div>
		<div id="loadingIcon"></div>
		<?php echo $menu; ?>
		<?php echo $content; ?>
		<?php echo empty( $debug ) ? '' : '<pre>' . $debug . '</pre>' ?>
	</div>
</div>
</body>
</head>
</html>
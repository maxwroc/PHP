<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title><?php echo $sPageTitle; ?></title>
		<meta name="keywords" content="" />
		<meta name="description" content="" />
		<link href="<?php $this->file( 'mint/main.css', 'css' ); ?>" rel="stylesheet" type="text/css" />
		<link href="<?php $this->file( 'mint/menu.css', 'css' ); ?>" rel="stylesheet" type="text/css" />
		<link href="<?php $this->file( 'mint/box.css', 'css' ); ?>" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div class="wrapper">
			<div class="header">
				<div class="title">blog chodorowski.co</div>
			</div>
			<?php echo $oMenu; ?>
			<div class="main_content">
				<?php
					if ( is_array( $mContent ) ) {
						foreach ( $mContent as $oBox ) {
							echo $oBox;
						}
					} else {
						echo $mContent;
					}
				?>
			</div>
			<br class="spacer" />
		</div>
	</body>
</html>
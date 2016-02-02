<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<link rel="stylesheet" href="<?php $this->file( 'ciao_new/all.css', 'css' ); ?>" type="text/css">
	<link rel="stylesheet" href="<?php $this->file( 'ciao_new/dinner.css', 'css' ); ?>" type="text/css">
	<link rel="stylesheet" href="<?php $this->file( 'ciao_new/print.css', 'css' ); ?>" type="text/css" media="print">
	
	<link rel="stylesheet" href="<?php $this->file( 'ciao_new/jquery/ui.all.css', 'css' ); ?>" type="text/css">
	
	<script type="text/javascript" src="<?php $this->file( 'jquery/jquery-1.3.2.js', 'js' ); ?>"></script>
	<script type="text/javascript" src="<?php $this->file( 'jquery/ui/ui.core.js', 'js' ); ?>"></script>
	<script type="text/javascript" src="<?php $this->file( 'jquery/ui/ui.datepicker.js', 'js' ); ?>"></script>
	<script type="text/javascript" src="<?php $this->file( 'jquery/ui/i18l/ui.datepicker-pl.js', 'js' ); ?>"></script>
	
	<script defer type="text/javascript" src="<?php $this->file( 'functions.js', 'js' ); ?>"></script>
	
	<?php // print additional scripts etc.
	if ( ! empty( $sAdditionalHeadData ) ) { 
		echo $sAdditionalHeadData;
	}
	?>
	
<?php //TODO: jezyk dla pozostalych krajow w date pickerze ?>
	
	<!--[if lt IE 7.]>
	  <script defer type="text/javascript" src="<?php $this->file( 'ciao_new/pngfix.js', 'js' ); ?>"></script>
	<![endif]-->
	
	<title><?php echo $title ?></title>
	
<?php 
foreach ( $aMeta as $sMeta ) {
	echo $sMeta;
} 
?>  
  </head>
  
  <body>
	
	
	<div id="mainWrapper" class="clearfix">
		
	  <div id="wrapperLeft" class="clearfix">
		
		<img id="cLogo" src="<?php $this->file( 'ciao_new/logo.png', 'image' ); ?>" title="Logo" />
		<div id="menu" class="clearfix">
		
		<?php foreach( $menu as $sTitle => $aItems ) { ?>
			
			<p class="menuSection">
		   		<?php echo $sTitle; ?>
		  	</p>
		  	
		  	<ul class="menuList">
			<?php foreach( $aItems as $aPosition ) { ?>
				<?php if( ! empty( $aPosition ) ) { ?>
				<li><a href="<?php echo $this->anchor( $aPosition[ 'sTarget' ] ); ?>"><?php echo $aPosition[ 'sText' ]; ?></a></li>
				<?php } ?>
			<?php } ?>
			</ul>
			
		<?php } ?>
		  
		</div>	
		
	  </div>
	  
	  <div id="wrapperRight">
		<div id="topPic">
		  <div>
			<span><?php echo $aWelcomeMessage[ 'sText' ]; ?></span>
			
			<p class="tooltip" style="display:none;" id="tooltip">
			  <?php echo $aWelcomeMessage[ 'sDinnersFullName' ]; ?>
			</p>
			
		  </div>
		</div>
		
		<div id="main" class="clearfix">
		  
		  <!-- modul contentu -->
		  <div class="mainContent">
			<div class="topBar">
			  <?php echo $sSectionTitle ?>
			</div>
			
			<?php echo $content; ?>
			
		  </div>
		  <!-- end modul contentu -->
		  
		</div>
		
	  </div>
	  
	</div>
	
  </body>
</html>
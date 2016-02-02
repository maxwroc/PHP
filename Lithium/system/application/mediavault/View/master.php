<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>media vault</title>
<?php echo $headers; ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="/css/mediavault/images/favicon.ico">
<link rel="stylesheet" href="/css/mediavault/style.css" type="text/css" media="all" />
<script src="<?php $this->file( 'knockout/knockout-3.3.0.js', 'lib' ); ?>" type="text/javascript"></script>
<script src="<?php $this->file( 'mediavault/moviemgr.js', 'js' ); ?>" type="text/javascript"></script>
</head>
<body>
<!-- START PAGE SOURCE -->
<div id="shell">
  <div id="header">
    <h1 id="logo"><a href="<?php echo $this->anchor( '/' ) ?>">media vault</a></h1>
    <div class="social"> <span>FOLLOW US ON:</span>
      <ul>
        <li><a class="twitter" href="#">twitter</a></li>
        <li><a class="facebook" href="#">facebook</a></li>
        <li><a class="vimeo" href="#">vimeo</a></li>
        <li><a class="rss" href="#">rss</a></li>
      </ul>
    </div>
    <div id="navigation">
      <ul>
        <li><a class="active" href="<?php echo $this->anchor( '/' ) ?>">HOME</a></li>
        <li><a href="<?php echo $this->anchor( '/movie/add' ) ?>">ADD NEW</a></li>
        <li><a href="#">IN THEATERS</a></li>
        <li><a href="#">COMING SOON</a></li>
        <li><a href="#">CONTACT</a></li>
        <li><a href="#">ADVERTISE</a></li>
      </ul>
    </div>
    <div id="sub-navigation">
      <?php if ( !empty( $aSubNavigation ) ) { ?>
      <ul>
        <?php foreach ( $aSubNavigation as $aLink ) { ?>
        <li><a href="<?php echo $aLink[ 'url' ] ?>"><?php echo $aLink[ 'text' ] ?></a></li>
        <?php } ?>
      </ul>
      <?php } ?>
      <div id="search">
        <form action="#" method="get" accept-charset="utf-8">
          <label for="search-field">SEARCH</label>
          <input type="text" name="search field" value="Enter search here" id="search-field" class="blink search-field"  />
          <input type="submit" value="GO!" class="search-button" />
        </form>
      </div>
    </div>
  </div>
  <div id="main">
    <?php 
      if ( !empty( $aBoxes ) ) {
        foreach ( $aBoxes as $sBox ) {
          echo $sBox;
        }
      }    
    ?>
    <?php echo $this->content; ?>
    <div class="cl">&nbsp;</div>
  </div>
  <div id="footer">
    <p class="lf">Copyright &copy; 2010 <a href="#">SiteName</a> - All Rights Reserved</p>
    <p class="rf">Design by <a href="http://chocotemplates.com/">ChocoTemplates.com</a></p>
    <div style="clear:both;"></div>
  </div>
</div>
<?php
  if ( !empty( $debugInfo ) ) {
    echo $debugInfo;
  }
?>
<!-- END PAGE SOURCE -->
</body>
</html>
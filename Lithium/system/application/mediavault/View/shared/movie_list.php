<div class="box">
  <div class="head">
    <h2><?php echo $sHeader ?></h2>
    <p class="text-right"><a href="<?php echo $sSeeAllUrl ?>">See all</a></p>
  </div>
  <ul class="movie-list">
  <?php foreach ( $aMovies as $aMovie ) { ?>
    <li class="movie">
      <div class="movie-image">
        <div class="movie-info">
          <div class="name"><?php echo $aMovie[ 'name' ] ?></div>
          <div class="detail"><span><?php echo $aMovie[ 'year' ] ?></span></div>
          <div class="detail"><span><?php echo $aMovie[ 'actors' ] ?></span></div>
        </div> 
        <a href="<?php echo $this->anchor( '/movie/info/' . $aMovie[ 'movie_id' ] ) ?>">
          <img src="<?php echo $this->file( 'mediavault/images/movies/' . $aMovie[ 'image_name' ], 'css' ) ?>" alt="" />
        </a> 
      </div>
      <div class="rating">
        <p>RATING</p>
        <div class="stars">
          <div class="stars-in"> </div>
        </div>
        <span class="comments">12</span> </div>
    </li>
  <?php } ?>
  </ul>
</div>
<?php

class Controller_Mediavault extends Abstract_BaseController {

  public function init() {
    parent::init();
  
    $this->mTemplate->aSubNavigation = array(
      array( 'url' => '#', 'text' => 'Show all' ),
      array( 'url' => '#', 'text' => 'Latest trailers' ),
      array( 'url' => '#', 'text' => 'top rated' ),
      array( 'url' => '#', 'text' => 'Most commented' )
    );
  }

  public function indexAction() {
    
    $aRecentMovies = $this->getModel( 'movie' )->orderby( 'movie_id', 'desc' )->limit( 6 )->getAll();
    
    $aBoxes = [];
    
    
    if ( !empty( $aRecentMovies ) ) {
      $aBoxes[] = View::factory( 'shared/movie_list', array(
        'sHeader' => 'Recently added movies',
        'sSeeAllUrl' => '',
        'aMovies' => $aRecentMovies
      ));
    }
    
    $this->mTemplate->aBoxes = $aBoxes;
  
    $this->mTemplate->content = View::factory( 'index' );
  }
}
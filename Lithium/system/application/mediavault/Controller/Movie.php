<?php

class Controller_Movie extends Abstract_BaseController {

  public function init() {
    parent::init();
  
    if ( !empty( $this->mTemplate ) ) {
      $this->mTemplate->aSubNavigation = array(
        array( 'url' => $this->oRouter->getPageUrl( '/movie/add' ) , 'text' => 'Add from disk' ),
        array( 'url' => $this->oRouter->getPageUrl( '/movie/towatch' ), 'text' => 'Add to watch' )
      );
    }
  }

  public function addAction() {  
    if ( empty( $this->mTemplate ) ) {
      return;
    }
  
    $this->mTemplate->content = View::factory( 'movie/add_disk', array( 'mMovieInfo' => View::factory( 'movie/info' ) ) );
  }
  
  public function infoAction( $iMovieId ) {
  
    $oMovie = $this->getModel( 'movie', $iMovieId );
    
    if ( !empty( $oMovie ) ) {
      $aResult = array();
      $aResult[ 'movie_id' ] = $oMovie->movie_id;
      $aResult[ 'name' ] = $oMovie->name;
      $aResult[ 'local_name' ] = $oMovie->local_name;
      $aResult[ 'year' ] = $oMovie->year;
      $aResult[ 'actors' ] = $oMovie->actors;
      $aResult[ 'img_url' ] = $oMovie->image_name ? $this->oRouter->getFileUrl( 'mediavault/images/movies/' . $oMovie->image_name, 'css' ) : '';
      
      $aFiles = $this->getModel( 'file' )->where( 'movie_id', $oMovie->movie_id )->getAll();
      
      $aScripts = array( 'pageViewModel.movieDetails(' . json_encode( $aResult ) . ');' );
    
      $this->mTemplate->content = View::factory( 'movie/info', array( 'aScripts' => $aScripts ) );
    }
  }
  
  public function towatchAction() {
    $this->mTemplate->content = 'to watch';
  }
  
  public function saveAjax( $aFormValues ) {
    $oResp = new xajaxResponse();
    
    if ( empty( $aFormValues[ 'movie_id' ] ) ) {
      
      // check if movie exists already
      $oMovieResult = $this->getModel( 'movie' )->where( 'name', $aFormValues[ 'name' ], '=' )->where( 'year', $aFormValues[ 'year' ], '=' );
      $oMovieResult->getAll();
      
      error_log( print_r($oMovieResult->movie_id,1) );
      if ( empty( $oMovieResult->movie_id ) ) {
      error_log( print_r("ok1",1) );
        $oMovie = $this->getModel( 'movie' );
        $oMovie->name = $aFormValues[ 'name' ];
        $oMovie->local_name = $aFormValues[ 'local_name' ];
        $oMovie->year = $aFormValues[ 'year' ];
        $oMovie->actors = $aFormValues[ 'actors' ];
         
        // saving image
        if ( $aFormValues[ 'img_url' ] && strpos( $aFormValues[ 'img_url' ], $_SERVER[ 'SERVER_NAME' ] ) !== false ) {
          $sImageRootPath = '/home/www/public/css/mediavault/images/movies/';
          
          error_log( print_r("ok2",1) );
          
          $sFileName = sprintf( '%u.jpg', crc32( $aFormValues[ 'img_url' ] ) );
          
          if ( !file_exists( $sImageRootPath . $sFileName ) ) {
            try {
              file_put_contents( $sImageRootPath . $sFileName, file_get_contents( $aFormValues[ 'img_url' ] ) );
              
              $oMovie->image_name = $sFileName; //$this->oRouter->getFileUrl( 'mediavault/images/movies/' . $sFileName, 'css' );
            }
            catch( Exception $oError ) {
              $oResp->alert( 'error3: ' . $oError->getMessage() );
            }
          }
        }
        
        if ( $oMovie->save() ) {
          $oResp->alert( 'Saved movie' );
          
          error_log( print_r("ok3",1) );
          $iMovieId = $oMovie->getInsertId();
          
          $oFile = $this->getModel( 'file', $aFormValues[ 'file_id' ] );
          $oFile->getAll();
          $oFile->movie_id = $iMovieId;
          if ( $oFile->save() ) {
            $oResp->alert( 'Saved file' );
          }
          else {
            $oResp->alert( 'error1: ' . $oFile->getLastError() );
          }
        }
        else {
          $oResp->alert( 'error2: ' . $oMovie->getLastError() );
        }
      } // if
      else {
        // TODO updating movie details
        
        $oFile = $this->getModel( 'file', $aFormValues[ 'file_id' ] );
        $oFile->getAll();
        $oFile->movie_id = $oMovieResult->movie_id;
        
        if ( $oFile->save() ) {
          $oResp->alert( 'Saved file' );
        }
        else {
          $oResp->alert( 'error1: ' . $oFile->getLastError() );
        }
      }
    } // if
    
    return $oResp;
  }
  
  public function scanAjax() {
    $oResp = new xajaxResponse();
    
    $oResp->script('pageViewModel.update({"newFiles":[{"file_id":32,"path":"paddo-immortal-rpk-a.avi"},{"file_id":33,"path":"This.Must.Be.The.Place.2011.DVDRip.XviD-NYDIC.avi"},{"file_id":34,"path":"About Fifty 2011 DVDRip XviD-FTW.avi"},{"file_id":35,"path":"saimorny-the.hunter.xvid.avi"},{"file_id":36,"path":"dmd-tsit.avi"}],"deletedFiles":["\\/fsdfd","\\/zonk","\\/z","\\/kij","","","\\/kij2","","","\\/kij5","","","","","\\/zbok7","\\/zbok8","\\/zbok9","\\/zbok77","\\/zbok71","\\/zbok72"]});');
    
    return $oResp;
    
    $directory = new RecursiveDirectoryIterator('/mnt/movies/');
    $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY, RecursiveIteratorIterator::CATCH_GET_CHILD);
    $aRegex = new RegexIterator($iterator, '/^.+\.(?:mpe?g|avi|mp4|mkv)$/i', RecursiveRegexIterator::GET_MATCH);
  
    $aCurrentFiles = [];
    foreach( $aRegex as $aResult ) {
      $aCurrentFiles[] = $aResult[0];
    }
    
    $aKnownFiles = $this->getModel( 'file' )->getAll( array( 'path' ) );
    
    $aNewFilesObjs = [];
    $aNewFiles = array_values( array_diff( $aCurrentFiles, $aKnownFiles ) );
    $aMissingFiles = array_values( array_diff( $aKnownFiles, $aCurrentFiles ) );
    
    $iNewFilesCount = count( $aNewFiles );
    
    // to be removed
    $iNewFilesCount = $iNewFilesCount > 5 ? 5 : $iNewFilesCount;
    
    if ( $iNewFilesCount > 0 ) {
      $oFile = $this->getModel( 'file' );
      
      for( $i = 0; $i < $iNewFilesCount; $i++ ) {
        if ( $i > 0 ) {
          $oFile->next();
        }
        
        $oFile->path = $aNewFiles[ $i ];
      }
      
      $oFile->save();
      
      $iLastId = $oFile->getInsertId();
      
      for( $i = 0; $i < $iNewFilesCount; $i++ ) {
        $aNewFilesObjs[] = array(
          'file_id' => $iLastId + $i,
          'path' => $this->getMovieName( $aNewFiles[ $i ] )
        );
      }
    }
    
    $aResult = array(
      'newFiles' => $aNewFilesObjs,
      'deletedFiles' => $aMissingFiles,
    );
    
    $oResp->script( "pageViewModel.update(" . json_encode( $aResult ) . ");" );
    
    return $oResp;
  }
  
  public function loadFilesWithoutMoviesAjax() {
    $oResp = new xajaxResponse();
    
    $aFiles = $this->getModel( 'file' )->where( 'movie_id', null, 'is' )->getAll();
    
    if( !empty( $aFiles ) ) {
      for( $i = 0; $i < count( $aFiles ); $i++ ) {
        $aFiles[ $i ][ 'path' ] = $this->getMovieName( $aFiles[ $i ][ 'path' ] );
      }
    }
    
    $oResp->script( "pageViewModel.update(" . json_encode( array( 'newFiles' => $aFiles ) ) . ");"  );
    $oResp->script( 'pageViewModel.movieDetails(null);' );
    
    return $oResp;
  }
  
  public function getFileDetailsAjax( $iFileId ) {
    $oFile = $this->getModel( 'file', $iFileId );
    
    $oResp = new xajaxResponse();
    
    $aResult = array(
      'file_id' => $iFileId,
      'path' => $oFile->path,
      'name' => $this->getNormalizedMovieName( $this->getMovieName( $oFile->path ) )
    );
    
    $oMovie = $oFile->get( 'movie_id');
    if ( !empty( $oMovie ) ) {
      $aResult[ 'movie_id' ] = $oMovie->movie_id;
      $aResult[ 'name' ] = $oMovie->name;
      $aResult[ 'local_name' ] = $oMovie->local_name;
      $aResult[ 'year' ] = $oMovie->year;
      $aResult[ 'actors' ] = $oMovie->actors;
      $aResult[ 'img_url' ] = $oMovie->image_name ? $this->oRouter->getFileUrl( 'mediavault/images/movies/' . $oMovie->image_name, 'css' ) : '';
    }
    
    $oResp->script( 'pageViewModel.movieDetails(' . json_encode( $aResult ) . ');' );
    
    return $oResp;
  }
  
  public function getMovieInfoAjax( $iFileId, $sName ) {
    $oResp = new xajaxResponse();
    
    $sUrl = "http://www.filmweb.pl/search/live?q=";
    
    $sName = $this->getNormalizedMovieName( $sName );
    $aNameParts = explode( '+', $sName );
    $sLastResult = null;
    
    $aParialNamesTried = array();
    
    for( $i = 0; $i < count( $aNameParts ); $i++ ) {
      $sPartialName = implode( '+', array_slice( $aNameParts, 0, $i + 1 ) );
      
      $aParialNamesTried[] = $sPartialName;
      //error_log($sUrl . $sPartialName);
      $sData = file_get_contents($sUrl . $sPartialName);
      
      if ( strlen( $sData ) == 0 ) {
        if ( $sLastResult == "ERROR:tooShort" ) {
          $sLastResult = null;
        }
        else {
          break;
        }
      }
      
      $sLastResult = $sData;
    }
    
    $oFile = $this->getModel( 'file', $iFileId );
    
    $aMovieInfo = array(
      'file_id' => $iFileId,
      'movie_id' => 0,
      'path' => $oFile->path
    );
    
    if ( ! empty( $sLastResult ) ) {
      $aParsed = $this->parseFilmWebResult( $sLastResult );
      if ( empty( $aParsed ) ) {
        $aMovieInfo[ 'message' ] = "Couldn't parse results from FilmWeb\n" . implode( "\n", $aParialNamesTried ) . "\n\n" . $sLastResult;
        $aMovieInfo[ 'messageType' ] = 'message error';
      }
      else {
        $aMovieInfo = array_merge( $aMovieInfo, $aParsed );
      }
    }
    else {
      $aMovieInfo[ 'message' ] = "Couldn't find any results on FilmWeb\n" . implode( "\n", $aParialNamesTried ) . "\n\n" . $sLastResult;
      $aMovieInfo[ 'messageType' ] = 'message error';
    }

    error_log(json_encode( $aMovieInfo ));
    $oResp->script( 'pageViewModel.movieDetails(' . json_encode( $aMovieInfo ) . ');' );    
    
    return $oResp;
  }
  
  private function getNormalizedMovieName( $sFileName ) {
    return str_replace( array( ' ', '.' ), '+', $sFileName );
  }
  
  private function getMovieName( $sPath ) {
    $aDirs = explode( '/', $sPath );
    $iSectionsCount = count( $aDirs );
    return $aDirs[ ( $iSectionsCount >= 2 ? $iSectionsCount - 2 : $iSectionsCount - 1 ) ];
  }
  
  private function parseFilmWebResult( $sResult ) {
    //error_log('result: ' . $sResult);
    $aSuggestions = explode( '\a', $sResult );
    
    if ( empty( $aSuggestions[0] ) ) {
      return null;
    }
    
    // take the first suggestion and split fields
    // 0 - type (f/g/s)
    // 1 - id
    // 2 - image
    // 3 - title
    // 4 - local title
    // 5 - ??
    // 6 - year
    // 7 - actors
    //error_log('suggestions: ' . print_r($aSuggestions,1));
    $aMovie = explode( '\c', $aSuggestions[0] );
    
    $aMapping = array( 
      2 => 'img_url',
      3 => 'name', 
      4 => 'local_name',
      6 => 'year', 
      7 => 'actors' 
    );
    
    $aMovieInfo = array();
    
    //error_log(print_r($aMovie,1));
    foreach( $aMapping as $iKey => $sValue ) {
      $aMovieInfo[ $aMapping[ $iKey ] ] = $aMovie[ (int)$iKey ]; 
    }
    
    // fix image url
    if ( !empty( $aMovieInfo[ 'img_url' ] ) ) {
      $aMovieInfo[ 'img_url' ] = 'http://1.fwcdn.pl/po' . str_replace( '4.jpg', '6.jpg', $aMovieInfo[ 'img_url' ]);
    }
    
    return $aMovieInfo;
  }
}
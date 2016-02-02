<?php

abstract class Abstract_BaseController extends Core_Controller {

  public $mTemplate = 'master';
  
  protected $oXajax;
  
  public function init() {
  
    // load xajax lib
    Loader::loadClass( 'Library_Xajax', 'LXajax' );
    
    //$this->oXajax = new LXajax();
    $this->oXajax = new LXajax( null, null, array( 'debug' => true ) );
    $this->oXajax->registerXajaxFunctions( $this );
    
    // if xajax call end executing rest of code
    if ( $this->oXajax->isAjaxCall() ) {
      parent::init();
      // prevent from sending master view
      $this->mTemplate = null;
      
      return;
    }
  
    parent::init();
    
    $this->mTemplate->headers = $this->oXajax->getJavascript();
  }
  
  public function preDispatch() {
    parent::preDispatch();
    
    $aQueries = $this->oLithium->getDatabase()->getQueryHistory();
    
    if ( !IN_PRODUCTION && !empty( $aQueries ) ) {
    
      foreach ( $aQueries as $sQuery ) {
          error_log( $sQuery );
      
      }
      
      if ( !empty( $this->mTemplate ) ) {
        $this->mTemplate->debugInfo = '<pre>' . implode( "\n", $aQueries ) . '</pre>';
      }
    }
  }
}
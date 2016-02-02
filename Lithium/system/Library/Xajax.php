<?php

require_once SYSPATH . 'Library/xajax/xajax_core/xajax.inc.php';

/**
 * Wrapper for Xajax base class
 */
class LXajax {
	
	/**
	 * @var xajax
	 */
	protected static $oXajax = null;
	
	/**
	 * @var bool
	 */
	protected $bIsFunctionRegistered = false;
	
	public function __construct( $sRequestURI = null, $sLanguage = null, $aConfig = array() ) {
		
		if ( is_null( self::$oXajax ) ) {
			self::$oXajax = new xajax( $sRequestURI, $sLanguage );
			self::$oXajax->configure( 'javascript URI', '/lib/xajax/' );
			self::$oXajax->configure( 'statusMessages', true );
      self::$oXajax->configure( 'exitAllowed', false );
			//self::$oXajax->configure( 'debug', true );
      
      foreach( $aConfig as $sName => $mValue ) {
        self::$oXajax->configure( $sName, $mValue );
      }
		}
		
	}
	
	/**
	 * Wrapper for register function from xajax library
	 */
	public function register() {
		
		$this->bIsFunctionRegistered = true;
		$aParams = func_get_args();
		
		// add xajax param in the beginning of param list
		array_unshift( $aParams, XAJAX_FUNCTION );
		
		return call_user_func_array( array( self::$oXajax, 'register' ), $aParams );
		
	}
	
	/**
	 * Calls functions from xajax library
	 * 
	 * @return mixed
	 */
	public function __call( $sName, $aParams ) {
		
		return call_user_func_array( array( self::$oXajax, $sName ), $aParams );
		
	}
	
	/**
	 * Return true if some xajax call has been registered
	 * 
	 * @return bool
	 */
	public function isFunctionRegistered() {
		return $this->bIsFunctionRegistered;
	}
	
	
	
	/**
	 * Register all xajax functions from current controller
	 * 
	 * @param LXajax - instance of LXajax object
	 * @return void
	 */
	public function registerXajaxFunctions( $oController ) {
		
		// register all available xajax functions
		foreach ( get_class_methods( $oController ) as $sFunction ) {
			if ( preg_match( '!Ajax$!', $sFunction ) ) {
				$this->register( array( $sFunction, $oController, $sFunction ) );
			}
		}
		
	}
  
  public function isAjaxCall() {
		
		static $bIsAjaxCall;
		
		// rest of code can be executed only one time
		if ( ! is_null( $bIsAjaxCall ) ) return $bIsAjaxCall;
		
		if ( ! self::$oXajax->canProcessRequest() ) return $bIsAjaxCall = false;
		
		self::$oXajax->processRequest();
		
		return $bIsAjaxCall = true;
		
	}
}
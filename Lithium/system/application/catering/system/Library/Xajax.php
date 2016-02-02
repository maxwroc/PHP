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
	
	public function __construct( $sRequestURI = null, $sLanguage = null ) {
		
		if ( is_null( self::$oXajax ) ) {
			self::$oXajax = new xajax( $sRequestURI, $sLanguage );
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
	
}
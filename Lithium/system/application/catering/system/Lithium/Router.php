<?php

class Router {
	
	/**
	 * Router configuration values
	 * 
	 * @var array
	 */
	protected $aConfig;
	
	/**
	 * Routing rules
	 * 
	 * @var array
	 */
	protected $aRouteRules = array();
	
	/**
	 * Application name
	 * 
	 * @var string
	 */
	protected $sApplicationName;
	
	/**
	 * Determinate is current app default one
	 * 
	 * @var bool
	 */
	protected $bIsDefaultApp = true;
	
	/**
	 * Name of controller that should be used for current request
	 * 
	 * @var string
	 */
	protected $sControllerName;
	
	/**
	 * Name of a function that should be invoked for current request
	 * 
	 * @var string
	 */
	protected $sFunctionName;
	
	/**
	 * PATH_INFO value
	 * 
	 * @var string
	 */
	protected $sCurrentPath;
	
	/**
	 * Additional params given in url
	 * 
	 * @var array
	 */
	protected $aParams = array();
	
	/**
	 * Base application url
	 * 
	 * @var string
	 */
	protected $sAppUrl;
	
	/**
	 * Class constructor
	 * 
	 * @param array $aConfig - configuration
	 */
	public function __construct( $aConfig ) {
		
		$this->aConfig = $aConfig;
		
		if ( isset( $_SERVER[ 'ORIG_PATH_INFO' ] ) && empty( $_SERVER[ 'PATH_INFO' ] ) ) { 
			$this->sCurrentPath = $_SERVER[ 'ORIG_PATH_INFO' ];
		} else { 
			$this->sCurrentPath = $_SERVER[ 'PATH_INFO' ];
		}
		
		if ( ( ! empty( $aConfig['rules'] ) ) AND ( is_array( $aConfig['rules'] ) ) ) {
			$this->aRouteRules = $aConfig['rules'];
			$this->executeRules();
		}
		
	}
	
	/**
	 * Initialize class
	 */
	public function init() {
		
		$this->parseURL();
		
		$this->sAppUrl = substr( 'http://' . $_SERVER[ 'HTTP_HOST' ] . URLROOT, 0, -1 );
		
	}
	
	/**
	 * Execute URL rules
	 */
	protected function executeRules() {
		
		foreach ( $this->aRouteRules as $aRule ) {
			$this->sCurrentPath = preg_replace( $aRule[0], $aRule[1], $this->sCurrentPath );
		}
		
	}
	
	public function addConfig( Array $aConfig ) {
		$this->aConfig = array_merge( $this->aConfig, $aConfig );
	}
	
	public function getApplicationName() {
		
		if ( empty( $this->sApplicationName ) ) {
			$this->determinateApplicationName();
		}
		
		return $this->sApplicationName;
	}
	
	/**
	 * Returns name of controller that should be used to handle current request
	 * 
	 * @return string
	 */
	public function getControllerName() {
		return $this->sControllerName;
	}
	
	/**
	 * Returns name of function that should be used to handle current request
	 * 
	 * @return string
	 */
	public function getFunctionName() {
		return $this->sFunctionName;
	}
	
	/**
	 * Returns parameters for current request
	 * 
	 * @return array
	 */
	public function getParams() {
		return $this->aParams;
	}
	
	/**
	 * Removes given param from param list
	 * 
	 * @param string $sName - name of param
	 * @param bool $bHasValue - has param value
	 */
	public function removeParam( $sName, $bHasValue = false ) {
		
		for ( $i = 0, $c = sizeof( $this->aParams ); $i < $c; $i++ ) {
			if ( $this->aParams[ $i ] == $sName ) {
				if ( $bHasValue ) {
					unset( $this->aParams[ $i++ + 1 ] );
				}
				unset( $this->aParams[ $i++ - 1 ] );
			} // if
		} // for
		
		// reset keys
		$this->aParams = array_values( $this->aParams );
		
	}
	
	/**
	 * Returns current PATH_INFO value
	 * 
	 * @return string
	 */
	public function getCurrentPath() {
		return $this->sCurrentPath;
	}
	
	/**
	 * Returns url for given page
	 * 
	 * @return string
	 */
	public function getPageUrl( $sPatch = '' ) {
		
		static $sAppUrl = null;
		
		if ( is_null( $sAppUrl ) ) {
			$sAppUrl = substr( 'http://' . $_SERVER[ 'HTTP_HOST' ] . URLROOT, 0, -1 );
		}
		
		if ( empty( $sPatch ) ) {
			$sPatch = $this->getCurrentPath();
		}
		
		return $sAppUrl . $sPatch;
		
	}
	
	protected function determinateApplicationName() {
		
		if ( empty( $this->sApplicationName ) ) {
			
			$this->sApplicationName = $this->aConfig['default_app'];
			
			if ( ! empty( $this->aConfig['applications'] ) ) {
				
				$aUrlParts = explode( '/', $this->sCurrentPath );
				
				if ( ! empty( $aUrlParts[1] ) ) {
					if ( ( $aUrlParts[1] != $this->aConfig['default_app'] ) && in_array( $aUrlParts[1], $this->aConfig['applications'] ) ) {
						$this->sApplicationName = $aUrlParts[1];
						$this->bIsDefaultApp = false;
					}
				}
				
			}
			
		}
		
	}
	
	/**
	 * Parse current request url
	 */
	protected function parseURL() {
		
		$aUrlParts = explode( '/', $this->sCurrentPath );
		
		$iUrlPos = 1;
		
		if ( ! $this->bIsDefaultApp ) {
			// in first pos we have app name so we switch to controller name
			$iUrlPos++;
		}
		
		$this->sControllerName =  'Controller_' . ucfirst( empty( $aUrlParts[ $iUrlPos ] ) ? $this->aConfig[ 'default_controller' ] :  $aUrlParts[ $iUrlPos ] );
		
		// function name
		$iUrlPos++;
		
		$this->sFunctionName =  strtolower( empty( $aUrlParts[ $iUrlPos ] ) ? $this->aConfig[ 'default_function' ] :  $aUrlParts[ $iUrlPos ] ) . 'Action';
		
		//params pos
		$iUrlPos++;
		
		if ( ! empty( $aUrlParts[ $iUrlPos ] ) ) {
			for ( $i = $iUrlPos; $i < count( $aUrlParts ); $i++ ) {
				$this->aParams[] = $aUrlParts[ $i ];
			}
		}
		
	}
	
	/**
	 * Returns url for given file name and type
	 * 
	 * @param string $sName - file name/path
	 * @param string $sType - file type
	 * @return string
	 */
	public function getFileUrl( $sName, $sType ) {
		return $this->sAppUrl . '/' . $sType . '/' . $sName;
	}
	
}

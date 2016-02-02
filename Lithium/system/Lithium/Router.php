<?php

class Router {
	
	/**
	 * Lithium configuration
	 * 
	 * @var Config
	 */
	protected $oConfig;
	
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
	 * Determinate is current url contains app name
	 * 
	 * @var bool
	 */
	protected $bUrlContainsAppName = false;
	
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
   * Server root URL
   *
   * @var string
   */
  protected $sRootUrl;
	
	/**
	 * Base application url
	 * 
	 * @var string
	 */
	protected $sAppUrl;
	
	/**
	 * Class constructor
	 * 
	 * @param Config $oConfig
	 */
	public function __construct( $oConfig ) {
		$this->oConfig = $oConfig;
	}
	
	/**
	 * Initialize class
	 */
	public function init() {
		
		// Check if we have url mapping rules and apply them
		if ( $this->oConfig->hasValue( 'router.rules' ) ) {
			$this->aRouteRules = $this->oConfig->getValue( 'rules' );
			if ( ! is_array( $this->aRouteRules ) ) {
				$this->aRouteRules = array();
			}
		}
		
		$sAppRules = sprintf( 'applications.%s.router.rules', $this->getApplicationName() );
		if ( $this->oConfig->hasValue( $sAppRules ) ) {
			$aRules = $this->oConfig->getValue( $sAppRules );
			if ( is_array( $aRules ) ) {
				$this->aRouteRules = array_merge( $this->aRouteRules, $aRules );
			}
		}
		
		if ( !empty( $this->aRouteRules ) ) $this->executeRules();
		
		$this->parseURL();
		
    $this->sRootUrl = substr( 'http://' . $_SERVER[ 'HTTP_HOST' ] . URLROOT, 0, -1 );
    
    if ( empty( $this->sAppUrl ) ) {
      $this->sAppUrl = $this->sRootUrl;
    }
		
	}
	
	/**
	 * Execute URL rules
	 */
	protected function executeRules() {
		foreach ( $this->aRouteRules as $aRule ) {
			$this->sCurrentPath = preg_replace( $aRule[0], $aRule[1], $this->getCurrentPath() );
		}
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
		
		if ( empty( $this->sCurrentPath ) ) {
			if ( isset( $_SERVER[ 'ORIG_PATH_INFO' ] ) && empty( $_SERVER[ 'PATH_INFO' ] ) ) { 
				$this->sCurrentPath = $_SERVER[ 'ORIG_PATH_INFO' ];
			} else { 
				$this->sCurrentPath = $_SERVER[ 'PATH_INFO' ];
			}
		}
	
		return $this->sCurrentPath;
	}
	
	/**
	 * Returns url for given page
	 * 
	 * @return string
	 */
	public function getPageUrl( $sPatch = '' ) {
		
		if ( empty( $sPatch ) ) {
			$sPatch = $this->getCurrentPath();
		}
		
		return $this->sAppUrl . $sPatch;
		
	}
	
	/**
	 * Sets up correct application name
	 */
	protected function determinateApplicationName() {
		
		if ( empty( $this->sApplicationName ) ) {
			
			$this->sApplicationName = $this->oConfig->getValue( 'default_application' );
			
			if ( $this->oConfig->hasValue( 'applications' ) ) {
				
				$aApplications = $this->oConfig->getValue( 'applications' );
				
				// Check domains
				foreach ( $aApplications as $sName => $aApp ) {
					if ( !empty( $aApp['domain'] ) && ( $_SERVER['SERVER_NAME'] == $aApp['domain'] ) ) {
						$this->sApplicationName = $sName;
						return; // Domain is most important - if found we do not check rest od the url
					}
				}
				
				$aUrlParts = explode( '/', $this->getCurrentPath() );
				
				if ( ! empty( $aUrlParts[1] ) ) {
					
					foreach ( $aApplications as $sName => $aApp ) {
						if ( ! empty( $aApp['url_path'] ) && ( $aUrlParts[1] == $aApp['url_path'] ) ) {
							$this->sApplicationName = $sName;
              $this->sAppUrl = substr( 'http://' . $_SERVER[ 'HTTP_HOST' ] . URLROOT, 0, -1 ) . '/' . $aApp['url_path'];
							$this->bUrlContainsAppName = true;
							break;
						}
					} // foreach
				} // if
				
			} // if
			
		} // if
		
	}
	
	/**
	 * Parse current request url
	 */
	protected function parseURL() {
		
		$aUrlParts = explode( '/', $this->getCurrentPath() );
		
		$iUrlPos = 1;
		
		if ( $this->bUrlContainsAppName ) {
			// in first pos we have app name so we switch to controller name
			$iUrlPos++;
		}
		
		$this->sControllerName = sprintf( 
			'Controller_%s', 
			ucfirst( strtolower( $this->oConfig->getValue( 'router.default_controller', 'index' ) ) )
		);
		$this->sFunctionName = sprintf( 
			'%sAction',
			strtolower( $this->oConfig->getValue( 'router.default_function', 'index' ) )
		);
		
		// Check if we should use different then default controller
		if ( ! empty( $aUrlParts[ $iUrlPos ] ) ) {
			
			$this->sControllerName = 'Controller_' . ucfirst( $aUrlParts[ $iUrlPos ] );
			
			// Function name
			$iUrlPos++;
			if ( ! empty( $aUrlParts[ $iUrlPos ] ) ) {
				
				$this->sFunctionName =  strtolower( $aUrlParts[ $iUrlPos ] ). 'Action';
				
				//params pos
				$iUrlPos++;
				if ( ! empty( $aUrlParts[ $iUrlPos ] ) ) {
					for ( $i = $iUrlPos; $i < count( $aUrlParts ); $i++ ) {
						$this->aParams[] = $aUrlParts[ $i ];
					}
				}
			} // if function
			
		} // if controller
		
	}
	
	/**
	 * Returns url for given file name and type
	 * 
	 * @param string $sName - file name/path
	 * @param string $sType - file type
	 * @return string
	 */
	public function getFileUrl( $sName, $sType ) {
		return $this->sRootUrl . '/' . $sType . '/' . $sName;
	}
	
}

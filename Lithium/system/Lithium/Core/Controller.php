<?php

abstract class Core_Controller {
	
	/**
	 * Instance of Lithium
	 *
	 * @var Lithium
	 */
	protected $oLithium;
	
	/**
	 * Instance of Router class
	 * 
	 * @var Router
	 */
	protected $oRouter;
	
	/**
	 * Template name or View object
	 * 
	 * @var mixed
	 */
	protected $mTemplate;
	
	/**
	 * Class initializer
	 */
	public function init() {
		// make View object from name of template
		if ( ! empty( $this->mTemplate ) && is_string( $this->mTemplate ) ) {
			$this->mTemplate = new View( $this->mTemplate );
		}
	}
	
	/**
	 * Returns main View for current controller
	 * 
	 * @return View
	 */
	public function getTemplate() {
		
		// Make View object from name of template
		if ( is_string( $this->mTemplate ) && ( strlen( $this->mTemplate ) > 0 ) ) {
			$this->mTemplate = $this->getView( $this->mTemplate );
		}
		
		if ( is_object( $this->mTemplate ) && ( $this->mTemplate instanceof View ) ) {
			return $this->mTemplate;
		}
		
		throw new Lithium_Exception( 'core.controller_template_name_not_set', get_class( $this ) );
	}
	
	/**
	 * Sets template
	 * 
	 * @param string $sTemplate
	 */
	public function setTemplate( $sTemplate ) {
		$this->mTemplate = $sTemplate;
	}
	
	/**
	 * Sets sub directory of views
	 * 
	 * Wrapper for View::setDefaultTemplateDir
	 * 
	 * @param string $sDir
	 */
	public function setLayoutDir( $sDir ) {
		View::setDefaultTemplateDir( $sDir );
	}
	
	/**
	 * Returns url for given location
	 * 
	 * Wrappper for same router method
	 * 
	 * @param string $sPath
	 * @return string
	 */
	protected function getPageUrl( $sPath = '' ) {
		return $this->oRouter->getPageUrl( $sPath );
	}
	
	/**
	 * Returns name od current controller (without prefix: 'Controller_')
	 *
	 * @return string
	 */
	public function getName() {
		
		// cut last part of class name
		return substr( get_class( $this ), 11 );
		
	}
	
	public function setPropertyObject( $oObject ) {
		
		if ( ! is_object( $oObject ) ) {
			throw new Lithium_Exception( 'core.incorrect_func_arg' );
		}
		
		switch ( get_class( $oObject ) ) {
			
			case 'Lithium' :
				$this->oLithium = $oObject;
				break;
			
			case 'Router' :
				$this->oRouter = $oObject;
				break;
			
			default:
				throw new Lithium_Exception( 'core.incorrect_func_arg' );
			
		}
		
	}
	
	/**
	 * Handles methods that do not exist.
	 *
	 * @param   string  method name
	 * @param   array   arguments
	 * @return  void
	 */
	public function __call( $sMethod, $aArgs ) {
		
		if ( is_array( $aArgs ) ) {
			foreach ( $aArgs as $iKey => $mArg ) {
				$aArgs[ $iKey ] = is_string( $mArg ) ? $mArg : '<i>[mixed]</i>';
			}
			$aArgs = implode( ', ', $aArgs );
		} else {
			$aArgs = is_string( $aArgs ) ? $aArgs : '<i>[mixed]</i>';
		}
		
		throw new Lithium_404_Exception( 'Core.action_not_found', get_class( $this ), $sMethod, $aArgs );
	}
	
	public function preDispatch() {
		
	}
	
	public function postDispatch() {
		
	}
	
	protected function redirect( $sUrl = '/', $sMethod = '302' ) {
		
		if ( headers_sent() ) {
			return;
		}
		
		// if there is no full url string in sUrl we build url for app page
		if ( ! preg_match( '!^http://!', $sUrl ) ) {
			$sUrl = $this->getPageUrl( $sUrl );
		}
		
		$aCodes = array(
			'301' => 'Moved Permanently',
			'302' => 'Found',
			'303' => 'See Other',
			'304' => 'Not Modified',
			'305' => 'Use Proxy',
			'307' => 'Temporary Redirect'
		);
		
		$sMethod = (string)$sMethod;
		
		$sMethod = isset( $aCodes[ $sMethod] ) ? $sMethod : '302';
		
		header( 'HTTP/1.1 ' . $sMethod . ' ' . $aCodes[ $sMethod ] );
		header( 'Location: ' . $sUrl );
		
	}
	
	/**
	 * Return values from _POST array
	 * 
	 * @param string $sName - name of the value
	 * @param mixed $mDefaultValue - value that will be returned if given name will not be found
	 * @return mixed
	 */
	protected function post( $sName, $mDefaultValue = null ) {
		
		if ( isset( $_POST[ $sName ] ) ) {
			$mDefaultValue = $_POST[ $sName ];
		}
		
		return $mDefaultValue;
		
	}
	
	/**
	 * Return landuage strings
	 * 
	 * @param string $sKey - key name
	 * @param array $aParams - values that will be used in language string
	 * @return string
	 */
	protected function getLang( $sKey, $aParams = array() ) {
		
		// check do we have dot in key name
		if ( strpos( $sKey, '.' ) === false ) {
			// add class name to searched key name
			$sKey = substr( get_class($this), 11 ) . '.' . $sKey;
		}
		
		return $this->oLithium->getLang( $sKey, $aParams );
	}
	
	/**
	 * Returns instance of model class for given name
	 * 
	 * @param string $sName
	 * @param int $iId
	 * @return Core_Model
	 */
	protected function getModel( $sName, $iId = null ) {
		$sName = 'Model_' . ucfirst( strtolower( $sName ) );
		return new $sName( $iId );
	}
	
	/**
	 * Returns instance of View object for given name
	 * 
	 * @param string $sViewName
	 * @param array $aData
	 * @return View
	 */
	protected function getView( $sViewName, $aData = array() ) {
		return View::factory( $sViewName, $aData );
	}
	
	/**
	 * Creates and returns Validator module
	 * 
	 * @return Module_Validator
	 */
	protected function getModule( $sName ) {
		
		$sName = 'Module_' . ucfirst( strtolower( $sName ) );
		
		$aParams = func_get_args();
		array_shift( $aParams );
		
		if ( empty( $aParams ) ) {
			return new $sName();
		}
		
		if ( method_exists( $sName,  '__construct' ) === false ) {
			throw new Lithium_Exception( 'core.controller_module_without_constructor', $sName );
		}
		
		$refClass = new ReflectionClass( $sName );
		return $refClass->newInstanceArgs( $aParams ); 
	}
	
}

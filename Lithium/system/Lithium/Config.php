<?php

/**
 * Class responsible for storying all configuration values
 * 
 * @package Lithium
 */
class Config {
	
	/**
	 * Array of configuration values
	 * 
	 * @var array
	 */
	protected $aConfigValues = array();
	
	/**
	 * Class constructor
	 */
	public function __construct( Array $aConfig = null ) {
		
		$this->aConfigValues = $aConfig;
		
		// setting default names
		$this->aConfigValues[ 'router' ][ 'default_controller' ] = 'Index';
		$this->aConfigValues[ 'router' ][ 'default_function' ] = 'index';
		
	}
	
	/**
	 * Returns config values
	 * 
	 * @param string $sName - value name
	 * @return mixed
	 */
	public function getValue( $sPath = null, $mDefaultValue = null ) {
		
		// check if should we return whole config
		if ( is_null( $sPath ) ) {
			return $this->aConfigValues;
		}
		
		return $this->getPathValue( $sPath, $this->aConfigValues, $mDefaultValue);
		
	}
	
	/**
	 * Check if given config path has value
	 * 
	 * @param bool $sPath
	 */
	public function hasValue( $sPath ) {
		
		$aPath = explode( '.', $sPath );
		
		return eval( sprintf( 'return isset( $this->aConfigValues["%s"] );', implode( '"]["', $aPath ) ) );
	}
	
	protected function getPathValue( $sPath, &$aConfig, $mDefaultValue = null  ) {
		
		$aPath = explode( '.', $sPath, 2 );
		
		$aPath[0] = strtolower( $aPath[0] );
		
		if ( ! isset( $aConfig[ $aPath[0] ] ) ) {
			if ( is_null( $mDefaultValue ) ) {
				throw new Lithium_Exception( 'core.conig_value_not_found', $sPath );
			} else {
				return $mDefaultValue;
			}
		}
		
		// check do we have second level
		if ( isset( $aPath[1] ) ) {
			
			
			// check should we parse deeper levels
			if ( strpos( $aPath[1], '.' ) !== false ) {
				return $this->getPathValue( $aPath[1], $aConfig[ $aPath[0] ], $mDefaultValue );
			}
			
			$aPath[1] = strtolower( $aPath[1] );
			
			if ( ! isset( $aConfig[ $aPath[0] ][ $aPath[1] ] ) ) {
				if ( is_null( $mDefaultValue ) ) {
					throw new Lithium_Exception( 'Config value not found' );
				} else {
					return $mDefaultValue;
				}
			}
			
			return $aConfig[ $aPath[0] ][ $aPath[1] ];
			
		}
		
		return $aConfig[ $aPath[0] ];
		
	}
	
	/**
	 * Setts config values
	 * 
	 * @param string $sPath - value path
	 * @param mixed $mValue - value
	 * @return null
	 */
	public function setValue( $sPath, $mValue ) {
		
		$aPath = explode( '.', $sPath );
		
		foreach ( $aPath as $iKey => $sKey ) {
			$aPath[ $iKey ] = strtolower( $sKey );
		}
		
		eval( '$this->aConfigValues[ "' . implode( '"]["', $aPath ) . '"] = $mValue;' );

	}
	
	/**
	 * Loads config values from given ini file
	 * 
	 * @param string $sIniPath - ini file path
	 * @return null
	 */
	public function loadIniFile( $sIniPath ) {
		
		if ( ! file_exists( $sIniPath ) ) {
			throw new Lithium_Exception( 'Incorrect INI file path' );
		}
		
		$aIniConfig = parse_ini_file( $sIniPath, true );
		
		foreach ( $aIniConfig as $sSectionName => $aSettings ) {
			
			foreach ( $aSettings as $sName => $mValue ) {
				
				// check do we have deeper levels
				$aPath = explode( '.', $sName );
				if ( isset( $aPath[1] ) ) {
					
					// parse deeper level and add them to config array
					foreach ( $aPath as $iKey => $sKey ) {
						$aPath[ $iKey ] = strtolower( $sKey );
					}
					
					eval( '$this->aConfigValues[ strtolower( $sSectionName ) ]["' . implode( '"]["', $aPath ) . '"] = $mValue;' );
					
				} else {
					$this->aConfigValues[ strtolower( $sSectionName ) ][ strtolower( $sName ) ] = $mValue;
				}
				
			} // foreach
			
		} // foreach
		
	} // func
	
	/**
	 * Hides fields that shouldn't be shown in debug view
	 */
	public function hideFieldsForDebug() {
		
		$sHideFields = $this->getValue( 'General.Debug_hide', null );
		if ( ! is_null( $sHideFields ) ) {
			$sHideFields = explode( ',', $sHideFields );
			foreach ( $sHideFields as $sField ) {
				$this->setValue( $sField, '' );
			}
		}
		
	}
}

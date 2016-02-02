<?php

/**
 * Class responsible for loading files and classes
 * 
 * @package Lithium
 *
 */
class Loader {
	
	/**
	 * Stores paths which will be used to search for files
	 *
	 * @var array
	 */
	protected static $aPaths;
	
	/**
	 * Register {@link autoload()} with spl_autoload()
	 *
	 * @param string $sClassName	- class name with autoload function
	 * @param bool $bEnabled 		- register/unregister autoload function
	 */
	public static function registerAutoload( $sClassName = 'Loader', $bEnabled = true ) {
		
		if ( ! function_exists( 'spl_autoload_register' ) ) {
			throw new Exception( 'spl_autoload does not exist in this PHP installation' );
		}

		self::loadClass( $sClassName );
		$aMethods = get_class_methods( $sClassName );
		if ( ! in_array( 'autoload', (array)$aMethods ) ) {
			throw new Exception( "The class \"$sClassName\" does not have an autoload() method" );
		}

		if ( $bEnabled === true ) {
			spl_autoload_register( array( $sClassName, 'autoload' ) );
		} else {
			spl_autoload_unregister( array( $sClassName, 'autoload' ) );
		}
		
	}
	
	/**
	 * spl_autoload() suitable implementation for supporting class autoloading.
	 *
	 * @param string $sClassName
	 * @return string/bool
	 */
	public static function autoload( $sClassName ) {
		
		try {
			
			self::loadClass( $sClassName );
			return $sClassName;
			
		} catch ( Exception $e ) {
			return false;
		}
	}
	
	/**
	 * Include file of given class name
	 * 
	 * @param string $sClassName - name of class
	 * @param string $sDifferentClassName - name of class for check does it exist in file
	 * @return void
	 */
	public static function loadClass( $sClassName, $sDifferentClassName = null ) {
		
		if ( class_exists( $sClassName, false ) || interface_exists( $sClassName, false ) ) {
			return;
		}
		
		$sFile = self::findFile( str_replace( '_', DIRECTORY_SEPARATOR, $sClassName ) . EXT );
		
		if ( $sFile === false ) {
			
			error_log( 'Lithium error: Class file does not exist! ( ' . $sClassName . ' )' );
			throw new Exception( 'Lithium error: Class file does not exists! ' );
			
		}
		
		include_once( $sFile );
		
		$sDifferentClassName = is_null( $sDifferentClassName ) ? $sClassName : $sDifferentClassName;
		
		if ( ! class_exists( $sDifferentClassName, false ) && ! interface_exists( $sDifferentClassName, false ) ) {
			
			error_log( 'Lithium error: Class does not exists in file! [' . $sDifferentClassName . '] (' . $sFile . ')' );
			throw new Exception( 'Lithium error: Class does not exists!' );
			
		}
		
	}
	
	/**
	 * Add directory to path list
	 * 
	 * @param mixed $mPath - dir or couple of dirs in array
	 * @param bool $bPrepend - should the list be prepend
	 * @return bool
	 */
	public static function addPath( $mPath, $bPrepend = true ) {
		
		$bSuccess = true;
		
		if ( empty( self::$aPaths ) ) {
			self::findBasePath();
		}
		
		// if path is not an array we make it array
		if ( ! is_array( $mPath ) ) {
			$mPath = array( $mPath );
		}
		
		array_reverse( $mPath );

		foreach( $mPath as & $sPath ) {
			
			$sPath = realpath( $sPath );
			// check if such dir exists
			if ( ! is_dir( $sPath ) ) {
				$bSuccess = false;
				continue;
			}
			// add directory separator at the end of each path
			if ( substr( $sPath, -1 ) != DIRECTORY_SEPARATOR ) $sPath .= DIRECTORY_SEPARATOR;
			
			if ( $bPrepend ) {
				array_unshift( self::$aPaths, $sPath );
			} else {
				array_push( self::$aPaths, $sPath );
			}
			
		}
		
		return $bSuccess;
		
	}
	
	protected static function findBasePath() {
		
		self::$aPaths = array( str_replace( __CLASS__ . EXT, '', __FILE__ ) );
		
	}
	
	public static function findFile( $sFilePath, &$aPathsScanned = array() ) {
		
		if ( empty( self::$aPaths ) ) {
			self::findBasePath();
		}
		
		foreach( self::$aPaths as $sPath ) {
			
			$sTmpFilePath = $sPath . $sFilePath;
      $aPathsScanned[] = $sTmpFilePath;
			if ( file_exists( $sTmpFilePath ) ) {
				return $sTmpFilePath;
			}
			
		}
		
		return false;
		
	}
	
}

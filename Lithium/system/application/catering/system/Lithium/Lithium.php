<?php
// Lithium version
define( 'LITHIUM_VERSION', '0.8' );
// Define Lithium error constant
define( 'E_LITHIUM', 42 );
// Define 404 error constant
define( 'E_PAGE_NOT_FOUND', 43 );
// Define database error constant
define( 'E_DATABASE_ERROR', 44 );

/**
 * Main core of framework
 *
 */
final class Lithium {
	
	/**
	 * Singletone instance
	 *
	 * @var Lithium
	 */
	private static $oInstance;
	
	private static $bThrowErrors = true;
	
	private $sLogsDirectory;
	
	private $sLanguage = 'pl_PL';
	
	private $sControllerName;
	
	private $aLanguageStrings = array();
	
	/**
	 * Instance of configuraction object/container
	 * 
	 * @var Config
	 */
	private $oConfig;
	
	private $aPaths = array();
	
	private $aLoadedFiles = array();
	
	
	/**
	 * Singletone instance getter
	 *
	 * @return Lithium
	 */
	public static function getInstance()
	{
		if ( null === self::$oInstance ) {
			
			self::$oInstance = new self();

		}

		return self::$oInstance;
	}
	
	/**
	 * Setter for Config class
	 * 
	 * @param Config $oConfig
	 */
	public function setConfig( Config $oConfig ) {
		$this->oConfig = $oConfig;
	}
	
	/**
	 * Setter for Router class
	 * 
	 * @param Router $oRouter
	 */
	public function setRouter( Router $oRouter ) {
		$this->oRouter = $oRouter;
	}
	
	/**
	 * Class constructor
	 *
	 * @param mixed $mConfig 	Array or path to ini file
	 */
	private function __construct() {;
		
		// Set error handler
		if ( ! IN_PRODUCTION ) {
//			set_error_handler( array( $this, 'exception_handler' ) );
		}

		// Set exception handler
		set_exception_handler( array( $this, 'exception_handler' ) );
		
	}
	
	public function addPath( $sPath ) {
		
		// if nesesery we add syspath at begining of path 
		if ( ! strpos( $sPath, SYSPATH ) ) $sPath = SYSPATH . $sPath;
		
		// get real path
		$sPath = str_replace( '\\', '/', realpath( $sPath ) );
		
		// add slash at the end if needed
		if ( substr( $sPath, -1 ) != '/' ) $sPath .= '/';
		
		// put path on the begining of array
		if ( $sPath !== false ) array_unshift( $this->aPaths, $sPath );
		
	}
	
	/**
	 * Gets part of config array releated with given path
	 *
	 * @throws Lithium_Exception	when key not found
	 * @param string $sPath
	 * @return assoc-array
	 */
	public function getConfig( $sPath = null, $mDefaultValue = null ) {
		
		try {
			
			// check if should we return whole config
			if ( empty( $sPath ) ) {
				return $this->oConfig->getValue();
			}
			
			return $this->oConfig->getValue( $sPath, $mDefaultValue );
			
		} catch ( Lithium_Exception $oError ) {
			
			$this->riseWarning( 'Config key not found: ' . $sPath );
			return false;
			
		}
		
	
	}
	
	/**
	 * Set up class properties etc.
	 */
	private function Setup() {
		
		// place for additional sets
		// e.g. $this->aConfig[ section_key ][ value_key ] = value
		
		$sAppConfigIni = DOCROOT . $this->oConfig->getValue( 
			sprintf( 'applications.%s.config_file', $this->oRouter->getApplicationName() ) 
		);
		
		$this->oConfig->loadIniFile( $sAppConfigIni );
		
		$this->sLogsDirectory = $this->getConfig( 'General.Logs_directory', DOCROOT . 'logs/' );
		$this->sLogsDirectory .= date( 'Y-m-d' ) . '/';
		
		// set main framework path
		$this->addPath( 'Lithium' );
		
		// set application path received from config file
		if ( $sAppPath = $this->getConfig( 'General.App_path' ) ) {
			
			$this->addPath( $sAppPath );
			Loader::addPath( DOCROOT . $sAppPath );
			
		}
		
		// add path for external classes
		Loader::addPath( DOCROOT );
		
		// set language
		if ( $sLanguage = $this->getConfig( 'Locale.Language' ) ) {
			$this->sLanguage = $sLanguage;
		}
		
		Core_Model::setLithium( $this );
		Core_Driver::setLithium( $this );
		
		// set router configs and initialize it
		$this->oRouter->addConfig( $this->getConfig( 'Router' ) );
		$this->oRouter->init();
		
		View::setRouter( $this->oRouter );
		Module_Sorter::setRouter( $this->oRouter );
		Module_Pagination::setRouter( $this->oRouter );
		
	}
	
	/**
	 * Get string from language string tables
	 *
	 * @param string $sKey
	 * @return string
	 */
	public function getLang( $sKey, $aParams = array() ) {
		
		// split key when it contains dot
		if ( strpos( $sKey, '.' ) ) {
			
			$aKeyParts = explode( '.', $sKey, 2 );
			
			$sKey = $aKeyParts[1];
			$sNamespace = ucfirst( $aKeyParts[0] );
			
		} else {
			
			$sNamespace = $this->sControllerName;
			
		}
		
		// if there are more then two args in function we treat them as array used to parse string
		if ( func_num_args() > 2 ) {
			$aParams = func_get_args();
			array_shift( $aParams );
		}
		
		// check should we return array element
		if ( preg_match( '/([^\\[]+)\\[([0-9]+)\\]$/', $sKey, $aRet ) ) {
			$iIndex = $aRet[2];
			$sKey = $aRet[1];
		}
		
		$sNewKey = $sNamespace . '.' . $sKey;
		
		// check if such key exists already in language array
		if ( array_key_exists( $sNewKey, $this->aLanguageStrings ) ) {
			
			$aRet = $this->parseLang( $this->aLanguageStrings[ $sNewKey ] , $aParams );
			return  isset( $iIndex ) ? $aRet[ $iIndex ] : $aRet;
			
		}
		
		// receive real path of searched file
		if ( $sFile = $this->findFile( $sNamespace, 'i18n' ) ) {
			
			require( $sFile ); // loading language table from file
			
			if ( isset( $aLang ) AND is_array( $aLang ) ) {
				
				// add each value from loaded file into lang array
				foreach ( $aLang as $sCode => $sValue ) {
					$this->aLanguageStrings[ $sNamespace . '.' . $sCode ] = $sValue;
				}
				
			}
			
		}
		
		// check if we have such a key
		if ( array_key_exists( $sNewKey, $this->aLanguageStrings ) ) {
			
			$aRet = $this->parseLang( $this->aLanguageStrings[ $sNewKey ] , $aParams );
			return  isset( $iIndex ) ? $aRet[ $iIndex ] : $aRet;
			
		} else {

			// when key does not exists we rise warring
			$this->riseWarning( 'Key not found: ' . $sNewKey );
			
			// to prevent from executing whole code again we add it to language array
			$this->aLanguageStrings[ $sNewKey ] = $sKey;
			
			return $sKey;
			
		}
		
	} // func getLang
	
	/**
	 * Fill message with given params
	 *
	 * @param string $sMessage
	 * @param array $aParams
	 * @return string
	 */
	protected function parseLang( $mMessage, $aParams ) {
		
		// check if there is a need to parse string
		if ( empty( $aParams ) ) {
			return $mMessage;
		}
		
		if ( ! is_array( $aParams ) ) {
			$aParams = array( $aParams );
		}
		
		// check if message is an array
		if ( is_array( $mMessage ) ) {
			
			// we only need to parse description
			$sMessage = $mMessage[1];
			
		} else {
			$sMessage = $mMessage;
		}
		
		$iCountS = substr_count( $sMessage, '%s' );
		$iCountParams = count( $aParams );
		
		// check if there is a need to fill array with params
		if( $iCountParams < $iCountS ) {
			$aParams = array_merge( $aParams, array_fill( $iCountParams - 1, $iCountS - $iCountParams, '') );
		}
		// add string as a first param
		array_unshift($aParams, $sMessage);
		
		$sMessage = call_user_func_array( 'sprintf', $aParams );
		
		if ( is_array( $mMessage ) ) {
			
			// add parsed description
			$mMessage[1] = $sMessage;
			
		} else {
			$mMessage = $sMessage;
		}
		
		return $mMessage;
		
	} // func parseLang
	
	/**
	 * Log and display warning
	 *
	 * @param string $sWorning
	 * @param string $sFunction
	 * @param string $sLine
	 * 
	 * @return void
	 */
	public function riseWarning( $sMessage, $iWarningLevel = 0 ) {
		
		$aCaller = debug_backtrace();
		$aCaller = $aCaller[ $iWarningLevel ];
		
		$aCaller['file'] = preg_replace( '!^' . preg_quote( DOCROOT ) . '!', '', $aCaller['file'] );
		
		$sMessage = sprintf( '%s:%d (%s) > %s', $aCaller['file'], $aCaller['line'], $this->oRouter->getPageUrl(), $sMessage );
		
		if ( ! IN_PRODUCTION ) {
			global $aLithiumWarnings;
			$aLithiumWarnings[] = '<p style="border: solid 2px red; padding: 3px">' . $sMessage . '</p>';
		}
		
		error_log( $sMessage );
		
	}
	
	/**
	 * Adds error information into Lithium logs
	 * 
	 * Function accepts additional params
	 * 
	 * @param string $sMessage - error message
	 */
	public function logError( $sMessage ) {
		
		// log error into php logs and notice user about error (if we are not on production)
		$this->riseWarning( $sMessage, 1 );
		
		// create dir if necessary
		if ( ! is_dir( rtrim( $this->sLogsDirectory, '/' ) ) ) {
			if ( ! mkdir( rtrim( $this->sLogsDirectory, '/' ), 0775, true ) ) {
				$this->riseWarning( 'No privileges to create dir: ' . $this->sLogsDirectory );
			}
		}
		
		// create message
		$sMessage .= "\n" . func_num_args() > 1 ? serialize( array_slice( func_get_args(), 1 ) ) : '';
		$sMessage .= "\n" . $this->oRouter->getPageUrl();
		$sMessage .= "\nbacktrace=" . serialize( $this->getBacktrace( array_slice( debug_backtrace(), 1 ), true ) );
		
		$sLogFile = $this->sLogsDirectory . md5( $sMessage ) . '.log';
		
		$iQuantity = 0;
		
		// check do we have already such kind of log
		if ( is_file( $sLogFile ) ) {
			
			$fp = @fopen ( $sLogFile, 'r' );
			if ( $fp ) {
				$iLine = 0;
				while ( ! feof( $fp )) {
					$buffer = fgets( $fp );
					
					switch ( $iLine ) {
						case 0 :
							$iQuantity = (int)trim( $buffer );
							break;
						case 1 :
							// date of last log
							break;
						default :
							break 2;
					}
					$iLine++;
					
					if ( $iLine == 2 ) break;
					
				}
				fclose( $fp );
				
			} // if
		} // if
		
		$iQuantity++;
		
		// save message in log file
		$sMessage = $iQuantity . "\n" . date( 'Y-m-d H:i:s' ) . "\n" . $sMessage;
		file_put_contents( $sLogFile, $sMessage );
		
	}
	
	/**
	 * Find and return real path of searched file
	 *
	 * @param string $sName
	 * @param string $sType
	 * @return string
	 * @return false	if file not found
	 */
	public function findFile( $sName, $sType = 'Controller' ) {
		
		// Remove sType from controller name
		$sName = str_replace( $sType . '_', '', $sName );
		
		if ( $sType == 'i18n' ) $sType .= DIRECTORY_SEPARATOR . $this->sLanguage;
		
		return Loader::findFile( $sType . DIRECTORY_SEPARATOR . $sName . EXT );
		
	}
	
	public function getExternalFilePath( $sName, $sType ) {
		
		static $sAppUrl = null;
		
		if ( is_null( $sAppUrl ) ) {
			$sAppUrl = substr( 'http://' . $_SERVER[ 'HTTP_HOST' ] . URLROOT, 0, -1 );
		}
		
		return $sAppUrl . '/' . $sType . '/' . $sName;
		
	}
	
	public function Dispatch() {
		
		$this->Setup();	// run setup
		
		// get name of controller
		$sControllerName = $this->oRouter->getControllerName();
		$oController = $this->loadController( $sControllerName );
		
		// get function name
		$sFunctionName = $this->oRouter->getFunctionName();
		// get params
		$aParams = $this->oRouter->getParams();
		
		// set Lithium object instance in controller
		$oController->setPropertyObject( $this );
		// set Router object instance in controller
		$oController->setPropertyObject( $this->oRouter );
		
		// initialize controller
		$oController->init();
		
		// call function
		call_user_func_array( array( $oController, $sFunctionName ), $aParams );
		
		// run preDispatcher
		$oController->preDispatch();
		
		// render view if such used
		if ( ( is_object( $oController->mTemplate ) ) && ( is_a( $oController->mTemplate, 'View' ) ) ) {
			$oController->mTemplate->render(true);
		}
		// run postDispatcher
		$oController->postDispatch();
		
		$oController = null;
		
	}
	
	protected function loadController( $sControllerName ) {
		
		// check if we will be able to load such a file (where controller should be defined)
		if ( $this->findFile( $sControllerName ) === false ) {
			throw new Lithium_404_Exception( 'Core.controller_not_found', $sControllerName );
		}
		
		return new $sControllerName( $this );
		
	}
	
	
	/**
	 * PHP error and exception handler
	 *
	 * @param   integer|object  exception object or error code
	 * @param   string          error message
	 * @param   string          filename
	 * @param   integer         line number
	 * @return  void
	 */
	public function exception_handler( $oException )
	{
		// PHP errors have 5 args, always
		$PHP_ERROR = (func_num_args() === 5);

		// Test to see if errors should be displayed
		if ($PHP_ERROR AND (error_reporting() & $oException) === 0) return;


		if ( ! isset( self::$oInstance ) ) {
			die( 'Lithium internal error: Cannot initialize framework ( ' . $oException->getMessage() . ' )' );
		}

		while ( ob_get_level() > 0 ) {
			// Close open buffers
			ob_end_clean();
		}
		
		$aParams = array();
		
		$template 	= 'lithium_error';
		$aViewData['version'] = LITHIUM_VERSION;
		
		if ( $PHP_ERROR ) {
			
			$code			= $oException;
			$type			= 'PHP Error';
			$message		= IN_PRODUCTION ? 'error.' . E_RECOVERABLE_ERROR : 'core.' . $oException;
			
			$aParams = func_get_args();
			array_shift( $aParams );
			
		} else {
			
			$code	 = $oException->getCode();
			$type	 = get_class( $oException );
			$file	 = IN_PRODUCTION ? null : preg_replace( '!^' . preg_quote( DOCROOT ) . '!', '', $oException->getFile() );
			$line	 = $oException->getLine();
			$message = IN_PRODUCTION ? 'error.' . $code : $oException->getMessage();
			
			if ( $oException instanceof Lithium_Exception ) {
				
				// get message from code
				$aParams = $oException->getParams();
				
				// set template
				$template = $oException->getTemplate();
				
				// Send headers
				if ( ! headers_sent() ) {
					$oException->sendHeaders();
				}
			}
			
		}
		
		if ( strstr( $message, 'error.' ) ||  ( ! IN_PRODUCTION ) ) {
			$message = $this->getLang( $message, $aParams );
		}
		
		if ( ! IN_PRODUCTION ) {
			
			$aViewData['aTrace'] = $PHP_ERROR ? array_slice( debug_backtrace(), 1 ) : $oException->getTrace();
			
			$aViewData['aTrace'] = $this->getBacktrace( $aViewData['aTrace'] );
			
		}
		
		$aViewData['description'] 	= is_array( $message ) ? $message[1] : '';
		$aViewData['message'] 		= is_array( $message ) ? $message[0] : $message;
		
		$aViewData['title'] = 'Error: ' . $message;
		
//		if ( class_exists( 'View', false ) ) {
//			echo View::factory( $template, $aViewData );
//		} else {
			$sFile = 'View' . DIRECTORY_SEPARATOR . $template . EXT;
			
			$sIncludeFile = Loader::findFile( $sFile );
			if ( $sIncludeFile === false ) {
				$this->logError( 'Cannot find view: ' . $sFile );
				echo 'Lithium internal error.';
			} else {
				extract( $aViewData );
				require $sIncludeFile;
			}
//		}
		
		// Turn off error reporting
		error_reporting(0);
		exit;
		
	}
	
	/**
	 * Returns formated backtrace data
	 */
	protected function getBacktrace( $aBacktrace, $bShort = false ) {
		
		if ( ! is_array( $aBacktrace ) ) return;

		// Final output
		$aTrace = array();

		foreach ( $aBacktrace as $aEntry ) {
			
			$aTmp = $aEntry;
			
			if ( $bShort ) {
				unset( $aTmp['object'] );
			}
			
			if ( isset( $aEntry[ 'file' ] ) ) {
				$aTmp[ 'file' ] = preg_replace( '!^'.preg_quote( DOCROOT ).'!' , '', $aEntry['file'] );
			}
			
			// Add function args
			$aTmp['args'] = array();
			if ( isset( $aEntry['args'] ) AND is_array( $aEntry['args'] ) ) {
				
				while ( $arg = array_shift( $aEntry['args'] ) ) {
					
					if ( is_string( $arg ) AND is_file( $arg ) ) {
						// Remove docroot from filename
						$arg = preg_replace( '!^' . preg_quote( DOCROOT ) . '!', '', $arg );
					}
					
					if ( $bShort ) {
						$sContent = is_object( $arg ) ? get_class( $arg ) : gettype( $arg );
					} else {
						$sContent = htmlspecialchars( print_r( $arg, 1 ) );
					}
					
					$sShort = $sContent;
					if ( strlen( $sShort ) > 20 ) {
						$sShort = substr( $sShort, 0, 20 );
					}
					// check do we have new line in particular arg or do we have to clear sShort string
					if ( ( strlen( $sShort ) < 20 ) || strpos( $sShort, "\n" ) ) {
						$sShort =  substr( $sContent, 0, strpos( $sContent, "\n" ) ); // strstr( $sContent, "\n", true )
					}
					
					$aTmp['args'][] = array( 
						'content' => $sContent,
						'short' => $sShort
					);
					
				} // while
			} // if

			$aTrace[] = $aTmp;
			
		} // foreach
		
		return $aTrace;
		
	}
	
	public function getCurrentUrlPath() {
		return $this->oRouter->getCurrentPath();
	}
	
}


/**
 * Main framework exception
 * 
 * @package Lithium
 *
 */
class Lithium_Exception extends Exception {
	
	protected $sTemplate = 'lithium_error';
	
	protected $aParams = array();
	
	public function __construct( $sMessage ) {
		
		if ( empty( $sMessage ) ) $sMessage = 'Unknown error';
		
		$this->aParams = array_slice(func_get_args(), 1);
		
		parent::__construct( $sMessage );
		
	}
	
	/**
	 * Returns template name
	 *
	 * @return string
	 */
	public function getTemplate() {
		return $this->sTemplate;
	}
	
	public function getParams() {
		return $this->aParams;
	}
	
	/**
	 * Sends an Internal Server Error header.
	 *
	 * @return  void
	 */
	public function sendHeaders()
	{
		// Send the 500 header
		header('HTTP/1.1 500 Internal Server Error');
	}
	
	public function __toString() {
		return $this->getMessage();
	}
	
}


class Lithium_404_Exception extends Lithium_Exception {
	
	protected $code = E_PAGE_NOT_FOUND;
	
	protected $sTemplate = 'lithium_error_404';
	
	/**
	 * Sends "File Not Found" headers, to emulate server behavior.
	 *
	 * @return void
	 */
	public function sendHeaders() {
		// Send the 404 header
		header('HTTP/1.1 404 File Not Found');
	}
	
}

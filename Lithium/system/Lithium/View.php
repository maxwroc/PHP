<?php
class View {
	
	/**
	 * Name or path of template file
	 * 
	 * @var string
	 */
	protected $sViewName;
	
	/**
	 * Router instance
	 * 
	 * @var Router
	 */
	protected static $oRouter;
	
	/**
	 * Data for template
	 * 
	 * @var array
	 */
	protected $aLocalData = array();
	
	/**
	 * Template root directory
	 * 
	 * @var string
	 */
	protected static $sDefaultTemplateDir = '';
	
	public static function factory( $sViewName, $aData = array() ) {
		return new View( $sViewName, $aData );
	}
	
	/**
	 * Setter for Router instance
	 * 
	 * @param Router $oRouter
	 */
	public static function setRouter( Router $oRouter ) {
		self::$oRouter = $oRouter;
	}
	
	/**
	 * Class constructor
	 * 
	 * @param string $sViewName - template name/file/path
	 * @param array $aData - template data
	 */
	public function __construct( $sViewName, $aData = array() ) {
		
		$this->sViewName = strtolower( self::$sDefaultTemplateDir . $sViewName );
		
		if ( ( ! empty( $aData ) ) AND ( is_array( $aData ) ) ) {
			
			// aLocalData might be filled in extedned class so we merge arrays
			$this->aLocalData = array_merge( $this->aLocalData, $aData );
			
		}
		
	}
	
	/**
	 * Magic seter for template data
	 */
	public function __set( $sName, $mValue ) {
		
		$this->aLocalData[ $sName ] = $mValue;
		
	}
	
	/**
	 * Magic getter for template data
	 */
	public function __get( $sName ) {
		
		if ( isset( $this->aLocalData[ $sName ] ) ) {
			return $this->aLocalData[ $sName ];
		}
		
	}
	
	/**
	 * Setter for template root directory
	 * 
	 * @param string $sPath - template root directory
	 */
	public static function setDefaultTemplateDir( $sPath ) {
		
		if ( $sPath[ strlen( $sPath ) - 1 ] != '/' ) {
			$sPath .= '/';
		}
		
		self::$sDefaultTemplateDir = $sPath;
	}
	
	/**
	 * Getter for template root directory
	 * 
	 * @return string
	 */
	public static function getDefaultTemplateDir() {
		return self::$sDefaultTemplateDir;
	}
	
	/**
	 *  Return or print template filled with data
	 * 
	 * @param bool $bPrint - do we want to print result
	 * @return string
	 */
	public function render( $bPrint = false ) {
		
		if ( empty( $this->sViewName ) ) {
			throw Lithium_Exception( 'View file not set' );
		}
		
		$sView = $this->load_view();
		
		if ( $bPrint ) {
			
			echo $sView;
			return;
			
		}
		
		return $sView;
		
	}
	
	/**
	 * Fill template with data and return result
	 * 
	 * @return string
	 */
	protected function load_view() {
		
		if ( $sFile = Loader::findFile( 'View' . DIRECTORY_SEPARATOR . $this->sViewName . EXT ) ) {
			
			ob_start();
			
			extract( $this->aLocalData, EXTR_SKIP );
			
			
			include( $sFile );
			
			return ob_get_clean();
			
		} else {
			
			throw new Lithium_Exception( 'View can not be found.' );
			
		}
		
	}
	
	/**
	 * Print file url
	 * 
	 * @param string $sName - file name or path
	 * @param string $sType - file type
	 */
	public function file( $sName, $sType ) {
		echo self::$oRouter->getFileUrl( $sName, $sType );
	}
	
	/**
	 * Return url for given parh
	 * 
	 * @param string $sTarget
	 */
	public function anchor( $sTarget = '' ) {
		
		if ( func_num_args() > 1 ) {
			$aParams = func_get_args();
			$sTarget = call_user_func_array( 'sprintf', $aParams );
		}
		
		return self::$oRouter->getPageUrl( $sTarget );
		
	}
	
	/**
	 * Magically converts view object to string.
	 *
	 * @return  string
	 */
	public function __toString() {
		return $this->render();
	}
	
}

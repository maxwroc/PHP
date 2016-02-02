<?php
class Database {
	
	/**
	 * Array of instances
	 *
	 * @var assoc-array
	 */
	protected static $aInstances = array();
	
	/**
	 * Configuration array
	 *
	 * @var assoc-array
	 */
	protected $aConfig = array();
	
	/**
	 * Instance of Lithium
	 *
	 * @var Lithium
	 */
	protected $oLithium;
	
	protected $oDriver;
	
	protected $oLink;
	
	protected $iQueryCount = 0;
	
	/**
	 * Database constructor
	 *
	 * @param mixed $mConfig 	- config array or name of config
	 * @return void
	 */
	private function __construct( $mConfig ) {
		
		// get Lithium instance
		if ( ! ( $this->oLithium instanceof Lithium ) ) {
			$this->oLithium = Lithium::getInstance();
		}
		
		
		if ( empty( $mConfig ) ) {
			
			// load default db config
			$this->loadConfig( $this->oLithium->getConfig( 'database' ), 'default' );
			
		} elseif ( is_array( $mConfig ) ) {
			
			$this->loadConfig( $mConfig, 'default' );
			
		} elseif ( is_string( $mConfig ) ) {
			
			// load specified db config
			$this->loadConfig( $this->oLithium->getConfig( 'database' ), $mConfig );
			
		}
		
		if ( empty( $this->aConfig ) ) {
			throw new Lithium_Exception_Database( 'database.error_config' );
		}
		
		// get driver name
		$sDriver = $this->getDriverName();
		
		// load driver
		$this->oDriver = new $sDriver( $this->aConfig );
		
		// clean pass field
		$this->aConfig[ 'pass' ] = '';
		
	}
	
	/**
	 * Instance getter
	 *
	 * @param string $sName 	- instance name
	 * @param array $aConfig 	- instance config
	 * @return Database
	 */
	public static function getInstance( $sName = 'default', $aConfig = null ) {
		
		if ( ! isset( self::$aInstances[ $sName ] ) ) {
			
			self::$aInstances[ $sName ] = new Database( $aConfig === null ? $sName : $aConfig );
			
		}
		
		return self::$aInstances[ $sName ];
		
	}
	
	/**
	 * Loads config for given db name
	 *
	 * @param array $aConfig 	- configuration array
	 * @param string $sDbName 	- name of config to extract
	 */
	protected function loadConfig( $aConfig, $sDbName ) {
		
		if ( ! is_array( $aConfig ) ) {
			throw new Lithium_Exception_Database( 'database.error_config' );
		}
		
		if ( ! empty( $aConfig[ $sDbName ] ) ) {
			$this->aConfig = $aConfig[ $sDbName ];
		} else {
			$this->aConfig = $aConfig;
		}
		
		// check if all needed values/keys are set
		$aKeys = array( 'type', 'host', 'user', 'pass', 'database', 'persistent' );
		if ( count( array_diff( $aKeys, array_keys( $this->aConfig ) ) ) ) {
			throw new Lithium_Exception_Database( 'database.error_wrong_config_keys' );
		}
		
	}
	
	/**
	 * Driver name getter
	 *
	 * @return string 	- driver name
	 */
	protected function getDriverName() {
		
		// check if such driver exists
		$sDriverPath = $this->oLithium->findFile( ucfirst( $this->aConfig[ 'type' ] ), 'Driver' );
		if ( $sDriverPath === false ) {
			throw new Lithium_Exception_Database( 'database.error_driver_not_found' );
		}
		
		return 'Driver_' . ucfirst( $this->aConfig[ 'type' ] );
		
	}
	
	
	public function connect() {
		
		if ( ! is_resource( $this->oLink ) AND ! is_object( $this->oLink ) ) {
			
			$this->oLink = $this->oDriver->connect();
			
			if ( ! is_resource( $this->oLink ) AND ! is_object( $this->oLink ) ) {
				throw new Lithium_Exception_Database( 'database.connection', $this->oDriver->getError() );
			}
			
		}
			
	}

	
	public function disconnect() {
		// TODO Disconnect
	}
	
	public function query( $sSql, $aParams = array(), $bReturnResult = true ) {
		
		if ( ! is_resource( $this->oLink ) AND ! is_object( $this->oLink ) ) {
			$this->connect();
		}
		
		if ( ! is_array( $aParams ) ) {
			
			if ( func_num_args() > 2 ) {
				$aParams = array_slice( func_get_args(), 1 );
			} else {
				$aParams = array( $aParams );
			}
			
		}
		
		$this->iQueryCount++;
		
		if ( $this->oDriver->query( $sSql, $aParams ) ) {
			
			if ( $bReturnResult ) {
				return $this->oDriver->getArrayResult();
			} else {
				return true;
			}
			
		} else {
			return false;
		}
		
	}
	
	public function setChunkArgs( $iOffset, $iQuantity ) {
		$this->oDriver->setChunkArgs( $iOffset, $iQuantity );
	}
	
	public function transaction( $bStart = false ) {
		return $this->oDriver->transaction( $bStart );
	}
	
	public function getInsertId() {
		return $this->oDriver->getInsertId();
	}
	
	public function getFieldsInfo( $sTable ) {
		
		if ( ! is_resource( $this->oLink ) AND ! is_object( $this->oLink ) ) {
			$this->connect();
		}
		
		return $this->oDriver->getFieldsInfo( $sTable );
	}
	
	
	public function getError() {
		return $this->oDriver->getError();
	}
	
	public function getQueryCount() {
		return $this->iQueryCount;
	}
	
	public function getQueryHistory() {
		return $this->oDriver->getQueryHistory();
	}
	
}

class Lithium_Exception_Database extends Lithium_Exception {
	
	protected $code = E_DATABASE_ERROR;
	
}
?>
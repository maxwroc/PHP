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
	 * Last query result object
	 * @var Database_QueryResult
	 */
	protected $oLastQueryResult = null;
	
	/**
	 * History of sql queries which were executed
	 * @var array
	 */
	protected $aQueryHistory = array();
	
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
		
		$sDriverName = 'Database_Driver_' . ucfirst( strtolower( $this->aConfig[ 'type' ] ) );
		
		// check if such driver exists
		$sDriverPath = $this->oLithium->findFile( $sDriverName, 'Database_Driver' );
		if ( $sDriverPath === false ) {
			throw new Lithium_Exception_Database( 'database.error_driver_not_found' );
		}
		
		return $sDriverName;
		
	}
	
	
	public function connect() {
		
		if ( ! is_resource( $this->oLink ) AND ! is_object( $this->oLink ) ) {
			
			if ( ! $this->oDriver->connect() ) {
				throw new Lithium_Exception_Database( 'database.connection', $this->oDriver->getError() );
			}
			
		}
			
	}

	
	public function disconnect() {
		// TODO Disconnect
	}
	
	public function query( Database_Query $oQuery ) {
		
		$this->iQueryCount++;
		
		$this->oLastQueryResult = $this->oDriver->query( $oQuery );
		
		$this->aQueryHistory[] = $this->oLastQueryResult->sql();
		
		if ( $this->oLastQueryResult->isError() ) {
			return false;
		} else {
			if ( $oQuery->returnResult() ) {
				return $this->oDriver->getArrayResult( $this->oLastQueryResult )->result();
			} else {
				return true;
			}
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
	
	public function getError() {
		return $this->oDriver->getError();
	}
	
	public function getQueryCount() {
		return $this->iQueryCount;
	}
	
	public function getQueryHistory() {
		return $this->aQueryHistory;
	}
	
	public function createQuery( $iQueryType ) {
		return new Database_Query( $iQueryType );
	}
	
}

class Lithium_Exception_Database extends Lithium_Exception {
	
	protected $code = E_DATABASE_ERROR;
	
}

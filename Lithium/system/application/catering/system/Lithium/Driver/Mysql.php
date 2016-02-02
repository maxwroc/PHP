<?php
class Driver_Mysql extends Core_Driver {
	
	protected $sLastError;
	
	protected $iLastErrorNr = 0;
	
	protected $rResult;
	
	protected $aQueryHistory = array();
	
	public function __construct( & $aConfig ) {
		$this->aConfig = $aConfig;
	}
	
	public function __destruct() {
		is_resource( $this->oLink ) AND mysql_close( $this->oLink );
	}
	
	public function connect() {
		
		// Check if link already exists
		if ( is_resource( $this->oLink ) ) {
			return $this->oLink;
		}
		
		// Import the connect variables
		extract( $this->aConfig );
		
		// Choose connection type/function
		$connect = ( $persistent == 1 ) ? 'mysql_pconnect' : 'mysql_connect';
		
		// Connect to db
		if ( ( $this->oLink = $connect( $host, $user, $pass, true ) ) AND ( mysql_select_db( $database, $this->oLink ) ) ) {
			
			// Clearing password
			$this->aConfig[ 'pass' ] = null;
			
			return $this->oLink;
			
		}
		
		return false;
		
	}
	
	protected function getLimit() {
		
		$sSql = ' LIMIT ';
		
		if ( ! $this->iOffset ) {
			$sSql .= $this->iQuantity;
		} else {
			// if quantity is not set we set max number of results
			if ( ! $this->iQuantity ) {
				$this->iQuantity = '18446744073709551615';
			}
			$sSql .= $this->iOffset . ', ' . $this->iQuantity;
		}
		
		$this->iOffset = 0;
		$this->iQuantity = 0;
		
		return $sSql;
		
	}
	
	public function query( $sSql, $aParams ) {
		
		// escaping illegal characters
		$this->escapeParams( $aParams );
		
		if ( ! is_resource( $this->oLink ) ) {
			throw new Lithium_Exception_Database( 'database.connection', $this->getError() );
		}
		
		$sSql = $this->buildQuery( $sSql, $aParams );
		
		// save query string in history
		$this->aQueryHistory[] = $sSql;
		
		if ( ! $this->rResult = mysql_query( $sSql, $this->oLink ) ) {
			
			if ( ! IN_PRODUCTION ) {
				$this->transaction();
				throw new Lithium_Exception_Database( 'database.query_failure', $sSql, $this->getError() );
			} else {
				self::$oLithium->logError( 'database.query_failure', $sSql, $this->getError() );
			}
			
			return false;
			
		}
		
		return true;
		
	}
	
	protected function buildQuery( $sStatement, $aParams ) {
		
		// check do we have SELECT in query and should we use LIMIT
		if ( stristr( $sStatement, 'select' ) && ( $this->iOffset || $this->iQuantity ) ) {
			$sStatement = rtrim( $sStatement, ';' ) . $this->getLimit() . ';';
		}
		
		if ( empty( $aParams ) ) {
			return $sStatement;
		}
		
		$iCountS = substr_count( $sStatement, '%' );
		$iCountParams = count( $aParams );
		
		// check if there is enough params for statement
		if( $iCountParams != $iCountS ) {
			throw new Lithium_Exception_Database( 'database.incorrect_query_params' );
		}
		
		// add statement as a first param
		array_unshift($aParams, $sStatement);
		
		// put params into statement and return it
		return call_user_func_array( 'sprintf', $aParams );
		
	}
	
	protected function escapeParams( & $aParams ) {
		
		foreach( $aParams as & $mParam ) {
			
			if ( is_string( $mParam ) ) {
				
				$mParam = mysql_escape_string( $mParam );
				$mParam = '\'' . $mParam . '\'';
				
			} else {
				$mParam = (string)$mParam;
			}
			
		}
		
	}
	
	
	public function getArrayResult() {
		
		if ( ! is_resource( $this->rResult ) ) {
			return;
		}
		
		$aResult = array();
		
		while( $aRow = mysql_fetch_array( $this->rResult, MYSQL_ASSOC ) ) {
			$aResult[] = $aRow;
		}
		
		return $aResult;
		
	}
	
	public function getFieldsInfo( $sTable ) {
		
		if ( ! is_resource( $this->oLink ) ) {
			throw new Lithium_Exception_Database( 'database.connection', $this->getError() );
		}
		
		$rResult = mysql_query( 'SELECT * FROM ' . $sTable . ' LIMIT 1;', $this->oLink );
		
		$aInfo = array();
		
		$i = 0;
		while ( $i < mysql_num_fields( $rResult ) ) {
			
		    $oMeta = mysql_fetch_field( $rResult );
		    if ( $oMeta ) {
		    	$aInfo[ $oMeta->name ] = array( 
		    		'type' => $oMeta->type
		    		, 'not_null' => $oMeta->not_null
		    		, 'def' => $oMeta->def
		    	);
		    }
		    $i++;
		}
				
		
		return $aInfo;
		
	}
	
	public function transaction( $bStart = false ) {
		
		// functionality temporary disabled
		return true;
		
		if ( $bStart ) {
			@mysql_query( 'BEGIN TRANSACTION ' );
		} else {
			
			// check if there are some errors
			if ( empty( $this->sLastError ) ) {
				@mysql_query( 'COMMIT TRANSACTION ' );
				return true;
			} else {
				@mysql_query( 'ROLLBACK ' );
				return false;
			}
			
		}
		
	}
	
	public function getInsertId() {
		return mysql_insert_id( $this->oLink );
	}
	
	public function getError() {
		
		if ( is_resource( $this->oLink ) ) {
			$this->iLastErrorNr = mysql_errno( $this->oLink );
			$this->sLastError = mysql_error( $this->oLink );
			return 'MySql [' . $this->iLastErrorNr . ']: ' . $this->sLastError;
		} else {
			return 'MySql: Cannot connect to specified database.';
		}
		
	}
	
	public function getQueryHistory() {
		return $this->aQueryHistory;
	}
	
}
?>
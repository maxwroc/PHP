<?php

/**
 * Database driver for MySQL database
 * 
 * @package Lithium
 * @subpackage Database
 */
class Database_Driver_Mysql extends Database_Driver {
	
	protected $sLastError;
	
	protected $iLastErrorNr = 0;
	
	protected $rResult;
	
	protected $aQueryHistory = array();
	
	public function connect() {
		
		// Check if link already exists
		if ( is_resource( $this->oResource ) ) {
			return true;
		}
		
		// Import the connect variables
		extract( $this->aConfig );
		
		// Clearing password
		$this->aConfig[ 'pass' ] = null;
		
		// Choose connection type/function
		$connect = ( $persistent == 1 ) ? 'mysql_pconnect' : 'mysql_connect';
		
		// Connect to db
		if ( ( $this->oResource = @$connect( $host, $user, $pass, true ) ) && ( mysql_select_db( $database, $this->oResource ) ) ) {
			return true;
		}
		
		return false;
	}
	
	public function close() {
		is_resource( $this->oResource ) && mysql_close( $this->oResource );
	}
	
	protected function executeQuery( Database_QueryResult $oQResult ) {
		
		if ( ! $oQResult->rResource = mysql_query( $oQResult->sql(), $this->oResource ) ) {
			
			if ( ! IN_PRODUCTION ) {
				throw new Lithium_Exception_Database( 'database.query_failure', $oQResult->sql(), $this->getError() );
			} else {
				$oQResult->error( 'database.query_failure' );
				self::$oLithium->logError( 'database.query_failure', $sSql, $this->getError() );
			}
			
		}
		
		return $oQResult;
		
	}
	
	protected function escapeParams( & $aParams ) {
		
		foreach( $aParams as & $mParam ) {
			
			if ( is_string( $mParam ) ) {
				
				$this->escapeString( $mParam );
				
			} else {
				$mParam = (string)$mParam;
			}
			
		}
		
	}
	
	
	public function getArrayResult( Database_QueryResult $oQResult ) {
		
		if ( $oQResult->isError() ) {
			return $oQResult;
		}
		
		if ( ! is_resource( $oQResult->rResource ) ) {
			return $oQResult->error( 'database.incorrect_resource' ); // TODO uzupelnic string table
		}
		
		$aResult = array();
		
		while ( $aRow = mysql_fetch_array( $oQResult->rResource, MYSQL_ASSOC ) ) {
			$aResult[] = $aRow;
		}
		
		return $oQResult->result( $aResult );
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
		return mysql_insert_id( $this->oResource );
	}
	
	public function getError() {
		
		if ( is_resource( $this->oResource ) ) {
			$this->iLastErrorNr = mysql_errno( $this->oResource );
			$this->sLastError = mysql_error( $this->oResource );
			return 'MySql [' . $this->iLastErrorNr . ']: ' . $this->sLastError;
		} else {
			return 'MySql: Cannot connect to specified database.';
		}
		
	}
	
	/**
	 * Add special markaps around object names
	 * 
	 * @param string $sObjName
	 * @return string
	 */
	protected function markDatabaseObject( $sObjName ) {
		$aObjects = explode( '.', $sObjName );
		foreach ( $aObjects as $iKey => $sObject ) {
			$aObjects[ $iKey ] = '`' . $sObject . '`';
		}
		return implode( '.', $aObjects );
	}
	
	/**
	 * Escapes string and adds quotes to given string
	 * 
	 * @param string $sString
	 * @return string
	 */
	protected function escapeString( $sString ) {
		return sprintf( '\'%s\'', mysql_escape_string( $sString ) );
	}
	
	/**
	 * Builds and returns the sql select statement
	 * 
	 * @param Database_Query $oQuery
	 * @return string
	 */
	protected function buildSqlSelect( Database_Query $oQuery ) {
		$sSql = parent::buildSqlSelect( $oQuery );
		
		if ( $oQuery->limit() > 0 ) {
			$sSql .= ' LIMIT ';
			if ( $oQuery->offset() > 0 ) {
				$sSql .= $oQuery->offset() . ', ';
			}
			$sSql .= $oQuery->limit();
		}
		return $sSql;
	}
	
}

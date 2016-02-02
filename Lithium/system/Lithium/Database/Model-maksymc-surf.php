<?php

/**
 * Database model class
 * 
 * @package Lithium
 * @subpackage Database
 */
abstract class Database_Model extends Core_Model {
	
	protected $sTable = '';
	protected $sPrimaryKey = '';
	
	protected $aHasMany = array();
	protected $aHasOne = array();
	
	protected $sConfigName = 'default';
	
	protected $aTableInfo = array();
	
	protected $oDB;
	
	/**
	 * @var Module_Sort
	 */
	protected $oSorter;
	
	protected $aQuery = array();
	protected $aData = array();
	protected $iDataActiveElement = 0;
	protected $aDataObjects = array();
	protected $aFiledsType = array();
	protected $sError;
	
	/**
	 * Query parameters
	 * @var array
	 */
	protected $aQueryParams = array();
	
	public function __construct( $iId = 0 ) {
		
		if ( empty( $this->aTableInfo ) ) {
			throw new Lithium_Exception( 'database.columns_array_not_set' );
		}
		
		// Get database object
		if ( ! is_object( $this->oDB ) ) {
			$this->oDB = self::$oLithium->getDatabase( $this->sConfigName );
		}
		
		// sTable and sPrimaryKey must be set
		if ( empty( $this->sTable ) || empty( $this->sPrimaryKey ) ) {
			throw new Lithium_Exception( 'core.model_vars_not_set', get_class( $this ) );
		}
		
		$iId = (int)$iId;
		
		
		if ( $iId ) {
			$this->aData[0][ $this->sPrimaryKey ] = $iId;
			$this->where( $this->sPrimaryKey, $iId );
		}
		
	}
	
	/**
	 * Setter for Sort Module
	 * 
	 * @param Sort_Module $oSortModule
	 */
	public function setSorter( $oSorter ) {
		$this->oSorter = $oSorter;
	}
	
	public function __get( $sName ) {
		
		if ( isset( $this->aData[1] ) ) {
			$this->sError = 'user.model_many_rows';
			return false;
		}
		
		if ( empty( $this->aData ) ) $this->getAll();
		
		if ( isset( $this->aData[0] ) && isset( $this->aData[0][ $sName ] ) ) {
			return $this->aData[0][ $sName ];
		}
		
	}
	
	
	public function get( $sName, $iRowNr = 0 ) {
		
		if ( ! isset( $this->aData[ $iRowNr ] ) ) {
			
			if ( ! isset( $this->aData[ $iRowNr ] ) ) {
				$this->sError = 'core.model_row_not_exists';
				return false;
			}
			
		}
		
		if ( isset( $this->aData[ $iRowNr ] ) && isset( $this->aData[ $iRowNr ][ $sName ] ) ) {

			if ( in_array( $sName, array_keys( $this->aHasOne ) ) ) {
				
					if ( isset( $this->aDataObjects[ $sName ] ) AND ( $this->aDataObjects[ $sName ] instanceof Core_Model ) ) {
						return $this->aDataObjects[ $sName ];
					}
				
					$sClassName = 'Model_' . $this->aHasOne[ $sName ];
					$this->aDataObjects[ $sName ] = new $sClassName( $this->aData[0][ $sName ] );
					$this->aDataObjects[ $sName ]->getRow();
					return $this->aDataObjects[ $sName ];
				
			}
			
			return $this->aData[ $iRowNr ][ $sName ];
			
		}
		
	}
	
	/**
	 * Magic setter for table fields/columns
	 * 
	 * @param string $sName
	 * @param value $mValue
	 * @throws Lithium_Exception
	 */
	public function __set( $sName, $mValue ) {
		
		// Chceck if name is correct
		if ( ! isset( $this->aTableInfo[ $sName ] ) ) {
			throw new Lithium_Exception( 'database.model_incorrect_column_name', $this->sTable, $sName );
		}
		
		$this->aData[ $this->iDataActiveElement ][ $sName ] = $mValue;
	}
	
	/**
	 * Executes query and returns its result
	 * 
	 * @param mixed $mFields
	 * 			- string : column name thet will be returned
	 * 			- array : array values are column names which will be returned 
	 * 			- assoc-array : array keys are column names and their values are aliases
	 * @return array
	 */
	public function getAll( $aFields = array() ) {
		
		if ( ! is_array( $aFields ) ) {
			$aFields = func_get_args();
		}
		
		$oQuery = $this->createQuery( Database_Query::T_SELECT, $aFields );
		
		$this->aData = $this->oDB->query( $oQuery );
		
		$aCleanResult = array();
		if ( count( $aFields ) == 1 ) {
			foreach ( $this->aData as $aRow ) {
				$aCleanResult[] = reset( $aRow );
			}
		} else {
			$aCleanResult = &$this->aData;
		}
		
		return $aCleanResult;
	}
	
	/**
	 * Executes query and returns its result (returns only first row)
	 * 
	 * @param mixed $mFields
	 * 			- string : column name thet will be returned
	 * 			- array : array values are column names which will be returned 
	 * 			- assoc-array : array keys are column names and their values are aliases
	 * @return array
	 */
	public function getRow( $mFields = array() ) {
		
		if ( ! is_array( $mFields ) ) {
			$mFields = func_get_args();
		}
		
		$oQuery = $this->createQuery( Database_Query::T_SELECT, $mFields );
		
		$this->aData = $this->oDB->query( $oQuery );
		
		if ( ! isset( $this->aData[ $this->iDataActiveElement ] ) ) {
			return false;
		}
		
		// Return field value only in case when single field was specified
		if ( ( count( $mFields ) == 1 ) && isset( $this->aData[0][ $mFields[0] ] ) ) {
			return $this->aData[ $this->iDataActiveElement ][ $mFields[0] ];
		}
		
		return $this->aData[ $this->iDataActiveElement ];
	}
	
	/**
	 * Moves element cursor to next position
	 * 
	 * @return bool
	 */
	public function next() {
		$this->iDataActiveElement++;
		return isset( $this->aData[ $this->iDataActiveElement ] );
	}
	
	/**
	 * Moves element cursor to previous position
	 * 
	 * @return bool
	 */
	public function prev() {
		$this->iDataActiveElement--;
		return isset( $this->aData[ $this->iDataActiveElement ] );
	}
	/**
	 * Getter or setter for current data element position
	 * 
	 * @param int $iNewPosition
	 * @return int;
	 */
	public function position( $iNewPosition = null ) {
		if ( is_null( $iNewPosition ) ) return $this->iDataActiveElement;
		$this->iDataActiveElement = $iNewPosition;
	}
	
	public function getInsertId() {
		return $this->oDB->getInsertId();
	}
	
	public function getRowsCount() {
		$aResult = $this->oDB->query( 'SELECT count(*) as count FROM ' . $this->sTable );
		return (int)$aResult[0]['count'];
	}
	
	/**
	 * Add contition AND to query
	 * 
	 * @param string $sColumn
	 * @param mixed $mValue
	 * @param string $sOperator
	 * @return Core_Model - self instance
	 */
	public function where( $sColumn, $mValue, $sOperator = '=' ) {
		
		$this->aQueryParams[] = array( 
			Database_Query::C_AND, 
			array( $sColumn, Database_Query::parseParam( $sOperator ), $mValue ) 
		);
		
		return $this;
	}
	
	/**
	 * Add condition OR to query
	 * 
	 * @param string $sColumn
	 * @param mixed $mValue
	 * @param string $sOperator
	 * @return Core_Model - self instance
	 */
	public function orwhere( $sColumn, $mValue, $sOperator = '=' ) {
		
		$this->aQueryParams[] = array( 
			Database_Query::C_OR, 
			array( $sColumn, Database_Query::parseParam( $sOperator ), $mValue ) 
		);
		
		return $this;
	}
	
	/**
	 * AND contition & IN operator
	 * 
	 * @param string $sColumn
	 * @param array $aValue
	 * @param bool $bNot - determinate when condition should be negative
	 * @return Core_Model - self instance
	 */
	public function in( $sColumn, $aValue, $bNot = false ) {
		
		$this->aQuery[] = array( 'IN', $sColumn , $aValue );
		
		$this->aQueryParams[] = array( 
			Database_Query::C_AND, 
			array( $sColumn, $bNot ? Database_Query::O_NOT_IN : Database_Query::O_IN, $aValue ) 
		);
		
		return $this;
	}
	
	/**
	 * AND contition & BETWEEN operator
	 * 
	 * @param string $sColumn
	 * @param mixed $mFromValue
	 * @param mixed $mToValue
	 * @param bool $bNot - determinate when condition should be negative
	 * @return Core_Model - self instance
	 */
	public function between( $sColumn, $mFromValue, $mToValue, $bNot = false ) {
		
		$this->aQueryParams[] = array( 
			Database_Query::C_AND, 
			array( 
				$sColumn, 
				$bNot ? Database_Query::O_NOT_BETWEEN : Database_Query::O_BETWEEN, 
				array( $mFromValue, $mToValue ) 
			) 
		);
		
		return $this;
	}
	
	/**
	 * Add ORDER BY to query
	 * 
	 * @param string $sColumn
	 * @param mixed $mType
	 * @return Core_Model - self instance
	 */
	public function orderby( $sColumn, $mType = 0 ) {
		
		$this->aQueryParams[] = array( 
			Database_Query::C_ORDERBY, 
			array( 
				$sColumn, 
				Database_Query::parseSort( $mType )
			) 
		);
		
		return $this;
	}
	
	public function delete() {
		
		if ( count( $this->aData ) != 1 ) {
			return false;
		}
		
		if ( $this->oDB->query( 'DELETE FROM '. $this->sTable . ' WHERE  ' . $this->sPrimaryKey . '=%s;', array( $this->aData[0][ $this->sPrimaryKey ] ), false ) ) {
			return true;
		}
		
		return false;
		
	}
	
	/**
	 * Resets model data
	 */
	public function reset() {
		$this->aData = array();
		$this->sError = null;
	}
	
	public function save( $bReturnInsertId = true ) {

		// Check if we should use update operation
		if ( isset( $this->aData[ $this->iDataActiveElement ][ $this->sPrimaryKey ] ) ) {
			return $this->update();
		}
		
		return $this->insert();
		
//		printf( "<pre>%s (%d):\n%s</pre>", __FILE__, __LINE__, print_r( $this->aData,1 ) );
//		return false;
//		
//		// TODO rest
//		$this->prepareDataForSave();
//		
//		foreach( $this->aData as $aRecord ) {
//			
//			$aObjToSave 	= array();
//			$aColumnNames 	= array();
//			$aValues 		= array();
//			
//			// check if record contains model objects
//			foreach ( $aRecord as $sColumnName => $mValue ) {
//				
//				if ( $mValue instanceof Core_Model ) {
//					$aObjToSave[] = $mValue;
//				} else {
//					$aColumnNames[] = $sColumnName;
//					$aValues[] = $mValue;
//				}
//				
//			}
//			
//			$sColumnNames = '`' . implode( '`,`', $aColumnNames ) . '`';
//			
//			$sSCount = implode( ',', array_fill( 0, count( $aValues ), '%s' ) );
//			
//			if ( ! $this->oDB->query( 'INSERT INTO ' . $this->sTable . ' (' . $sColumnNames . ') VALUES (' . $sSCount . ');', $aValues, false ) ) {
//				
//				$this->sError = $this->oDB->getError();
//				return false;
//			}
//			
//			// save remaining models
//			$iSavedItemId = $this->oDB->getInsertId();
//			foreach ( $aObjToSave as & $oModel ) {
//				
//				$sClassName = str_replace( 'Model_', '', get_class( $oModel ) );
//				if ( ! isset( $this->aHasMany[ $sClassName ] ) ) {
//					throw new Lithium_Exception( 'core.model_assigned_incorrect_model_object', $sClassName, __CLASS__ );
//				}
//				
//				// get foregin key
//				$sForeginKey = $this->aHasMany[ $sClassName ];
//				
//				$oModel->$sForeginKey = $iSavedItemId;
//				
//				$oModel->save();
//				
//			}
//			
//			unset( $aObjToSave );
//			
//		} // foreach
//		
//		$mReturn = true;
//		if ( $bReturnInsertId ) {
//			$mReturn = $this->oDB->getInsertId();
//		}
//		return $mReturn;
		
	} // func save
	
	/**
	 * Updates active row in database
	 * 
	 * @param array $aFields - name of fields which should be updated
	 * @return bool
	 */
	public function update( $aFields = array() ) {
		
		if ( ! is_array( $aFields ) ) {
			$aFields = array( $aFields );
		}
		
		// Clean params array - it should contain only primary key
		$this->aQueryParams = array();
		$this->aQueryParams[] = array( 
			Database_Query::C_AND, 
			array( 
				$this->sPrimaryKey, 
				Database_Query::parseParam( '=' ), 
				$this->aData[ $this->iDataActiveElement ][ $this->sPrimaryKey ] 
			) 
		);
		
		$aFieldValues = array();
		if ( empty( $aFields ) ) { // Take all fields except primary key
			$aFieldValues = $this->aData[ $this->iDataActiveElement ];
			unset( $aFieldValues[ $this->sPrimaryKey ] );
		} else { // Take only specific fields
			foreach ( $aFields as $sName ) {
				if ( isset( $this->aData[ $this->iDataActiveElement ][ $sName ] ) ) {
					$aFieldValues[ $sName ] = $this->aData[ $this->iDataActiveElement ][ $sName ];
				}
			}
		}
		
		$oQuery = $this->createQuery( Database_Query::T_UPDATE, $aFieldValues );
		return $this->oDB->query( $oQuery );
	} // func update
	
	/**
	 * Inserts model values into database table
	 * 
	 * @return bool
	 */
	public function insert() {
		
		if ( isset( $this->aData[0][ $this->sPrimaryKey ] ) ) {
			$this->raiseWarrning( 'Primary key should not be set for insert operation' );
			unset( $this->aData[0][ $this->sPrimaryKey ] );
		}
		
		// Take fields (column names) that we want to save from the first row
		$aFieldsToSave = array_keys( $this->aData[0] );
		
		foreach ( $this->aData as $iKey => $aRow ) {
			
			if ( isset( $aRow[ $this->sPrimaryKey ] ) ) {
				$this->raiseWarrning( 'Primary key should not be set for insert operation' );
				unset( $this->aData[ $iKey ][ $this->sPrimaryKey ] );
			}
			// Check do rest of rows contains same columns
			if ( count( array_diff( $aFieldsToSave, array_keys( $aRow ) ) ) > 0 ) {
				throw new Lithium_Database_Exception( 'database.model_insert_columns_inconsistency' );
			}
			
			// Prepare params (row to save)
			$iCount = count( $this->aQueryParams );
			foreach ( $aFieldsToSave as $sColumn ) {
				$this->aQueryParams[ $iCount ][ $sColumn ] = $aRow[ $sColumn ];
			}
		}
		
		$oQuery = $this->createQuery( Database_Query::T_INSERT, $aFieldsToSave, false );
		return $this->oDB->query( $oQuery );
	}
	
	/**
	 * Creates and returns Query object
	 * 
	 * @param int $iQueryType
	 * @param array $aFields
	 * @return Database_Query
	 */
	public function createQuery( $iQueryType, $aFields, $bReturnResult = true ) {
		
		// TODO obsluga pozostalych typów zapytan
		return $this->oDB->createQuery( $iQueryType )
			->tableInfo( $this->aTableInfo )	// Database table structure
			->fields( $aFields )				// Fields that should be returned
			->table( $this->sTable )			// Query subject table
			->params( $this->aQueryParams )		// Query parameters
			->returnResult( $bReturnResult );	// Do we want to return result
		
	}
	
	public function transaction( $bStart = false ) {
		return $this->oDB->transaction( $bStart );
	}
	
}

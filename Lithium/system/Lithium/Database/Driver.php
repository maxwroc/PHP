<?php

/**
 * Base database driver class
 * 
 * @package Lithium
 * @subpackage Database
 */
abstract class Database_Driver {
	
	/**
	 * Connection configuration
	 * @var array
	 */
	protected $aConfig;
	
	/**
	 * Resource pointing to current DB connection
	 * @var resource
	 */
	protected $oResource;
	
	/**
	 * Lithium instance
	 * 
	 * @var Lithium
	 */
	protected static $oLithium;
	
	/**
	 * Driver constructor
	 * 
	 * @param array $aConfig
	 */
	public function __construct( $aConfig ) {
		$this->aConfig = $aConfig;
	}
	
	/**
	 * Driver destructor
	 * 
	 * Closes connection with database
	 */
	public function __destruct() {
		$this->close();
	}
	
	/**
	 * Setter for Lithium instance
	 * 
	 * @param Lithium $oLithium
	 */
	public static function setLithium( $oLithium ) {
		self::$oLithium = $oLithium;
	}
	
	/**
	 * Connects to database
	 * 
	 * @return bool
	 */
	public abstract function connect();
	
	/**
	 * Closes database connection
	 */
	public abstract function close();
	
	/**
	 * Extracts array query result and assign it back to given Database_QueryResult object
	 * 
	 * @param Database_QueryResult $oQResult
	 * @return Database_QueryResult
	 */
	public abstract function getArrayResult( Database_QueryResult $oQResult );
	
	/**
	 * Returns error message if such occoured
	 * 
	 * @return string
	 */
	public abstract function getError();
	
	/**
	 * Add special markaps around object names
	 * 
	 * @param string $sObjName
	 * @return string
	 */
	protected abstract function markDatabaseObject( $sObjName );
	
	/**
	 * Escapes string and adds quotes to given string
	 * 
	 * @param string $sString
	 * @return string
	 */
	protected abstract function escapeString( $sString );
	
	/**
	 * Executes given query
	 * 
	 * @param Database_Query $oQuery
	 */
	public function query( Database_Query $oQuery ) {
		
		if ( ! is_resource( $this->oResource ) && ! $this->connect() ) {
			throw new Lithium_Exception_Database( 'database.connection', $this->getError() );
		}
		
		$oQResult = new Database_QueryResult( $this->buildSqlCommand( $oQuery ) );
		
		return $this->executeQuery( $oQResult );
	}
	
	/**
	 * Build SQL command
	 * 
	 * @param Database_Query $oQuery
	 * @return string
	 */
	public function buildSqlCommand( Database_Query $oQuery ) {
		
		$sSql = '';
		switch ( $oQuery->getType() ) {
			case Database_Query::T_SELECT :
				$sSql = $this->buildSqlSelect( $oQuery );
				break;
			case Database_Query::T_INSERT :
				$sSql = $this->buildSqlInsert( $oQuery );
				break;
			case Database_Query::T_UPDATE :
				$sSql = $this->buildSqlUpdate( $oQuery );
				break;
			case Database_Query::T_DELETE :
				$sSql = $this->buildSqlDelete( $oQuery );
				break;
			case Database_Query::T_CALL :
				$sSql = $this->buildSqlCall( $oQuery );
				break;
			case Database_Query::T_CUSTOM :
				$sSql = $this->buildSqlCustomQuery( $oQuery );
				break;
			default :
				throw new Lithium_Exception( 'database.uknown_sql_command_type' );
		}
		
		return $sSql;
	}
	
	/**
	 * Builds and returns the sql select statement
	 * 
	 * @param Database_Query $oQuery
	 * @return string
	 */
	protected function buildSqlSelect( Database_Query $oQuery ) {
		
		return sprintf( 
			'SELECT %s FROM %s%s',
			$this->buildFieldListString( $oQuery ), 
			$this->formatTableName( $oQuery ), 
			$this->buildWhereStatement( $oQuery )
		);
		
	}
	
	protected function buildSqlInsert( Database_Query $oQuery ) {
		$aFieldColumns = $oQuery->fields();
		if ( empty( $aFieldColumns ) ) {
			throw new Lithium_Exception_Database( 'database.insert_columns_not_set' );
		}
		
		// Parse fields
		foreach ( $aFieldColumns as $iKey => $aField ) {
			$aFieldColumns[ $iKey ] = $aField[0];
		}
		
		$aValues = array();
		foreach ( $oQuery->params() as $aRow ) {
			
			// Check do rest of rows contains same columns
			if ( count( array_diff( $aFieldColumns, array_keys( $aRow ) ) ) > 0 ) {
				throw new Lithium_Exception_Database( 'database.model_insert_columns_inconsistency' );
			}
			
			// Format values
			foreach ( $aFieldColumns as $sColumn ) {
				$aRow[ $sColumn ] = $this->formatValue( $oQuery, $sColumn, $aRow[ $sColumn ] );
			}
			
			$aValues[] = sprintf( '( %s )', implode( ', ', $aRow ) );
		}
		
		// Prepare columns
		foreach ( $aFieldColumns as $iKey => $sColumn ) {
			$aFieldColumns[ $iKey ] = $this->markDatabaseObject( $sColumn );
		}
		
		$sTableName = $oQuery->table();
		$sTableName = $sTableName[0];
		return sprintf( 'INSERT INTO %s ( %s ) VALUES %s', 
			$this->markDatabaseObject( $sTableName ),
			implode( ', ', $aFieldColumns ),
			implode( ', ', $aValues )
		);
	}
	
	protected function buildSqlUpdate( Database_Query $oQuery ) {
		
		$aFields = $oQuery->fields();
		if ( empty( $aFields ) ) {
			throw new Lithium_Exception_Database( 'database.update_fields_not_set' );
		}
		
		$aSetFieldValuePair = array();
		foreach ( $aFields as $aField ) {
			list( $sName, $mValue ) = $aField;
			$aSetFieldValuePair[] = sprintf( '%s=%s',
				$this->markDatabaseObject( $sName ),
				$this->formatValue( $oQuery, $sName, $mValue )
			);
		}
		
		return sprintf( 'UPDATE %s SET %s%s',
			$this->formatTableName( $oQuery ),
			implode( ', ', $aSetFieldValuePair ),
			$this->buildWhereStatement( $oQuery ) 
		);
	}
	
	protected function buildSqlDelete( Database_Query $oQuery ) {
		// TODO konstruktor delete
	}
	
	protected function buildSqlCall( Database_Query $oQuery ) {
		// TODO konstruktor call
	}
	
	protected function formatTableName( Database_Query $oQuery ) {
		$aTable = $oQuery->table();
		$sTable = $this->markDatabaseObject( $aTable[0] );
		if ( ! is_null( $aTable[1] ) ) { // alias
			$sTable .= ' AS ' . $this->escapeString( $aTable[1] );
		}
		
		return $sTable;
	}
	
	protected function formatValue( Database_Query $oQuery, $sColumn, $mValue ) {
		
		$aTableInfo = $oQuery->tableInfo();
		
		if ( empty( $aTableInfo[ $sColumn ] ) ) {
			throw new Lithium_Exception_Database( 'database.missing_column_info', $sColumn );
		}
		
		switch ( strtolower( $aTableInfo[ $sColumn ] ) ) {
			case 'int' :
			case 'integer' :
				return $this->formatValueInt( $mValue );
				break;
			case 'text' :
			case 'varchar' :
				return $this->formatValueVarchar( $mValue );
			case 'decimal' :
				return $this->formatValueDecimal( $mValue );
			case 'date' :
				return $this->formatValueVarchar( $mValue );
			default:
				throw new Lithium_Exception_Database( 'database.model_unknown_column_type', $aTableInfo[ $sColumn ] );
		}
	}
	
	/**
	 * Converts given value to integer
	 * 
	 * @param mixed $mValue
	 */
	protected function formatValueInt( $mValue ) {
		return (int)$mValue;
	}
	
	/**
	 * Converts given value to database safe string
	 * 
	 * @param mixed $mValue
	 */
	protected function formatValueVarchar( $mValue ) {
		return $this->escapeString( (string)$mValue );
	}
	
	/**
	 * Converts value to datbase decimal format
	 * 
	 * @param mixed $mValue
	 */
	protected function formatValueDecimal( $mValue ) {
		return str_replace( ',', '.', (float)$mValue );
	}
	
	/**
	 * Builds custom sql query
	 * 
	 * Marks database objects and escapes strings (passed in params array)
	 * 
	 * @param Database_Query $oQuery
	 * @return string
	 */
	protected function buildSqlCustomQuery( Database_Query $oQuery ) {
		
		$sStatement = $oQuery->sql();
		$aParams = $oQuery->params();
		
		if ( empty( $aParams ) ) {
			return $sStatement;
		}
		
		$iFoundedParams = preg_match_all( '!%([osdf])!', $sStatement, $aMatches );
		
		// Check if there is enough params for statement
		if( count( $aParams ) != $iFoundedParams ) {
			throw new Lithium_Exception_Database( 'database.incorrect_query_params' );
		}
		
		// Additional protection if matching fails
		if ( empty( $aMatches[1] ) ) {
			throw new Lithium_Exception( 'database.matching_variables_failed', $sStatement );
		}
		
		foreach ( $aMatches[1] as $iIndex => $sPType ) {
			switch ( $sPType ) {
				case 'o' :
					$aParams[ $iIndex ] = $this->markDatabaseObject( $aParams[ $iIndex ] );
					break;
				case 'd' :
					$aParams[ $iIndex ] = (int)$aParams[ $iIndex ];
					break;
				case 'f' :
					$aParams[ $iIndex ] = (float)$aParams[ $iIndex ];
					break;
				case 's' :
					$aParams[ $iIndex ] = $this->escapeString( $aParams[ $iIndex ] );
					break;
			}
		}
		
		// Replace mark tak will not be recognized by sprintf function
		$sStatement = str_replace( '%o', '%s', $sStatement );
		
		// Add statement as a first param
		array_unshift($aParams, $sStatement);
		
		// Put params into statement and return it
		return call_user_func_array( 'sprintf', $aParams );
	}
	
	/**
	 * Builds and returns string with list of field names
	 * 
	 * @param Database_Query $oQuery
	 * @return string
	 */
	protected function buildFieldListString( Database_Query $oQuery ) {
		
		$aFields = $oQuery->fields();
		if ( empty( $aFields ) ) {
			return '*';
		}
		
		$aFields = array();
		foreach ( $oQuery->fields() as $aField ) {
			
			// if array is not assoc we treat its values as field names
			if ( is_null( $aField[1] ) ) {
				$aFields[] = $this->markDatabaseObject( $aField[0] );
			} else {
				$aFields[] = sprintf( 
					'%s AS %s', 
					$this->markDatabaseObject( $aField[0] ),
					$this->escapeString( $aField[1] )
				);
			}
			
		} // foreach
		
		return implode( ', ', $aFields );
	}
	
	/**
	 * Builds and returns
	 * @param Database_Query $oQuery
	 * @throws Lithium_Exception
	 */
	protected function buildWhereStatement( Database_Query $oQuery ) {
		
		$sSql = '';    
    $bAddWhere = false;
    $sGlue = '';
    
		foreach ( $oQuery->params() as $aParam ) {
			
			if ( count( $aParam ) != 2 ) {
				throw new Lithium_Exception( 'database.incorrect_query_params' );
			}
			list( $iType, $aParams ) = $aParam;
			
			switch ( $iType ) {
				case Database_Query::C_AND :
					$sGlue = 'AND ';
					$sOperator = $this->getSqlConditionStatement( $aParams );
          $bAddWhere = true;
					break;
				case Database_Query::C_OR :
					$sGlue = 'OR ';
					$sOperator = $this->getSqlConditionStatement( $aParams );
          $bAddWhere = true;
					break;
				case Database_Query::C_ORDERBY :
					$sOperator = sprintf( 
						'ORDER BY %s %s', 
						$aParams[0], 
						Database_Query::S_DESC == $aParams[1] ? 'ASC' : 'DESC'
					);
					break;
				default :
					throw new Lithium_Exception( 'database.unknown_condition_type', $iType );
			}
			
			if ( ! empty( $sSql ) ) {
				$sSql .= ' ' . $sGlue;
			}
			$sSql .= $sOperator;
		}
		
		if ( $bAddWhere && ! empty( $sSql ) ) {
			$sSql = ' WHERE ' . $sSql;
		}
		
		return $sSql;
	}
	
	/**
	 * Builds and returns condition statement
	 * 
	 * @param array $aData
	 * @return string
	 * @throws Lithium_Exception
	 */
	protected function getSqlConditionStatement( $aData ) {
		$sOperator = '';
    $aParams = [];
		
		$sColumn = $aData[0];
		switch ( $aData[1] ) {
			case Database_Query::O_EQUAL :
				$sOperator = '%s=%s';
				$aParams = array( $aData[2] );
				break;
			case Database_Query::O_NOT_EQUAL :
				$sOperator = '%s!=%s';
				$aParams = array( $aData[2] );
				break;
			case Database_Query::O_GREATER :
				$sOperator = '%s<%s';
				$aParams = array( $aData[2] );
				break;
			case Database_Query::O_GREATER_EQ :
				$sOperator = '%s<=%s';
				$aParams = array( $aData[2] );
				break;
			case Database_Query::O_LESS :
				$sOperator = '%s>%s';
				$aParams = array( $aData[2] );
				break;
			case Database_Query::O_LESS_EQ :
				$sOperator = '%s>=%s';
				$aParams = array( $aData[2] );
				break;
			case Database_Query::O_IN :
				$aParams = is_array( $aData[2] )? $aData[2] : array( $aData[2] );
				$sOperator = '%s IN (' . trim( str_repeat( '%s,', count( $aParams ) ), ',' ) . ')';
				break;
			case Database_Query::O_NOT_IN :
				$aParams = is_array( $aData[2] )? $aData[2] : array( $aData[2] );
				$sOperator = '%s NOT IN (' . trim( str_repeat( '%s,', count( $aParams ) ), ',' ) . ')';
				break;
			case Database_Query::O_BETWEEN :
				$sOperator = '%s BETWEEN %s AND %s';
				$aParams = $aData[2];
				break;
			case Database_Query::O_NOT_BETWEEN :
				$sOperator = '%s NOT BETWEEN %s AND %s';
				$aParams = $aData[2];
				break;
      case Database_Query::O_IS :
        $sOperator = '%s IS %s';
				$aParams = array( $aData[2] );
        break;
			default :
				throw new Lithium_Exception( 'database.incorrect_operator' );
		}
		
		foreach ( $aParams as $iKey => $mParam ) {
			if ( is_string( $mParam ) ) {
				$aParams[ $iKey ] = $this->escapeString( $mParam );
			}
      elseif ( is_null( $mParam ) ) {
        $aParams[ $iKey ] = 'NULL';
      }
		}
		
		array_unshift( $aParams, $sOperator, $this->markDatabaseObject( $sColumn ) );
		
		return call_user_func_array( 'sprintf', $aParams );
	} // func
	
} // class

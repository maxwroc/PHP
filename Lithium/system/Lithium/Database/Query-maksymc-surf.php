<?php 

/**
 * Query container class
 * 
 * Used to pass all data, needed to execute query, to database driver
 * 
 * @author max
 * @package Lithium
 * @subpackage Database
 */
class Database_Query {
	
	// Query types
	const T_CUSTOM = 0;
	const T_SELECT = 1;
	const T_INSERT = 2;
	const T_UPDATE = 3;
	const T_DELETE = 4;
	const T_CALL = 5;
	
	// Conditions
	const C_AND = 1;
	const C_OR = 2;
	const C_ORDERBY = 3;
	
	// Sorting orders
	const S_ASC = 0;
	const S_DESC = 1;
	
	// Operators
	const O_EQUAL = 1;
	const O_NOT_EQUAL = 2;
	const O_GREATER = 3;
	const O_GREATER_EQ = 4;
	const O_LESS = 5;
	const O_LESS_EQ = 6;
	const O_IN = 7;
	const O_NOT_IN = 8;
	const O_BETWEEN = 9;
	const O_NOT_BETWEEN = 10;
	
	// Custom query param types
	const CQ_DB_OBJECT = 0;
	const CQ_NUM_VALUE = 1;
	const CQ_STRING = 2;
	
	/**
	 * Query type
	 * @var int
	 */
	protected $iType;
	
	/**
	 * Table name
	 * @var string
	 */
	protected $sTable = null;
	
	/**
	 * Stored procedure name
	 * @var string
	 */
	protected $sStoredProc = null;
	
	/**
	 * Array of fields that should be return in the result (and their aliases)
	 * @var array
	 */
	protected $aFields = array();
	
	/**
	 * Other query parameters
	 * @var array
	 */
	protected $aParams = array();
	
	/**
	 * Custom SQL caommand
	 * @var string
	 */
	protected $sSqlCommand = null;
	
	/**
	 * Limit of rows that will be returned
	 * @var int
	 */
	protected $iLimit = 0;
	
	/**
	 * Offset of result rows that should be returned
	 * @var int
	 */
	protected $iOffset = 0;
	
	/**
	 * Determinates when return result after executing query
	 * @var bool
	 */
	protected $bReturnResult = false;
	
	/**
	 * Database table columns information
	 * 
	 * @var array
	 */
	protected $aDatabaseTableColumns;
	
	/**
	 * Query constructor
	 * @param int $iQueryType
	 */
	public function __construct( $iQueryType ) {
		$this->iType = $iQueryType;
	}
	
	/**
	 * SGets or sets table name and its alias that should be used
	 * 
	 * @param string $sName
	 * @param string $sAlias
	 * @return Database_Query - self instance
	 */
	public function table( $sName = null, $sAlias = null ) {
		if ( is_null( $sName ) ) return $this->sTable;
		$this->sTable = array( $sName, $sAlias );
		return $this;
	}
	
	/**
	 * Sets field and its alias that will be returned in the result
	 * 
	 * @param string $sName
	 * @param string $sAlias
	 * @return Database_Query - self instance
	 */
	public function field( $sName, $sAlias = null ) {
		$this->aFields[] = array( $sName, $sAlias );
		return $this;
	}
	
	/**
	 * Gets or sets fields which will be returned
	 * 
	 * If assoc array passed keys will be treated as fields and values as their aliases 
	 * 
	 * @param array $aFields
	 * @return Database_Query - self instance
	 */
	public function fields( $aFields = null ) {
		if ( is_null( $aFields ) ) return $this->aFields;
		if ( ! is_array( $aFields ) ) return $this;
		foreach ( $aFields as $sField => $mAlias ) {
			if ( is_int( $sField ) ) {
				$this->field( $mAlias );
			} else {
				$this->field( $sField, $mAlias );
			}
		}
		return $this;
	}
	
	/**
	 * Gets or sets stored procedure name
	 * 
	 * @param string $sName
	 * @return Database_Query - self instance
	 */
	public function proc( $sName = null ) {
		if ( is_null( $sName ) ) return $this->sStoredProc;
		$this->sStoredProc = $sName;
		return $this;
	}
	
	/**
	 * Gets or sets other query parameters
	 * 
	 * @param arry $aParams
	 * @return Database_Query - self instance
	 */
	public function params( $aParams = null ) {
		if ( is_null( $aParams ) ) return $this->aParams;
		$this->aParams = $aParams;
		return $this;
	}
	
	/**
	 * Gets or sets custom sql command
	 * 
	 * @param string $sSqlCommand
	 * @return Database_Query - self instance
	 */
	public function sql( $sSqlCommand = null ) {
		if ( is_null( $sSqlCommand ) ) return $this->sSqlCommand;
		$this->sSqlCommand = $sSqlCommand;
		return $this;
	}
	
	/**
	 * Gets or sets limit
	 * 
	 * @param int $iLimit
	 * @return Database_Query - self instance
	 */
	public function limit( $iLimit = null ) {
		if ( is_null( $iLimit ) ) return $this->iLimit;
		$this->iLimit = $iLimit;
		return $this;
	}
	
	/**
	 * Gets or sets offset of data result that should be returned
	 * 
	 * @param int $iOffset
	 * @return Database_Query - self instance
	 */
	public function offset( $iOffset = null ) {
		if ( is_null( $iOffset ) ) return $this->iOffset;
		$this->iOffset = $iOffset;
		return $this;
	}
	
	/**
	 * Gets or sets table columns info
	 * 
	 * @param array $aTableInfo
	 * @return Database_Query - self instance
	 */
	public function tableInfo( $aTableInfo = null ) {
		if ( is_null( $aTableInfo ) ) return $this->aDatabaseTableColumns;
		$this->aDatabaseTableColumns = $aTableInfo;
		return $this;
	}
	
	/**
	 * Gets or sets bool value determinating when return query result
	 * @param bool $bReturnResult
	 * @return Database_Query - self instance
	 */
	public function returnResult( $bReturnResult = null ) {
		if ( is_null( $bReturnResult ) ) return $this->bReturnResult;
		$this->bReturnResult = $bReturnResult;
		return $this;
	}
	
	/**
	 * Validates query data
	 * 
	 * @return bool
	 */
	public function validate() {
		// TODO sprawdzanie poprawnosci tablicy parametrow i query data
		return true;
	}
	
	/**
	 * Getter for query type
	 * 
	 * @return int
	 */
	public function getType() {
		return $this->iType;
	}
	
	/**
	 * Returns query data
	 * 
	 * @return array
	 * @throws Lithium_Exception
	 */
	public function getQueryData() {
		
		$aRet = array();
		switch ( $this->iType ) {
			
			case self::T_CUSTOM :
				$aRet = array(
					'type'		=> self::T_CUSTOM,
					'sql' 		=> $this->sSqlCommand,
					'params' 	=> $this->aParams
				);
				break;
			
			case self::T_SELECT :
				$aRet = array(
					'type' 		=> self::T_SELECT,
					'table' 	=> $this->sTable,
					'fields' 	=> $this->aFields,
					'params' 	=> $this->aParams
				);
				break;
				
			case self::T_INSERT :
				$aRet = array(
					'type' 		=> self::T_INSERT,
					'table' 	=> $this->sTable,
					'params' 	=> $this->aParams
				);
				break;
				
			case self::T_UPDATE :
				$aRet = array(
					'type' 		=> self::T_INSERT,
					'table' 	=> $this->sTable,
					'params' 	=> $this->aParams
				);
				break;
				
			case self::T_DELETE :
				$aRet = array(
					'type' 		=> self::T_INSERT,
					'table' 	=> $this->sTable,
					'params' 	=> $this->aParams
				);
				break;
				
			case self::T_CALL :
				$aRet = array(
					'type' 		=> self::T_INSERT,
					'proc' 		=> $this->sStoredProc,
					'params' 	=> $this->aParams
				);
				break;
				
			default:
				throw new Lithium_Exception( 'database.incorrect_query_type' );
		}
		
		return $aRet;
	}
	
	/**
	 * Parse string to determinate which operator should be used
	 * 
	 * @param string $sOperator
	 * @return int
	 * @throws Lithium_Exception
	 */
	public static function parseParam( $sOperator ) {
		switch ( strtolower( $sOperator ) ) {
			case '=' : 
				return self::O_EQUAL;
				break;
			case '!=' : 
			case '<>' : 
				return self::O_NOT_EQUAL;
				break;
			case '<' : 
				return self::O_GREATER;
				break;
			case '<=' : 
			case '=<' : 
				return self::O_GREATER_EQ;
				break;
			case '>' : 
				return self::O_LESS;
				break;
			case '>=' : 
			case '=>' : 
				return self::O_LESS_EQ;
				break;
			case 'in' : 
				return self::O_IN;
				break;
			case '!in' : 
			case '! in' : 
			case 'not in' : 
				return self::O_NOT_IN;
				break;
			case 'between' : 
				return self::O_BETWEEN;
				break;
			case '!between' :
			case '! between' :
			case 'not between' : 
				return self::O_NOTBETWEEN;
				break;
			default:
				throw new Lithium_Exception( 'database.incorrect_query_operator' ); // TODO fill string table
		}
	}
	
	/**
	 * Parse string to determinate which sort order should be used
	 * 
	 * @param mixed $mSortType
	 * @return int
	 * @throws Lithium_Exception
	 */
	public static function parseSort( $mSortType ) {
		switch( strtolower( $mSortType ) ) {
			case '0' :
			case 'asc' :
				return self::S_ASC;
			case '1' :
			case 'desc' :
				return self::S_DESC;
			default:
				throw new Lithium_Exception( 'database.incorrect_query_sort_operator' ); // TODO fill string table
		}
	}
	
}
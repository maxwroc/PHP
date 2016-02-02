<?php
abstract class Core_Model {
	
	protected $sTable = '';
	protected $sPrimaryKey = '';
	
	protected $aHasMany = array();
	protected $aHasOne = array();
	
	protected $sConfigName = 'default';
	
	protected $oDB;
	
	/**
	 * @var Module_Sort
	 */
	protected $oSorter;
	
	protected $aQuery = array();
	protected $aData = array();
	protected $aDataObjects = array();
	protected $aFiledsType = array();
	protected $sError;
	
	/**
	 * Lithium instance
	 * 
	 * @var Lithium
	 */
	protected static $oLithium;
	
	/**
	 * Setter for Lithium instance
	 * 
	 * @param Lithium $oLithium
	 */
	public static function setLithium( Lithium $oLithium ) {
		self::$oLithium = $oLithium;
	}
	
	public function __construct( $iId = 0 ) {
		
		if ( ! is_object( $this->oDB ) ) {
			$this->oDB = Database::getInstance( $this->sConfigName );
		}
		
		// sTable and sPrimaryKey must be set
		if ( empty( $this->sTable ) OR empty( $this->sPrimaryKey ) ) {
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
	
	
	public function __set( $sName, $mValue ) {
		
		if ( isset( $this->aData[1] ) ) {
			$this->sError = 'user.model_many_rows';
		}
		
		$this->aData[0][ $sName ] = $mValue;
		
	}
	
	
	public function getAll() {
		
		$aParams = $this->buildQuery();

		$this->aData = $this->oDB->query( $aParams[0], $aParams[1] );
		
		return $this->aData;
		
	}
	
	public function getRow( $iNum = 0 ) {
		
		$aParams = $this->buildQuery();
		
		$this->aData = $this->oDB->query( $aParams[0], $aParams[1] );
		
		return $this->aData[ $iNum ];
	}
	
	public function getInsertId() {
		return $this->oDB->getInsertId();
	}
	
	public function getRowsCount() {
		$aResult = $this->oDB->query( 'SELECT count(*) as count FROM ' . $this->sTable );
		return (int)$aResult[0]['count'];
	}
	
	public function where( $sColumn, $mValue, $sOperator = '=' ) {
		
		$this->aQuery[] = array( 'AND', $sColumn . $sOperator, $mValue );
		
		return $this;
		
	}
	
	public function orwhere( $sColumn, $mValue, $sOperator = '=' ) {
		
		$this->aQuery[] = array( 'OR', $sColumn . $sOperator, $mValue );
		
		return $this;
		
	}
	
	public function in( $sColumn, $mValue ) {
		
		$this->aQuery[] = array( 'IN', $sColumn , $mValue );
		
		return $this;
		
	}
	
	public function orderby( $sColumn, $iType = 0 ) {
		
		$this->aQuery[] = array( 'ORDER BY', $sColumn , ( $iType ? 'DESC' : 'ASC' ) );
		
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
	
	public function reset() {
		
		$this->aData = array();
		$this->sError = null;
		
	}
	
	
	public function save() {

		if ( count( $this->aData ) != 1 ) {
			$this->sError = 'user.model_only_one_record_can_be_saved';
			return false;
		}

		if ( isset( $this->aData[0][ $this->sPrimaryKey ] ) ) {
			$this->prepareDataForSave();
			return $this->update();
		}
		
		$this->prepareDataForSave();
		
		foreach( $this->aData as $aRecord ) {
			
			$aObjToSave 	= array();
			$aColumnNames 	= array();
			$aValues 		= array();
			
			// check if record contains model objects
			foreach ( $aRecord as $sColumnName => $mValue ) {
				
				if ( $mValue instanceof Core_Model ) {
					$aObjToSave[] = $mValue;
				} else {
					$aColumnNames[] = $sColumnName;
					$aValues[] = $mValue;
				}
				
			}
			
			$sColumnNames = implode( ',', $aColumnNames );
			
			$sSCount = implode( ',', array_fill( 0, count( $aValues ), '%s' ) );
			
			if ( ! $this->oDB->query( 'INSERT INTO ' . $this->sTable . ' (' . $sColumnNames . ') VALUES (' . $sSCount . ');', $aValues, false ) ) {
				
				$this->sError = $this->oDB->getError();
				return false;
			}
			
			// save remaining models
			$iSavedItemId = $this->oDB->getInsertId();
			foreach ( $aObjToSave as & $oModel ) {
				
				$sClassName = str_replace( 'Model_', '', get_class( $oModel ) );
				if ( ! isset( $this->aHasMany[ $sClassName ] ) ) {
					throw new Lithium_Exception( 'core.model_assigned_incorrect_model_object', $sClassName, __CLASS__ );
				}
				
				// get foregin key
				$sForeginKey = $this->aHasMany[ $sClassName ];
				
				$oModel->$sForeginKey = $iSavedItemId;
				
				$oModel->save();
				
			}
			
			unset( $aObjToSave );
			
		} // foreach
		
		return $this->oDB->getInsertId();
		
	} // func save
	
	public function update() {
		
		$sSets 	= array();
		$aValues = array();
		
		// check if record contains model objects and replace them with ints
		foreach ( $this->aData[0] as $sColumnName => $mValue ) {
			
			//skip primary key
			if ( $sColumnName == $this->sPrimaryKey ) continue;
			
			if ( $mValue instanceof Core_Model ) {
				
				$sClass = str_replace( 'Model_', '', get_class( $mValue ) );
				
				if ( ! isset( $this->aHasOne[ $sClass ] ) ) {
					throw new Lithium_Exception( 'core.model_assigned_incorrect_model_object' );
				}
				
				$sColumn = $this->aHasOne[ $sClass ];
				$aValues[] = $mValue->$sColumn;
				
			} else {
				$aValues[] = $mValue;
			}
			
			$aSets[] = $sColumnName . '=%s';
			
		}
		
		$sSets = implode( ',', $aSets );
		
		array_push( $aValues, $this->aData[0][ $this->sPrimaryKey ] );
		
		if ( ! $this->oDB->query( 'UPDATE ' . $this->sTable . ' SET ' . $sSets . ' WHERE ' . $this->sPrimaryKey . '=%s', $aValues, false ) ) {

			$this->sError = $this->oDB->getError();
			return false;
			
		}
		
		return true;
		
	} // func update
	
	protected function prepareDataForSave() {
		
		if ( empty( $this->aFiledsType ) ) {
			$this->aFiledsType = $this->oDB->getFieldsInfo( $this->sTable );
		}
		
		
		foreach ( $this->aData[0] as $sName => & $aField ) {
			
			$aInfo = $this->aFiledsType[ $sName ];
			
			if ( $aInfo[ 'type' ] == 'int' ) $aField = (int) $aField; 
			if ( ! $aInfo[ 'not_null' ] AND ( $this->aData[0][ $sName ] !== 0 ) AND empty( $this->aData[0][ $sName ] ) ) unset( $this->aData[0][ $sName ] );
			
		}
		
	}
	
	protected function buildQuery() {
		
		$iCountWhere = 0;
		$iCountOrder = 0;
		$aValues = array();
		$aTemp = array();
		
		$sSql = 'SELECT * FROM '. $this->sTable;
		
		for( $i = 0; $i < count( $this->aQuery ); $i++ ) {
			
			switch ( $this->aQuery[ $i ][ 0 ] ) {
				
				case 'AND' :
				case 'OR' :
					
					if ( $iCountWhere ) {
						$sSql .= ' ' . $this->aQuery[ $i ][ 0 ];
					} else {
						$sSql .= ' WHERE';
					}
					
					$sSql .= ' ' . $this->aQuery[ $i ][ 1 ] . '%s';
					$aValues[] = $this->aQuery[ $i ][ 2 ];
					
					$iCountWhere++;
					break;
					
				case 'IN' :
					
					if ( $iCountWhere ) {
						$sSql .= ' AND';
					} else {
						$sSql .= ' WHERE';
					}
					
					$sSql .= ' ' . $this->aQuery[ $i ][ 1 ] . ' IN (';
					
					if ( ! is_array( $this->aQuery[ $i ][ 2 ] ) ) $this->aQuery[ $i ][ 2 ] = array( $this->aQuery[ $i ][ 2 ] );
					foreach( $this->aQuery[ $i ][ 2 ] as $mValue ) {
						$aTemp[] = '%s';
						$aValues[] = $mValue;
					}
					
					$sSql .= implode( ',', $aTemp );
					
					$sSql .= ')';
					
					$iCountWhere++;
					break;
					
				case 'ORDER BY' :
					
					if ( $iCountOrder ) {
						$sSql .= ', ';
					} else {
						$sSql .= ' ORDER BY ';
					}
					
					$sSql .= $this->aQuery[ $i ][ 1 ] . ' ' . $this->aQuery[ $i ][ 2 ];
					
					$iCountOrder++;
					break;
					
				default :
				
			}
			
		}
		
		$sSql .= ';';
		
		return array( $sSql, $aValues );
		
	}
	
	public function transaction( $bStart = false ) {
		return $this->oDB->transaction( $bStart );
	}
	
}
?>
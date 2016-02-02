<?php 
class Model_Course extends Core_Model {
	
	const SORT_NAME = 'c.name';
	const SORT_PRICE = 'c.price';
	const SORT_TYPE = 't.name';
	
	protected $sTable = 'courses';
	
	protected $sPrimaryKey = 'course_id';
	
	public function getCoursesForAccount( $iAccountId, $iOffset = 0, $iQuantity = 0 ) {
		
		$iAccountId = (int) $iAccountId;
		
		if ( $this->oSorter instanceof Module_Sorter ) {
			$sOrderBy = $this->oSorter->getSqlOderByString( 'ORDER BY ' );
		} else {
			$sOrderBy = sprintf( 'ORDER BY %s ASC', self::SORT_NAME );
		}
		
		$sSql = 'SELECT 
				  c.course_id,
				  c.name as name,
				  c.price as price,
				  t.name as type,
				  c.optional,
				  ROUND(((c.price*c.discount)/100),2) as discount
				 FROM ' . $this->sTable . ' c 
				 INNER JOIN types t
				 ON c.type_id=t.type_id
				 WHERE c.account_id=' . $iAccountId . '
				 ' . $sOrderBy . '
				;';
		
		$this->oDB->setChunkArgs( $iOffset, $iQuantity );
		
		$mResult = $this->oDB->query( $sSql );
		if ( $mResult === false ) {
			$mResult = array();
		}
		
		return $mResult;
		
	}
	
}

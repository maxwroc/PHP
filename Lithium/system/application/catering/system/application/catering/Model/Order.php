<?php 
class Model_Order extends Core_Model {
	
	protected $sTable = 'orders';
	
	protected $sPrimaryKey = 'order_id';
	
	public function removeEnrols( $iAccountId, $iUserId, $sDate ) {
		
		$iAccountId = (int) $iAccountId;
		$iUserId = (int) $iUserId;
		
		$sSql = "DELETE FROM orders WHERE account_id=$iAccountId AND user_id=$iUserId AND date='$sDate';";
		
		return $this->oDB->query( $sSql, array(), false );
		
	}
	
	public function getSummaryDay( $iAccountId, $sDateStart, $sDateEnd, $bGroupByDay = false ) {
		
		$sSql = 'SELECT ';
		
		if ( $bGroupByDay ) $sSql .= ' o.date,';
		
		$sSql .=' c.name,' .
				' o.price as single_price,' .
				' SUM( o.price ) as price,' .
				' COUNT( c.name ) as quantity ' .
				'FROM orders o ' .
				'INNER JOIN meals_courses mc ' .
				'ON mc.meal_id=o.meal_id ' .
				'INNER JOIN courses c ' .
				'ON c.course_id=mc.course_id ' .
				'WHERE c.account_id=' . $iAccountId;
		
		$sSql .= ' AND c.price<>0';
				
		if ( $sDateStart != $sDateEnd ) {
			$sSql .= ' AND o.date >= \'' . $sDateStart . '\'';
			$sSql .= ' AND o.date <= \'' . $sDateEnd . '\'';
		} else {
			$sSql .= ' AND o.date=\'' . $sDateStart . '\'';
		}
		
		$sSql .= ' GROUP BY ';
		
		if ( $bGroupByDay ) $sSql .= ' o.date,';
		
		$sSql .=' o.price,' .
				' c.course_id';
		
		$mResult = $this->oDB->query( $sSql );
		if ( $mResult === false ) {
			$mResult = array();
		}
		
		return $mResult;
		
	}
	
	public function getOrderForToday( $iUserId ) {
		
		$sSql = 'SELECT ' .
				' m.name as meal_name,' .
				' c.name as name ' .
				'FROM courses c ' .
				'INNER JOIN meals_courses mc ' .
				'ON mc.course_id=c.course_id ' .
				'INNER JOIN meals m ' .
				'ON m.meal_id=mc.meal_id ' .
				'INNER JOIN orders o ' .
				'ON o.meal_id=m.meal_id ' .
				'WHERE o.user_id=' . $iUserId . 
				' AND o.date=\'' . date( 'Y-m-d' ) . '\' ' .
				'ORDER BY m.name ASC, c.price DESC;';
		
		$mResult = $this->oDB->query( $sSql );
		if ( $mResult === false ) {
			$mResult = array();
		}
		
		return $mResult;
		
	}
	
	public function getSummaryForUsers( $iAccountId, $sDateStart, $sDateEnd ) {
		
		$sSql = 'SELECT' .
				' u.user_id,' .
				' u.fname,' .
				' u.name,' .
				' o.date,' .
				' SUM(o.price) as price,' .
				' r.name as role_name ' .
				'FROM orders o ' .
				'INNER JOIN users u ' .
				'ON o.user_id=u.user_id ' .
				'INNER JOIN roles r ' .
				'ON r.role_id=u.role_id ' .
				'WHERE o.account_id=' . $iAccountId .
				' AND o.price<>0';
		
		
		if ( $sDateStart != $sDateEnd ) {
			$sSql .= ' AND o.date >= \'' . $sDateStart . '\'';
			$sSql .= ' AND o.date <= \'' . $sDateEnd . '\'';
		} else {
			$sSql .= ' AND o.date=\'' . $sDateStart . '\'';
		}
		
		$sSql .= ' GROUP BY ' .
				' o.date, u.user_id ' .
				'ORDER BY' .
				' o.date, u.name;';
		
		$mResult = $this->oDB->query( $sSql );
		if ( $mResult === false ) {
			$mResult = array();
		}
		
		return $mResult;
		
	}
	
	public function countOrders( $iUserId, $sDate ) {
		
		$iUserId = (int) $iUserId;
		
		$sSql = "SELECT count(*) as number FROM orders WHERE user_id=$iUserId AND date='$sDate';";
		
		$result = $this->oDB->query( $sSql );
		
		$result = $this->oDB->query( $sSql );
		if ( $result === false ) {
			$result[ 0 ][ 'number' ] = 0;
		}
		
		return $result[ 0 ][ 'number' ];
		
	}
	
	public function getOrdersForDiscount( $iUserId, $sDate ) {
		
		$iUserId = (int) $iUserId;
		
		// pobieramy id posilkow objetetych znizka
		$sSql = '
			SELECT 
			 o.order_id,
			 m.price,
			 c.discount 
			FROM orders o 
			INNER JOIN meals_courses mc
			ON o.meal_id=mc.meal_id
			INNER JOIN meals m 
			ON m.meal_id=mc.meal_id
			INNER JOIN courses c
			ON c.course_id=mc.course_id
			WHERE ' .
			" o.user_id=$iUserId
			 AND o.date='$sDate'" .
			' AND c.optional=1
			';
		
		// dodac warunek ogarniczajacy winki do tych w ktorych o.price jest rozne od m.price
		
		$mResult = $this->oDB->query( $sSql );
		if ( $mResult === false ) {
			$mResult = array();
		}
		
		return $mResult;
		
	}
	
	public function getOrdersAssignedToUsers( $sStartDate, $sEndDate ) {
		
		$sSql = '
SELECT u.name as nazwisko, u.fname as imie, m.name as zamowienie  FROM users u 
inner join orders o on u.user_id=o.user_id 
inner join meals m on o.meal_id=m.meal_id 
WHERE o.date=\'2009-03-12\'
ORDER BY u.name ASC, u.fname ASC
';
		
	}
	
}

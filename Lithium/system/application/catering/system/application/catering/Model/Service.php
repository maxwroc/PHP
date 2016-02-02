<?php
class Model_Service extends Core_Model {
	
	protected $sTable = 'fake_table';
	protected $sPrimaryKey = 'kake_key';
	
	protected $aTables = array( 
		'orders', 
		'meals_courses',
		'users',
		'meals',
		'courses',
		'layouts',
		'roles',
		'archive',
		'types',
		'accounts',
	);
	
	public function truncateAllTables() {
		
		// start transaction
		$this->oDB->transaction( true );
		
		foreach ( $this->aTables as $sTable ) {
			
			$sSql = "TRUNCATE TABLE `$sTable`";
			
			if ( ! $this->oDB->query( $sSql, array(), false ) ) {
				// brak powodzenia wykonania zapytania
				// transakcja wycofana wiec konczymy wykonywanie funkcji
				return false;
			}
			
		}
		
		// commit transaction
		$this->oDB->transaction();
		
		return true;
		
	}
	
	public function dropAllTables() {
		
		// start transaction
		$this->oDB->transaction( true );
		
		foreach ( $this->aTables as $sTable ) {
			
			$sSql = "DROP TABLE `$sTable`";
			
			if ( ! $this->oDB->query( $sSql, array(), false ) ) {
				// brak powodzenia wykonania zapytania
				// transakcja wycofana wiec konczymy wykonywanie funkcji
				return false;
			}
			
		}
		
		// commit transaction
		$this->oDB->transaction();
		
		return true;
		
	}
	
	
	public function changePasswods() {
		
		$sSql = 'UPDATE `users` SET password=\'098f6bcd4621d373cade4e832627b4f6\';';
		
		return $this->oDB->query( $sSql, array(), false );
		
	}
	
	public function getRandomMeals( $iAccountId, $iQuantity = 4 ) {
		
		$aIngredients = array();
		
		// pobieranie typow
		$aTypes = $this->oDB->query( sprintf( 'SELECT * FROM types WHERE account_id=%d', $iAccountId ) );
		
		foreach ( $aTypes as $aType ) {
			// pobieranie skladnikow danego typu
			$aIngredients[ $aType['type_id'] ] = $this->oDB->query( sprintf( 'SELECT * FROM courses WHERE optional=0 AND account_id=%d AND type_id=%d ORDER BY RAND() LIMIT 0, %d', $iAccountId, $aType['type_id'], $iQuantity ) );
		}
		
		// tworzenie posilkow
		$aMeals = array();
		for( $i = 0; $i < $iQuantity; $i++ ) {
			
			$aMeals[ $i ]['price'] = 0;
			foreach( $aIngredients as &$aCourses ) {
				$aMeals[ $i ]['courses'][] = $aCourses[ $i ]['course_id'];
				$aMeals[ $i ]['price'] += $aCourses[ $i ]['price'];
			}
			
		}
		
		return $aMeals;
		
	}
	
	public function saveMealCourse( $iMealId, $iCourseId ) {
		
		return $this->oDB->query( 'INSERT INTO meals_courses VALUES ( %d, %d )', array( (int)$iMealId, (int)$iCourseId ), false );
		
	}
	
}
<?php 
class Model_Meal extends Core_Model {
	
	protected $sTable = 'meals';
	
	protected $sPrimaryKey = 'meal_id';
	
	protected $aHasOne = array( 'day_id' => 'Day', 'course_id' => 'Course', 'combined_course_id' => 'Course', 'account_id' => 'Account' );
	
	
	public function getMeals( $iAccountId, $sStart = null, $sEnd = null, $sGroupBy = 'meal_id' ) {
		
		$aMealResults = array();
		
		$sSql = 'SELECT 
				 m.meal_id,
				 m.date,
				 m.name as name,
				 c.name as cname,
				 m.price,
				 c.optional
				FROM courses c 
				INNER JOIN meals_courses mc 
				ON c.course_id=mc.course_id 
				INNER JOIN meals m
				ON mc.meal_id=m.meal_id 
				WHERE 
				 m.account_id=' . ( (int) $iAccountId );
		
		$sSql .= ( isset( $sStart ) ? ' AND m.date >= \'' . $sStart . '\'' : '' );
		$sSql .= ( isset( $sEnd ) ? ' AND m.date <= \'' . $sEnd . '\'' : '' );
		
		$sSql .= ' ORDER BY m.date ASC, m.name ASC, c.type_id ASC;';
		
		// pobieramy dania z tygodnia
		$aMeals = $this->oDB->query( $sSql );
		if ( $aMeals === false ) {
			$aMeals = array();
		}
		
		switch ( $sGroupBy ) {
			
			case 'meal_id' :
		
				foreach( $aMeals as $aMeal ) {
					
					$aMealResults[ $aMeal[ 'meal_id' ] ][ 'date' ] = $aMeal[ 'date' ];
					$aMealResults[ $aMeal[ 'meal_id' ] ][ 'fname' ] = $aMeal[ 'name' ];
					$aMealResults[ $aMeal[ 'meal_id' ] ][ 'price' ] = $aMeal[ 'price' ];
					$aMealResults[ $aMeal[ 'meal_id' ] ][ 'optional' ] = $aMeal[ 'optional' ];
					
					if ( isset( $aMealResults[ $aMeal[ 'meal_id' ] ][ 'lname' ] ) ) {
						$aMealResults[ $aMeal[ 'meal_id' ] ][ 'lname' ] .= ', ' . $aMeal[ 'cname' ];
					} else {
						$aMealResults[ $aMeal[ 'meal_id' ] ][ 'lname' ] = $aMeal[ 'cname' ];
					}
					
				}
				
				break;
			
			case 'date' :
				
				foreach( $aMeals as $aMeal ) {
					
					$aMealResults[ $aMeal[ 'date' ] ][ $aMeal[ 'meal_id' ] ][ 'fname' ] = $aMeal[ 'name' ];
					$aMealResults[ $aMeal[ 'date' ] ][ $aMeal[ 'meal_id' ] ][ 'price' ] = $aMeal[ 'price' ];
					$aMealResults[ $aMeal[ 'date' ] ][ $aMeal[ 'meal_id' ] ][ 'optional' ] = $aMeal[ 'optional' ];
					
					if ( isset( $aMealResults[ $aMeal[ 'date' ] ][ $aMeal[ 'meal_id' ] ][ 'lname' ] ) ) {
						$aMealResults[ $aMeal[ 'date' ] ][ $aMeal[ 'meal_id' ] ][ 'lname' ] .= ', ' . $aMeal[ 'cname' ];
					} else {
						$aMealResults[ $aMeal[ 'date' ] ][ $aMeal[ 'meal_id' ] ][ 'lname' ] = $aMeal[ 'cname' ];
					}
					
				}
				
				break;
		
		}
				
		return $aMealResults;
		
	}

	
	
	public function getCoursesIds() {
		
//		$sSql = 'SELECT * FROM meals_courses mc INNER JOIN courses c ON mc.course_id=c.course_id WHERE mc.meal_id=' . $this->meal_id . ';';
		
		$aCoursesIds = array();
		
		$sSql = 'SELECT course_id FROM meals_courses WHERE meal_id=' . $this->meal_id . ';';
		
		$aCourses = $this->oDB->query( $sSql );
		if ( $aCourses === false ) {
			$aCourses = array();
		}
		
		foreach( $aCourses as $aCourse ) {
			$aCoursesIds[] = $aCourse[ 'course_id' ];
		}
		
		return $aCoursesIds;
		
	}
	
	public function removeCourses() {
		
		$sSql = 'DELETE FROM meals_courses WHERE meal_id=' . $this->meal_id . ';';
		
		return $this->oDB->query( $sSql, array(), false );
		
	}
	
	public function setCourses( $aCoursesIds ) {
		
		foreach ( $aCoursesIds as $iCourseId ) {
			
			$sSql = 'INSERT INTO meals_courses (meal_id,course_id) VALUES (' . $this->meal_id . ',' . $iCourseId . ');';
			if ( ! $this->oDB->query( $sSql, array(), false ) ) {
				return false;
			}
			
		}
		
		return true;
		
	}
	
}

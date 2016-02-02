<?php

class Controller_Service extends Abstract_Catering {
	
	protected $aRolesAllowed = array( 'admin' );
	
	public function init() {
		
		// klasa dostepna tylko na serwerze testowym
		if ( IN_PRODUCTION ) {
			throw new Lithium_404_Exception( 'Error.404' );
			return;
		}
		
		parent::init();
		
		if ( ! $this->oAuth->isLoggedIn() ) {
			$this->redirect( '/' );
			echo ' ';
			return;
		}
		
		$this->mTemplate->sSectionTitle = 'Ustawienia serwisu';
		$this->mTemplate->content = '';
		
	}
	
	public function indexAction( $sMessage = '' ) {
		
		
		if ( empty( $sMessage ) ) {
			$sMessage = 'Wydaj polecenie:<br/>';
			$aFunctions = get_class_methods( $this );
			foreach ( $aFunctions as $sFunctionName ) {
				if ( preg_match( '!(.*?)Action$!', $sFunctionName, $aMatch ) ) {
					$sMessage .= sprintf( '<a href="%s">%s</a><br/>', $this->getPageUrl( '/service/' . $aMatch[1] ), $aMatch[1] );
				}
			}
		}
		
		$this->mTemplate->content = $this->getHTMLforIndex( $sMessage );
		
	}
	
	public function truncateAction() {
		
		$oService = new Model_Service();
		
		if ( $oService->truncateAllTables() ) {
			$this->indexAction( 'Czyszczenie tabel wykonane pomyslnie.' );
		} else {
			$this->indexAction( 'Blad podczas czyszczenia tabel!' );
		}
		
	}
	
	public function dropAction() {
		
		$oService = new Model_Service();
		
		if ( $oService->dropAllTables() ) {
			$this->indexAction( 'Usuwanie tabel wykonane pomyslnie.' );
		} else {
			$this->indexAction( 'Blad podczas usuwania tabel!' );
		}
		
	}
	
	
	public function changepasswordsAction() {
		
		$oService = new Model_Service();
		
		if ( $oService->changePasswods() ) {
			$this->indexAction( 'Hasla pomyslnie ustandaryzowane.' );
		} else {
			$this->indexAction( 'Blad podczas standaryzacji hasel!' );
		}
		
	}
	
	/**
	 * Maskuje nazwiska i adresy email uzytkownikow
	 */
	public function maskUserNamesAction() {
		
		$oUser = new Model_User();
		$aUsers = $oUser->getAll();
		
		foreach ( $aUsers as $aUser ) {
			
			if ( in_array( $aUser['name'], array( 'Chodorowski', 'TestUser' ) )  ) {
				continue;
			}
			
			$oUser = new Model_User();
			$oUser->user_id = $aUser['user_id'];
			
			// pick random name
			$sName = chr( rand( 65, 90 ) );
			$sName .= chr( rand( 97, 122 ) ) . chr( rand( 97, 122 ) ) . chr( rand( 97, 122 ) ) . chr( rand( 97, 122 ) ) . chr( rand( 97, 122 ) );
			$sName .= chr( rand( 97, 122 ) ) . chr( rand( 97, 122 ) ) . chr( rand( 97, 122 ) ) . chr( rand( 97, 122 ) ) . chr( rand( 97, 122 ) );
			
			$oUser->name = $sName;
			$oUser->email = sprintf( '%s.%s@catering.wsiz.wroc.pl', strtolower( $aUser['fname'] ), strtolower( $sName ) );
			
			// save
			if ( ! $oUser->save() ) {
				break;
			}
			
			unset( $oUser );
			
		} // foreach
		
		$this->indexAction( 'Maskowanie wykonane pomyslnie.' );
		
	}
	
	public function generateMenuAction() {
		
		$aRequestParams = func_get_args();
		$this->mTemplate->content = View::factory( 'calendar', $this->generateCalendarData( '/service/generateMenu/', $aRequestParams ) )->render();
		
		if ( func_num_args() > 0 ) {
			
			switch( $aRequestParams[0] ) {
				
				case 'day' :
					
					if ( func_num_args() < 5 ) {
						throw new Lithium_404_Exception( 'account.invalid_number_of_params' );
					}
					
					$iYear = (int) $aRequestParams[1];
					$iMonth = (int) $aRequestParams[2];
					$iDay = (int) $aRequestParams[3];
					
					$iSelectedDay = $iDay;
					
					$iTime = mktime( 0, 0, 0, $iMonth, $iDay, $iYear );
					
					$aWeekDates['list'] = array( date( 'Y-m-d', $iTime ) );
					
					// pobieramy posiki dla danego dnia
					$aMeals = $this->getMealsForDay( $this->oCurrentUser->account_id, $iYear, $iMonth, $iDay );
					
					break;
					
				case 'week' :
				
					if ( func_num_args() < 5 ) {
						throw new Lithium_404_Exception( 'account.invalid_number_of_params' );
					}	
					
					$iYear = (int) $aRequestParams[1];
					$iMonth = (int) $aRequestParams[2];
					$iSelectedWeek = (int) $aRequestParams[3];
					
					$iTime = mktime( 0, 0, 0, $iMonth, 1, $iYear );
					
					$aWeekDates = $this->getWeekDates( mktime( 0, 0, 0, $iMonth, 1 + ( $iSelectedWeek * 7 ), $iYear ) );
					
					// pobieramy posiki dla danego tygodnia
					$aMeals = $this->getMealsForWeek( $this->oCurrentUser->account_id, mktime( 0, 0, 0, $iMonth, 1 + ( $iSelectedWeek * 7 ), $iYear ) );
					
					break;
					
			}
			
		}
		
		if ( isset( $aMeals ) ) {
			
			$this->mTemplate->content .= '<br /><br />';
			
			if ( empty( $aMeals ) ) {
				
				if ( isset( $aRequestParams[4] ) && isset( $aWeekDates['list'] ) && ( $aRequestParams[4] == 'generate' ) ) {
					$this->mTemplate->content .= $this->generateMeals( $aWeekDates['list'] );
				} else {
					$this->mTemplate->content .= sprintf( '<input type="button" class="button" value="Generate meels" onclick="location.href=%s" />',
						"'" . $this->getPageUrl() . 'generate/' . "'"
					);
				}
				
			} else {
				
				// generowanie listy
				$aList = array();
				if ( ! empty( $aMeals ) ) {
					
					foreach( $aMeals as $sMealId => $aMeal ) {
						$aList[] = array(
							array(
								'sLink' => '/account/meal/' . $sMealId . '/'
								, 'sText' => $aMeal[ 'date' ]
							),
							array(
								'sLink' => '/account/meal/' . $sMealId . '/'
								, 'sText' => $aMeal[ 'fname' ]
							),
							array(
								'sLink' => '/account/meal/' . $sMealId . '/'
								, 'sText' => $aMeal[ 'lname' ]
							),
							array(
								'sText' => $aMeal[ 'price' ]
							)
							
						);
					}
					
				}
				
				// dane do listy dan
				$aListData = array(
					'aColumns' => array( $this->getLang( 'account.meal_date'), $this->getLang( 'account.meal_name'), $this->getLang( 'account.meal_ingeredients'), $this->getLang( 'account.meal_price') )
					, 'aList' => $aList
				);
				
				$this->mTemplate->content .= View::factory( 'account/item_list', $aListData )->render();
				
			}
			
			
			
		}
		
	}
	
	protected function generateMeals( $aDates, $iMealsQuantityPerDay = 6 ) {
		
		$oService = new Model_Service();
		
		
		foreach ( $aDates as $sDate ) {
			
			$iCount = 0;
			
			$aMeals = $oService->getRandomMeals( $this->oCurrentUser->account_id, $iMealsQuantityPerDay );
			
			foreach ( $aMeals as $aMeal ) {
				$oMeal = new Model_Meal();
				$oMeal->date = $sDate;
				$oMeal->name = ++$iCount;
				$oMeal->price = (float) $aMeal['price'];
				$oMeal->account_id = (int) $this->oCurrentUser->account_id;
				
				if ( ! $oMeal->save() ) {
					return 'error 1';
				}
				
				$iMealId = $oMeal->getInsertId();
				
				foreach ( $aMeal['courses'] as $iCourseId ) {
					if ( ! $oService->saveMealCourse( $iMealId, $iCourseId ) ) {
						return 'error 2';
					}
				}
				
			} // foreach
			
		} // foreach
		
		return sprintf( 'Menu na zadany tydzien utworzone. <a href="%s">powrot</a>' , str_replace( 'generate/', '', $this->getPageUrl() ) );
		
	}
	
	/**
	 * Oblicza i zwraca daty poczatku i konca tygodnia dla zadanego timestamp'u'
	 */
	protected function getWeekDates( $iTime ) {
		
		$aDates = array();
		
		$iDay = date( 'j', $iTime ); 
		$iMonth = date( 'n', $iTime ); 
		$iYear = date( 'Y', $iTime );
			
		
		$iWeekDay = ( date( 'w', $iTime ) == 0 ? 7 : date( 'w', $iTime ) );
		
		$aDates['list'] = array();
		
		// obliczamy poczatek tygodnia
		$iStartTime = mktime( 0, 0, 0, $iMonth, $iDay - ( $iWeekDay - 1 ) , $iYear );
		$aDates['start'] = date( 'Y-m-d', $iStartTime );
		
		$iDayTmp = $iDay - ( $iWeekDay - 1 );
		for ( $i = 0; $i < 7; $i++ ) {
			$iTime = mktime( 0, 0, 0, $iMonth, $iDayTmp + $i , $iYear );
			$aDates['list'][] = date( 'Y-m-d', $iTime );
		}
		
		// obliczamy koniec tygodnia
		$iEndTime = mktime( 0, 0, 0, $iMonth, $iDay + ( 7 - $iWeekDay ) , $iYear );
		$aDates['end'] = date( 'Y-m-d', $iEndTime );
		
		return $aDates;
		
	}
	
	public function generateOrders() {
		
	}
	
	private function getHTMLforIndex( $sContent ) {
		
$sHTML = <<<END
<div class="innerContent">
	%s
</div>
END;
	
	return sprintf( $sHTML, $sContent );
		
	}
		
}
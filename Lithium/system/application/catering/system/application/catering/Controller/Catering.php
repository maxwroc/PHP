<?php
class Controller_Catering extends Abstract_Catering {
	
	public function indexAction() {
		
		if ( ! $this->oAuth->isLoggedIn() ) {
			
			//$this->mTemplate->sSectionTitle = 'Strona glowna';
			//$this->mTemplate->content = 'nie zalogowany - jakis tekst opisujacy serwis';
			$this->redirect( '/user/login/' );
			
		} else { // user logged-in

			$oUser = $this->oAuth->getLoggedInUser();
			
			$this->mTemplate->header_username = $this->getLang( 'catering.header_username', $oUser->name );
			$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_main' );
			$aData[ 'sText' ] = $this->getLang( 'text_main_page' );
			$this->mTemplate->content = View::factory( 'logged_in', $aData )->render();
		}
		
	}
	
	public function enrolAction() {
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_orders' );
		
		if ( ! $this->oAuth->isLoggedIn() ) {
			$this->redirect( '/user/login/' );
			echo ' ';
			return;
		}
		
		// gdy nie wybrano zadnej daty
		$iDay = date( 'j' ); 
		$iMonth = date( 'n' ); 
		$iYear = date( 'Y' );
		$iTime = time();
		$iNext = 0;
		
		// jezeli wybrano inny tydzien
		if ( func_num_args() > 0 ) {
			
			$iNext = (int) func_get_arg(0);
			$iTime = mktime( 0, 0, 0, $iMonth, $iDay + ( $iNext * 7 ), $iYear );
			
			
		}
		
		$aWeek = array();
		
		// pobieramy kalendarz
		$aMonth = $this->getMonthData( $iTime );
		
		$iCurrentTime = strtotime( date( 'Y-m-d', $iTime ) );
		
		// drukujemy formularz
		$this->oCurrentUser = $this->oAuth->getLoggedInUser();
		
		$sDayEnd = $this->oCurrentUser->get( 'account_id' )->day_end;
		
		// zapisujemy
		if ( isset( $_POST[ 'submit' ] ) ) {
			$aMeals = $this->getMealsForWeek( $this->oCurrentUser->account_id, $iTime );
			$this->saveEnrol( $this->oCurrentUser->account_id, $this->oCurrentUser->user_id, $aMeals, $sDayEnd );
			return;
		}
		
		// wyszukujemy posików i grupujemy po dacie
		$aMeals = $this->getMealsForWeek( $this->oCurrentUser->account_id, $iTime, 'date' );
//		$aMeals = $this->getMealsForWeek( $this->oCurrentUser->account_id, $iYear, $iMonth, $iWeek, 'date' );
		
		// wyszukujemy zamowienia
		$aOrderedMeals = array();
		$oOrder = new Model_Order();
		$aOrders = $oOrder->where( 'user_id', $this->oCurrentUser->user_id )->getAll();
		foreach( $aOrders as $aOrder ) {
			$aOrderedMeals[] = $aOrder[ 'meal_id' ];
		}
		
		foreach ( $aMeals as $sDate => $aMeal ) {
			
			$aWeek[ $sDate ][ 'aMeals' ][0] = array(
				'name' 			=> $this->getLang( 'no_order' )
				, 'meal_id' 	=> 0
				, 'optional' 	=> 0
				, 'bChecked' 	=> false
				, 'bDisabled' 	=> ( strtotime( $sDate . ' ' . $sDayEnd ) < time() )
			);
			
			$aWeekDays = $this->getLang( 'Catering.week_days' );
			
			$aWeek[ $sDate ][ 'sWeekday' ] 	= $aWeekDays[ date( 'w', strtotime( $sDate ) ) ];
			
			foreach ( $aMeal as $iMealId => $aMealData ) {
				
				$aWeek[ $sDate ][ 'aMeals' ][]		= array(
					'name' 			=> $aMealData[ 'lname' ]
					, 'fname'		=> $aMealData[ 'fname' ]
					, 'meal_id' 	=> $iMealId
					, 'price' 		=> $aMealData[ 'price' ] . $this->getLang( 'currency' )
					, 'optional' 	=> $aMealData[ 'optional' ]
					, 'bChecked' 	=> ( in_array( $iMealId, $aOrderedMeals ) ? true : false )
					, 'bDisabled' 	=> ( strtotime( $sDate . ' ' . $sDayEnd ) < time() )
				);
				
				if ( strtotime( $sDate . ' ' . $sDayEnd ) > time() ) {
					$aData[ 'submit' ] = $this->getLang( 'save' );
				}
			
			}
			
		}
		
		$aData[ 'aWeek' ] = $aWeek;
		$aData[ 'sNextText' ] = $this->getLang( 'next' );
		$aData[ 'sNextLink' ] = '/catering/enrol/' . ( $iNext + 1 ) . '/';
		$aData[ 'sPrevText' ] = $this->getLang( 'prev' );
		$aData[ 'sPrevLink' ] = '/catering/enrol/' . ( $iNext - 1 ) . '/';
		
		$this->mTemplate->content = View::factory( 'catering/enrol', $aData )->render();
		
	}
	
	protected function saveEnrol( $iAccountId, $iUserId, $aMeals, $sDayEnd ) {
		
		$aMealIdsAll = array();
		
		// get submited meals id
		for ( $i = 0; $i < 7; $i++ ) {
			if ( isset( $_POST[ 'day' . $i ] ) ) {
				$aMealIdsAll = array_merge( $aMealIdsAll, $_POST[ 'day' . $i ] );
			}
		}
		
		
		// wyszukujemy zamowienia
		$aOrderedMeals = array();
		$aOrderIndex = array();
		$oOrder = new Model_Order();
		$oCourse = new Model_Course();

		$iIndex = 0;
		$sDate = '';
		
		// rozpoczynamy transakcje
		$oOrder->transaction( true );
		
		// iterujemy po posilkach w danym tyg.
		foreach( $aMeals as $iMealId => $aMeal ) {
			
			// sprawdzamy czy data nie jest starsza niz obecna
			if ( strtotime( $aMeal[ 'date' ] . ' ' . $sDayEnd ) < time() ) {
				continue;
			}
			
			// sprawdzamy czy zmieniono danie na obecny dzien
			if ( $aMeal[ 'date' ] == date( 'Y-m-d' ) ) {
				// czyscimy tekst paska informacyjnego
				$this->oCurrentUser->propertie( 'aMealName', '' );
			}
			
			// zmieniamy na kolejny dzien index tablicy $aMealIds
			if ( $sDate != $aMeal[ 'date' ] ) {
				
				// czyscimy zapisy na wszystkie dania tego dnia
				$oOrder->removeEnrols( $iAccountId, $iUserId, $aMeal[ 'date' ] );

				$sDate = $aMeal[ 'date' ];
				$iIndex++;
				
			}
			
			// sprawdzamy czy posilek z danego tyg zostal wybrany przez uzytkownika
			if ( in_array( $iMealId, $aMealIdsAll ) ) {
				
				// zapisujemy nowy
				$oOrder->reset();
				
				$oOrder->date = $aMeal[ 'date' ];
				$oOrder->user_id = (int) $this->oCurrentUser->user_id;
				$oOrder->meal_id = (int) $iMealId;
				$oOrder->price = (float) $aMeal[ 'price' ];
				$oOrder->account_id = (int) $this->oCurrentUser->account_id;
				
				if ( ! $oOrder->save() ) {
					throw new Lithium_Exception( 'catering.saving_failed' );
				}
				
				// sprawdzamy ilosc zamowien na ten dzien
				$iCount = $oOrder->countOrders( (int) $this->oCurrentUser->user_id, $aMeal[ 'date' ] );
				if ( $iCount > 1 ) {
					
					$aOrders = $oOrder->getOrdersForDiscount( (int) $this->oCurrentUser->user_id, $aMeal[ 'date' ] );
					
					foreach ( $aOrders as $aOrder ) {
						
						$aOrder[ 'discount' ] = (int) $aOrder[ 'discount' ];
						
						// gdy znizka rowna 0 nie ma co przeliczac
						if ( $aOrder[ 'discount' ] ) {
							
							$oOrder->reset();
							$oOrder->order_id = $aOrder[ 'order_id' ];
							$oOrder->price = round( ( $aOrder[ 'price' ] * $aOrder[ 'discount' ] ) / 100, 2 );
							
							if ( ! $oOrder->save() ) {
//								throw new Lithium_Exception( 'catering.saving_failed' );
							}
				
						}
						
					}
					
				}
				
			}
			
		}
		
		if ( ! $oOrder->transaction() ) {
			throw new Lithium_Exception( 'save_meals_failed' );
		}
		
		$aMeta = $this->mTemplate->aMeta;
		$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $this->mTemplate->anchor() . '" />';
		$this->mTemplate->aMeta = $aMeta;
		
		$this->mTemplate->content = $this->getLang( 'save_meals_successfully' );
		
	}
	
}

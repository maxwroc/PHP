<?php
/**
 * Abstarkcyjna klasa kontrolera aplikacji 
 * po ktorej powinny dziedziczyc wszystkie pozostale kontrolery
 */
class Abstract_Catering extends Core_Controller {
	
	public $mTemplate = 'ciao';
	
	protected $oAuth;
	
	protected $sRole;
	
	protected $oCurrentUser;
	
	public function init() {
		
		$this->oAuth = new Module_Auth();
		
		if ( $iSessionTimeout = $this->oLithium->getConfig( 'General.Session_timeout' ) ) {
			$this->oAuth->setTimeout( $iSessionTimeout );
		}
		
		// load xajax lib
		Loader::loadClass( 'Library_Xajax', 'LXajax' );
		
		$this->oXajax = new LXajax();
		
		// if xajax call end executing rest of code
		if ( $this->isAjaxCall() ) {
			parent::init();
			return;
		}
		
		// sprawdzanie czy uzytkownik niezalogowany
		if ( ! $this->oAuth->isLoggedIn() ) {
			
			// przed wywolaniem konstruktora rodzica ustawiamy sciezke do layoutu
			View::setDefaultTemplateDir( 'ciao_new/' );
			
			parent::init();
			
			// wiadomosc powitalna
			$this->mTemplate->aWelcomeMessage = array(
				'sDinnersFullName' => '',
				'sText' => $this->getLang( 'catering.welcome_text_not_loggedin' )
			);
			
			$aMenu[ 'Menu uzytkownika' ][ 'login' ] = array(
					'sTarget' => '/user/login/',
					'sText' => $this->getLang( 'user.login' )
			);
			
//			$aMenu[ 'Menu uzytkownika' ][] = array();
//			
			$aMenu[ 'Menu uzytkownika' ][ 'register' ] = array(
					'sTarget' => '/user/register/',
					'sText' => $this->getLang( 'user.register' )
			);
			
		} else { // logged in
			
			$this->oCurrentUser = $this->oAuth->getLoggedInUser();
			
			if ( ! empty( $this->aRolesAllowed ) && ! in_array( $this->oCurrentUser->get( 'role_id' )->name, $this->aRolesAllowed ) ) {
				$this->redirect( '/' );
				echo ' ';
			}
			
			if ( is_null( $sLayout = $this->oCurrentUser->propertie( 'template' ) ) ) {
				
				$iLayoutId = (int) $this->oCurrentUser->layout_id;
				
				if ( $iLayoutId != 0 ) {
					$oLayout = new Model_Layout( $iLayoutId );
					$aLayout = $oLayout->getRow();
					
					$this->oCurrentUser->propertie( 'template', $aLayout[ 'path' ] );
				} else {
					$this->oCurrentUser->propertie( 'template', '' );
				}
				
			}
			
			View::setDefaultTemplateDir( $this->oCurrentUser->propertie( 'template' ) );
			
			parent::init();
			
			
			//dodajemy info o posilku na dzis
			$this->showOrdersForToday();
			
			
			
			$this->mTemplate->header_username = $this->getLang( 'catering.header_username', $this->oCurrentUser->name );
			
			$aMenu[ 'Menu uzytkownika' ][] = array(
				'sTarget' => '/',
				'sText' => 'Strona glowna'
			);
			
			$aMenu[ 'Menu uzytkownika' ][] = array();
			
			$aMenu[ 'Menu uzytkownika' ][] = array(
				'sTarget' => '/catering/enrol/',
				'sText' => $this->getLang( 'catering.enrol' )
			);
			
			$aMenu[ 'Menu uzytkownika' ][] = array();
			
			$aMenu[ 'Menu uzytkownika' ][] = array(
				'sTarget' => '/user/settings/',
				'sText' => 'Ustawienia'
			);
			
			$aMenu[ 'Menu uzytkownika' ][] = array();
			
			$aMenu[ 'Menu uzytkownika' ][ 'login' ] = array(
				'sTarget' => '/user/logout/',
				'sText' => $this->getLang( 'user.logout' )
			);
			
			$this->sRole = $this->oCurrentUser->get( 'role_id' )->name;
			
			switch ( $this->sRole ) {
				
				case 'admin' :
					
					$sTitle = $this->getLang( 'account.administration' );
					
					$aMenu[ $sTitle ][] = array(
						'sTarget' => '/account/',
						'sText' => $this->getLang( 'account.settings' )
					);
					
					$aMenu[ $sTitle ][] = array();
					
					$aMenu[ $sTitle ][] = array(
						'sTarget' => '/account/user/',
						'sText' => $this->getLang( 'account.add_user' )
					);
					
					$aMenu[ $sTitle ][] = array();
					
					$aMenu[ $sTitle ][] = array(
						'sTarget' => '/account/users/',
						'sText' => $this->getLang( 'account.user_list' )
					);
					
				case 'moderator' :
					
					$sTitle = $this->getLang( 'account.dinners_menu' );
					
					$aMenu[ $sTitle ][] = array(
						'sTarget' => '/account/types/',
						'sText' => 'Typy skladnikow'
					);
					
					$aMenu[ $sTitle ][] = array();
					
					$aMenu[ $sTitle ][] = array(
						'sTarget' => '/account/course/',
						'sText' => 'Dodaj skladnik'
					);
					
					$aMenu[ $sTitle ][] = array();
					
					$aMenu[ $sTitle ][] = array(
						'sTarget' => '/account/courses/',
						'sText' => 'Lista skladnikow'
					);
					
					$aMenu[ $sTitle ][] = array();
					
					$aMenu[ $sTitle ][] = array(
						'sTarget' => '/account/meals/',
						'sText' => 'Dania'
					);
					
					$aMenu[ $sTitle ][] = array();
					
					$aMenu[ $sTitle ][] = array(
						'sTarget' => '/account/orders/',
						'sText' => 'Edycja zamowien'
					);
					
					$sTitle = $this->getLang( 'account.summary_menu' );
					
					$aMenu[ $sTitle ][] = array(
						'sTarget' => '/account/summary/users/',
						'sText' => 'Kosztow na dzien'
					);
					
					$aMenu[ $sTitle ][] = array();
					
					$aMenu[ $sTitle ][] = array(
						'sTarget' => '/account/summary/',
						'sText' => 'Zamowienia'
					);
					
					break;
				
			}
			
		}
		
		// set images for sorter
		Module_Sorter::setImageUrl( 'arrow-sort-up.gif', Module_Sorter::SORT_ASC );
		Module_Sorter::setImageUrl( 'arrow-sort-down.gif', Module_Sorter::SORT_DESC );
		
		$this->mTemplate->aMeta = array();

		$this->mTemplate->title = 'Catering';
		
		$this->mTemplate->menu = $aMenu;
		
	}
	
	public function indexAction() {
		$this->redirect( '/' );
	}
	
	protected function showOrdersForToday() {
		
		// sprawdzamy czy pobieralismy juz info
		$aMealName = $this->oCurrentUser->propertie( 'aMealName' );
		
		if ( empty( $aMealName ) ) {
		
			$oOrder = new Model_Order();
			
			// pobieramy posilek na dzisiaj
			$aOrder = $oOrder->getOrderForToday( (int) $this->oCurrentUser->user_id );
			
			$aName = array();
			foreach ( $aOrder as $aIngredient ) {
				$aName[] = $aIngredient[ 'name' ];
				$sMealName = $aIngredient[ 'meal_name' ];
			}
			
			if ( ! empty( $aName ) ) {
				$sShortName = $sMealName . '. ' . $aName[0];
				$sShortName = ( strlen( $sShortName ) > 43 ? substr( $sShortName, 0, 40 ) . '...' : $sShortName );
				$aMealName = array(
					'sDinnersFullName' => implode( ', ', $aName ),
					'sText' => $this->getLang( 'catering.welcome_text_meal', array( $this->oCurrentUser->fname, $sShortName ) )
				);
			} else {
				$aMealName = array(
					'sDinnersFullName' => '',
					'sText' => $this->getLang( 'catering.welcome_text', $this->oCurrentUser->fname )
				);
			}
			
			// zapisujemy wlasciwosc w obiekcie zalogowanego uzytkownika 
			$this->oCurrentUser->propertie( 'aMealName', $aMealName );
			
		}
		
		$this->mTemplate->aWelcomeMessage = $aMealName;
		
	}
	
	
	protected function getMealsForWeek( $iAccountId, $iTime, $sGroupBy = 'meal_id' ) {
		
		$oMeal = new Model_Meal();
		
		$iDay = date( 'j', $iTime ); 
		$iMonth = date( 'n', $iTime ); 
		$iYear = date( 'Y', $iTime );
			
		
		$iWeekDay = ( date( 'w', $iTime ) == 0 ? 7 : date( 'w', $iTime ) );
		
		
		// obliczamy poczatek tygodnia
		$iStartTime = mktime( 0, 0, 0, $iMonth, $iDay - ( $iWeekDay - 1 ) , $iYear );
		$sStartDate = date( 'Y-m-d', $iStartTime );
		
		// obliczamy koniec tygodnia
		$iEndTime = mktime( 0, 0, 0, $iMonth, $iDay + ( 7 - $iWeekDay ) , $iYear );
		$sEndDate = date( 'Y-m-d', $iEndTime );
		
		
		return $oMeal->getMeals( $iAccountId, $sStartDate, $sEndDate, $sGroupBy ); 
		
	}
	
	protected function getMealsForDay( $iAccountId, $iYear, $iMonth, $iDay ) {
		
		$oMeal = new Model_Meal();
		
		$sStart = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, $iMonth, $iDay, $iYear ) );
		$sEnd = date( 'Y-m-d H:i:s', mktime( 23, 59, 59, $iMonth, $iDay, $iYear ) );
		
		return $oMeal->getMeals( $iAccountId, $sStart, $sEnd );
		
	}
	
	protected function clearWeek( $aWeek ) {
		
		$aNewWeek = array();
		
		foreach( $aWeek as $sDay ) {
			if( ! empty( $sDay ) ) {
				$aNewWeek[] = $sDay;
			}
		}
		
		return $aNewWeek;
		
	}
	
	/**
	 * Zwraca tablice z danymi o miesiacu
	 * 
	 * @param int iUnixTime - czas który znajduje sie w danym miesiacu
	 * @param int iWeekNr - gdy chcemy pobrac dane dot danego tyg
	 * 
	 * @return array - dane o miesiacu lub tygodniu
	 */
	protected function getMonthData($iUnixTime, $iWeekNr = null ) {
		
		// miesiac
		$m = date('n',$iUnixTime);
		// rok
		$y = date('Y',$iUnixTime);
		
		$iIndex = 0;
		$aMonth = array();
	
	 
		$j = date('j',$iUnixTime);
		$w = date('w', mktime(0, 0, 0, $m, 1, $y));
		if ($w == 0) $w = 7;
		for ($nr = 0, $cnt = date('t',$iUnixTime) + $w - 1; $nr < $cnt; $nr++) {
			if ($nr - $w + 2 <= 0) {
				$aMonth[$iIndex][] = '';
				continue;
			}
			
			if ($nr % 7 == 0) {
				if ( isset( $iWeekNr ) AND $iWeekNr == $iIndex ) {
					break;
				}
				$iIndex++;
			}
			$aMonth[$iIndex][] = ($nr - $w + 2);
		}
		
		// protection from case when user want to get last week
		if ( isset( $iWeekNr ) ){
			return $aMonth[$iWeekNr];
		}
		
		// fill rest of array with empty values
		if ( count($aMonth[$iIndex]) < 7 ) {
			$aMonth[$iIndex] = array_pad( $aMonth[$iIndex], 7, '');
		}
		
		return $aMonth;
	}
	
	protected function generateCalendarData( $sBaseLink, $aRequestParams ) {
		
		$iTime = time();
		$iYear = date( 'Y', $iTime ); 
		$iMonth = date( 'n', $iTime ); 
		
		if ( isset( $aRequestParams[0] ) ) {
			
			switch ( $aRequestParams[0] ) {
				
				case 'day':
					$iYear = (int) $aRequestParams[1];
					$iMonth = (int) $aRequestParams[2];
					$iSelectedDay = (int) $aRequestParams[3];
					$iTime = mktime( 0, 0, 0, $iMonth, $iSelectedDay, $iYear );
					break;
				case 'week':
					$iYear = (int) $aRequestParams[1];
					$iMonth = (int) $aRequestParams[2];
					$iSelectedWeek = (int) $aRequestParams[3];
					$iTime = mktime( 0, 0, 0, $iMonth, 1, $iYear );
					break;
				case 'month':
					$iYear = (int) $aRequestParams[1];
					$iMonth = (int) $aRequestParams[2];
					$iTime = mktime( 0, 0, 0, $iMonth, 1, $iYear );
					break;
				
			}
			
		}
		
		// sterowanie kalendarzem
		$iYearPrev = $iYearNext = $iYear;
		$iMonthPrev = $iMonth - 1;
		if( $iMonthPrev < 1 ) {
			$iMonthPrev = 12;
			$iYearPrev = $iYear - 1;
		}
		$iMonthNext = $iMonth + 1;
		if( $iMonthNext > 12 ) {
			$iMonthNext = 1;
			$iYearNext = $iYear + 1;
		}
		
		$aMonth = $this->getMonthData( $iTime );
		$aCalendar = array();
		$iWeek = 0;
		$iWeekIndex = 0;
		foreach ( $aMonth as $aWeek ) { 
			foreach ( $aWeek as $iDayIndex => $sDay ) { 
				if ( ! empty( $sDay ) ) {
					$aCalendar[ $iWeekIndex ][ $iDayIndex ][ 'sLink' ] = $sBaseLink . 'day/' . $iYear . '/' . $iMonth . '/' . $sDay . '/';
					$aCalendar[ $iWeekIndex ][ $iDayIndex ][ 'sText' ] = $sDay;
					if ( ( isset( $iSelectedWeek ) && ( $iWeekIndex == $iSelectedWeek ) ) || ( isset( $iSelectedDay ) && ( $iSelectedDay == $sDay ) ) ) 
						$aCalendar[ $iWeekIndex ][ $iDayIndex ][ 'iClass' ] = 0;
				} else {
					$aCalendar[ $iWeekIndex ][] = '';
				}
			}
			$aCalendar[ $iWeekIndex ][] = array( 'sLink' => $sBaseLink . 'week/' . $iYear . '/' . $iMonth . '/' . $iWeekIndex . '/', 'sText' => 'week' );
			$iWeekIndex++;
		}
		
		// dane do kalendarza
		$aCalendarData = array(
			'sPrevLink' => $sBaseLink . 'month/' . $iYearPrev . '/' . $iMonthPrev . '/'
			, 'sNextLink' => $sBaseLink . 'month/' . $iYearNext . '/' . $iMonthNext . '/'
			, 'aCalendar' => $aCalendar
			, 'sMonthName' => $this->getLang( 'catering.month_names[' . ( $iMonth - 1 ) . ']' ) . ' ' . $iYear
			, 'aWeekDays' => $this->getLang( 'catering.short_week_days' )
		);
		
		return $aCalendarData;
		
	}
	
	public function preDispatch() {
		
		if ( ( $this->oXajax instanceof LXajax ) && ( $this->oXajax->isFunctionRegistered() ) ){
			$this->oXajax->configure('javascript URI', $this->oLithium->getExternalFilePath( '', 'js' ) );
			$this->mTemplate->sAdditionalHeadData = $this->oXajax->getJavascript();
		}
		
	}
	
	public function postDispatch() {
		
		if ( ! IN_PRODUCTION ) {
	
			$oDB = Database::getInstance();
			echo 'Query count: ' . $oDB->getQueryCount(), '<br />';
			$aHistory = $oDB->getQueryHistory();
			foreach( $aHistory as $sSql ) {
				echo $sSql, '<br />';
			}
		
		}
		
	}
	
	/**
	 * Check is it ajax call
	 * 
	 * @return bool
	 */
	protected function isAjaxCall() {
		
		static $bIsAjaxCall;
		
		// rest of code can be executed only one time
		if ( ! is_null( $bIsAjaxCall ) ) return $bIsAjaxCall;
		
		$oXajax = new LXajax();
		
		if ( ! $oXajax->canProcessRequest() ) return $bIsAjaxCall = false;
		
		$this->registerXajaxFunctions( $oXajax );
		
		$oXajax->processRequest();
		
		return $bIsAjaxCall = true;
		
	}
	
	/**
	 * Register all xajax functions from current controller
	 * 
	 * @param LXajax - instance of LXajax object
	 * @return void
	 */
	protected function registerXajaxFunctions( $oXajax ) {
		
		// register all available xajax functions
		foreach ( get_class_methods( $this ) as $sFunction ) {
			if ( preg_match( '!Ajax$!', $sFunction ) ) {
				$oXajax->register( array( $sFunction, $this, $sFunction ) );
			}
		}
		
	}
	
}

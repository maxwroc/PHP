<?php 

class Abstract_Blog extends Core_Controller {
	
	/**
	 * Obiekt użytkownika
	 */
	protected $oUser;
	
	/**
	 * Moduł autentykacji
	 */
	protected $oAuth;
	
	/**
	 * Sprawdza czy obecny uzytkownik jest zalogowany
	 */
	protected function isLoggedIn() {
		return $this->oAuth->isLoggedIn();
	}
	
	/**
	 * Zwraca obecnie zalogowanego uzytkownika
	 * 
	 * W praypadku gry uzytkownik nie jest zalogowany zwraca false
	 * 
	 * @return Model_User/bool
	 */
	protected function getCurrentUser() {
		if ( ! empty( $this->oUser ) ) {
			return $this->oUser;
		}
		
		if ( $this->isLoggedIn() ) {
			$this->oUser = $this->oAuth->getLoggedInUser();
			return $this->oUser;
		}
		
		return false;
	}
	
	/**
	 * Inicjalizacja kontrolera
	 */
	public function init() {
		$this->setLayoutDir( 'mint' );
		$this->setTemplate( 'master' );
		
		// Inicjalizacja moduły autentykacji
		$this->oAuth = $this->getModule( 'auth' );
		
		// Ustawianie danych w widoku
		$oView = $this->getTemplate();
		$oView->sPageTitle = 'blog chodorowski.co';
		
		$oView->oMenu = $this->getMenuView();
		
		parent::init();
	}
	
	/**
	 * Zwraca widok dla menu
	 */
	protected function getMenuView() {
		
		$oView = $this->getView( 'menu' );
		
		if ( $this->isLoggedIn() ) { // Wylogowywanie
			$oView->sAuthUrl = $this->getPageUrl( '/auth/logout' );
			$oView->sAuthText = $this->getLang( 'blog.logout' );
		} else { // Zalogowanie
			$oView->sAuthUrl = $this->getPageUrl( '/auth/login' );
			$oView->sAuthText = $this->getLang( 'blog.login' );
		}
		
		return $oView;
	}
	
	/**
	 * Domyślna akcja dla wszystkich kontrolerów
	 */
	public function indexAction() {
		$this->getTemplate()->mContent = '';
	}
	
}
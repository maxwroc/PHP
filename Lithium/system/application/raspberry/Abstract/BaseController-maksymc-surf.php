<?php

abstract class Abstract_BaseController extends Core_Controller {

	public $mTemplate = 'master';
	
	protected $oAuth;
	protected $oXajax;
	
	public function init() {
	
		$this->oAuth = new Module_Auth();
		
		if ( $iSessionTimeout = $this->oLithium->getConfig( 'General.Session_timeout' ) ) {
			$this->oAuth->setTimeout( $iSessionTimeout );
		}
		
		// load xajax lib
		Loader::loadClass( 'Library_Xajax', 'LXajax' );
		
		$this->oXajax = new LXajax();
		$this->oXajax->registerXajaxFunctions( $this );
		
		// if xajax call end executing rest of code
		if ( $this->isAjaxCall() ) {
			parent::init();
			return;
		}
		
		// sprawdzanie czy uzytkownik niezalogowany
		if ( !$this->isUserAllowed() && $this->getName() != "Login" ) {
			$this->redirect( '/login' );
			return;
		}
		
		parent::init();
		
		$this->mTemplate->headers = $this->oXajax->getJavascript();
		$this->mTemplate->menu = $this->getMenu();
		$this->mTemplate->aResources = [];
	}
	
	public function indexAction() {
		$this->redirect( '/login' );
	}
	
	protected function isAjaxCall() {
		
		static $bIsAjaxCall;
		
		// rest of code can be executed only one time
		if ( ! is_null( $bIsAjaxCall ) ) return $bIsAjaxCall;
		
		$oXajax = new LXajax();
		
		if ( ! $oXajax->canProcessRequest() ) return $bIsAjaxCall = false;
		
		$oXajax->processRequest();
		
		return $bIsAjaxCall = true;
		
	}
	
	protected function isUserAllowed() {
		return $this->oAuth->isLoggedIn() || strpos( $_SERVER['REMOTE_ADDR'], '192.168.' ) === 0;
	}
	
	private function getMenu() {
	
		if ( ! $this->isUserAllowed() ) {
			return '';
		}
		
		$sActionName = substr( $this->oRouter->getFunctionName(), 0, -6 );
	
		$aMenu = [];
		
		$aMenu[] = array(
			'text' => 'service',
			'link' => '/raspberry/service'
		);
		$aMenu[] = array(
			'text' => 'logs',
			'link' => '/logs'
		);
		$aMenu[] = array(
			'text' => 'performance',
			'link' => '/raspberry/performance'
		);
		$aMenu[] = array(
			'text' => 'Deluge',
			'link' => strpos( $_SERVER['REMOTE_ADDR'], '192.168.' ) === 0 ? 'http://192.168.1.4:8112/' : 'http://magmax.dnsd.me:8564'
		);
		
		foreach ( $aMenu as $iKey => $aLink ) {
			$aMenu[ $iKey ][ 'active' ] = $sActionName == $aLink[ 'text' ];
		}
		
		return View::factory( 'menu', [ 'aMenu' => $aMenu ] );
	}
}
<?php

class Controller_Login extends Abstract_BaseController {
	
	public function indexAction() {
		
		if ( $this->oAuth->isLoggedIn() ) {
			$this->redirect( '/' );
			return;
		}
		
		$this->mTemplate->content = View::factory( 'login/login' );
	}
	
	public function loginAjax( $aData ) {
		
		$oResp = new xajaxResponse();
		
		// walidacja danych
		$oValidator = new Module_Validator();
		$oValidator->field( 'type_id', $iTypeId )->rules( 'required|toint|not[0]' );
		$oValidator->field( 'type_name', $sValue )->rules( 'required|hsc' );
		
		if ( $oValidator->validate() ) {
			
			
			
		} else {
			
		}
		
		$oUser = Model_User::tryCreate( $aData[ 'login' ] );
		
		$sPassHash = md5( $aData[ 'password' ] . 'fibonacci98765434567' );
		
		if ( $oUser !== null && $this->oAuth->login( $oUser, $sPassHash ) ) {
			$oResp->redirect( $this->getPageUrl( '/' ) );
		}
		else {
			$oResp->assign( 'error_msg', 'innerHTML', 'Incorrect name or password' );
		}
		
		return $oResp;
	}
}
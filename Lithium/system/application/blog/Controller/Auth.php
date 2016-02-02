<?php 

/**
 * Kontroler autentykacji uzytkownika
 */
class Controller_Auth extends Abstract_Blog {
	
	private $sPasswordSalt = '~!@#$%^&*(*&^%$#@!WEGF';
	
	/**
	 * Formularz logowania oraz proces logowania
	 */
	public function loginAction() {
		
		// Sprawdzamy czy uzytkownik juz nie jest zalogowany
		if ( $this->isLoggedIn() ) {
			return $this->redirect( '/' );
		}
		
		$aViewData['sLoginLabel'] 		= $this->getLang( 'email' );
		$aViewData['sPasswordLabel'] 	= $this->getLang( 'password' );
		$aViewData['sSubmitLabel'] 		= $this->getLang( 'login' );
		
		// Uzytkownik probuje sie zalogowac
		if ( ! is_null( $this->post( 'email' ) ) && ! is_null( $this->post( 'password' ) ) ) {
			
			$mLoginResult = $this->validateLoginData();
			if ( $mLoginResult === true ) {
					return $this->redirect( '/' );
			} else {
				if ( is_array( $mLoginResult ) ) {
					$aViewData['aErrorMessages'] = array();
					foreach ( $mLoginResult as $aError ) {
						$aViewData['aErrorMessages'][] = $this->getLang( $aError['msg'], $aError['field_name'] );
					}
				} elseif ( $mLoginResult === false ) {
					$aViewData['aErrorMessages'][] = $this->getLang( 'login_failed' );
				}
			}
			
		}
		
		$this->getTemplate()->mContent = $this->getView( 'auth/login', $aViewData );
	}
	
	/**
	 * Sprawdza poprawność wprowadzonych danych do formularza logowania
	 * oraz przeprowadza proces logowania
	 * 
	 * @return bool/array
	 */
	private function validateLoginData() {
		
		$sEmail = $this->post( 'email' );
		$sPass = $this->post( 'password' );
		
		// Dodajemy sól do hasła
		$sPass .= $this->sPasswordSalt;
		
		$oValidator = $this->getModule( 'validator' );
		$oValidator->field( 'email', $sEmail, $this->getLang( 'email' ) )->rules( 'required|email' );
		$oValidator->field( 'pass', $sPass, $this->getLang( 'password' ) )->rules( 'required|md5' );
		
		if ( $oValidator->validate() ) {
			
			// Probujemy pobrac z bazy uzytkownika z zadanymi kryteriami
			$oUser = $this->getModel( 'user' )->where( 'email', $sEmail )->where( 'password', $sPass );
			$aResult = $oUser->getRow();
			if ( $aResult === false ) {
				return false;
			}
			
			// Sprawdzanie poprawnosci hasla
			if ( ! $this->oAuth->login( $oUser, $sPass ) ) {
				return false;
			}
			
			//TODO Aktualizacja czasu ostatniego logowania
//			$oUser->date_login = date( 'Y-m-d' );
//			$oUser->save();
			
			return true;
			
		} else {
			// Zwracamy tablicę błędów
			return $oValidator->getError();
		}
	}
	
	/**
	 * Akcja wylogowuje uzytkownika i przekierowuje go na strone główną
	 */
	public function logoutAction() {
		$this->oAuth->logout();
		return $this->redirect( '/' );
	}
	
}

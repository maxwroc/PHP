<?php
class Controller_User extends Abstract_Catering {
	
	public function loginAction() {

		if ( isset( $_POST[ 'remind' ] ) ) {
			$this->remindPassword();
			return;
		}
		
		$this->mTemplate->title = 'Catering - Logowanie';
		$this->mTemplate->sSectionTitle = 'Logowanie';
		
		
		if ( $this->oAuth->isLoggedIn() ) {
			$this->redirect( '/' );
		} else {
			
			$sUser = $this->post( 'user' );
			$sPass = $this->post( 'pass' );
			
			$oValidator = new Module_Validator();
			$oValidator->field( 'email', $sUser, $this->getLang( 'user.email' ) )->rules( 'required|email' );
			$oValidator->field( 'password', $sPass, $this->getLang( 'user.password' ) )->rules( 'required|md5' );
			
			
			if ( isset( $_POST[ 'submit' ] ) ) {
				
				if ( $oValidator->validate() ) {
				
					$oUser = new Model_User();
					
					$aUser = $oUser->where( 'email', $sUser )->getRow();
					
					if ( is_array( $aUser ) && isset( $aUser[ 'password' ] ) ) {
						
						if ( $this->oAuth->login( $oUser, $sPass ) ) {
							$this->redirect( '/' );
						}
						
					}
					
					$error = $this->getLang( 'user.incorrect_username_or_password' );
				} else {
					$aErrors = $oValidator->getError();
					$error = 'Blad danych wejsciowych.';
					foreach( $aErrors as $sField => $aError ) {
						$error .= '<br />' . $this->getLang( $aError['msg'], $aError['field_name'] ) ;
					}
				}
				
			}
			
			// generate form
			$aData = array(
				'label_user' 		=> $this->getLang( 'user.email' ),
				'label_pass' 		=> $this->getLang( 'user.password' ),
				'user'				=> $sUser,
				'pass' 				=> '',
				'submit' 			=> $this->getLang( 'user.login' ),
				'remind' 			=> 'Nowe haslo',
				'error'				=> isset( $error ) ? $error : null
			);
			
			
			$this->mTemplate->content = View::factory( 'user/login_form', $aData )->render();
			
		}
				
	}
	
	public function logoutAction() {
		
		
		if ( $this->oAuth->logout() ) {
			$this->redirect( '/' );
		}
		
		$this->mTemplate->content = $this->getLang( 'user.logout_problems' );
		
	}
	
	public function registerAction() {
		
		if ( $this->oAuth->isLoggedIn() ) {
			$this->redirect( '/' );
			return;
		}
		
		$this->mTemplate->title = $this->getLang( 'title_registration' );
		$this->mTemplate->sSectionTitle = $this->getLang( 'title_registration' );
		
		$sUser = $this->post( 'user_name' );
		$sPass = $this->post( 'user_pass' );
		$sEmail = $this->post( 'user_email' );
		$sAccount_name = $this->post( 'account_name' );
		
		$oValidator = new Module_Validator();
		$oValidator->field( 'nick', $sUser, $this->getLang( 'user.nick' ) )->rules( 'required' );
		$oValidator->field( 'password', $sPass, $this->getLang( 'user.password' ) )->rules( 'required|md5' );
		$oValidator->field( 'email', $sEmail, $this->getLang( 'user.email' ) )->rules( 'required|email' );
		$oValidator->field( 'account_name', $sAccount_name, $this->getLang( 'user.account_name' ) )->rules( 'required' );
		
		if ( isset( $_POST[ 'submit' ] ) ) {
		
			if ( $oValidator->validate() ) {
				
				// sprawdzamy czy nie ma juz takiego konta lub usera
				$oUser = new Model_User();
				
				$aRes = $oUser->where( 'email', $sEmail )->getRow();
				
				if ( empty( $aRes ) ) {
			
					$oUser->reset();
					
					$oAccount = new Model_Account();
						
					$oAccount->name = $sAccount_name;
					
					if ( $iAccountId = $oAccount->save() ) {
						
						$oUser->name = $sUser;
						$oUser->email = $sEmail;
						$oUser->password = $sPass;
						$oUser->role_id = 1;
						$oUser->account_id = $iAccountId;
						
						if ( $oUser->save() ) {
							$this->redirect( '/user/login/' );
						} else {
							$error = $this->getLang( 'failed_creating_user' );
						}
						
					} else {
						$error = $this->getLang( 'failed_creating_account' );
					}
				
				} else {
					$error = $this->getLang( 'user_already_exists' );
				}
				
				
			} else {
				
				$error = 'Blad danych wejsciowych.';
				$aErrors = $oValidator->getError();
				foreach( $aErrors as $sField => $aError ) {
					$error .= '<br />' . $this->getLang( $aError['msg'], $aError['field_name'] ) ;
				}
				
			}
		
		}
		
		// generate form
		$aData = array(
			'label_user' 		=> $this->getLang( 'user.nick' ),
			'label_pass' 		=> $this->getLang( 'user.password' ),
			'label_email' 		=> $this->getLang( 'user.email' ),
			'label_accountname' => $this->getLang( 'user.account_name' ),
			'user_name'			=> $sUser,
			'user_pass' 		=> '',
			'user_email' 		=> $sEmail,
			'account_name' 		=> $sAccount_name,
			'submit' 			=> $this->getLang( 'user.register' ),
			'error'				=> isset( $error ) ? $error : null
		);
		
		$this->mTemplate->content = View::factory( 'user/registration_form', $aData )->render();
		
	}
	
	protected function remindPassword() {
		
		// TODO Przypominanie hasla
		
		$this->mTemplate->content = 'dziala';
		
	}
	
	public function settingsAction() {
		
		// sprawdzamy czy zalogowany
		if ( ! $this->oAuth->isLoggedIn() ) {
			$this->redirect( '/' );
			echo ' ';
			return;
		}
		
		$this->mTemplate->sSectionTitle = 'Ustawienia';
		
		$oLayout = new Model_Layout();
		
		$aLayouts = $oLayout->getAll();
		
		if ( isset( $_POST[ 'submit' ] ) ) {
			$sResult = $this->saveSettings( $aLayouts );
			if ( $sResult === true ) {
				$aData[ 'sInfo' ] = 'Ustawienia zapisane pomyslenie.';
			} else {
				$aData[ 'sInfo' ] = $sResult;
			}
		}
		
		$aOption = array();
		
		foreach ( $aLayouts as $aLayout ) {
			$aOption[] = array(
				'value' => $aLayout[ 'layout_id' ],
				'text' => $aLayout[ 'name' ]
			);
		}
		
		$aData[ 'aChangePassForm' ] = array(
			'sTitle' => 'Zmiana hasla',
			'sOldPass' => 'Stare haslo',
			'sNewPass' => 'Nowe haslo',
			'sNewPassConfirm' => 'Powtorz nowe haslo',
			'sSubmit' => 'Zmien'
		);
		
		$aData[ 'aLayoutForm' ] = array(
			'sTitle' => 'Wybor layout\'u',
			'sNull' => '',
			'value' => (int) $this->oCurrentUser->layout_id ,
			'sSubmit' => 'Zapisz',
			'aOptions' => $aOption
		);
		
		$this->mTemplate->content = View::factory( 'user/settings', $aData )->render();
		
	}
	
	protected function saveSettings( $aAvailableLayouts ) {
		
		
		if ( isset( $_POST[ 'layout' ] ) ) {
			$iLayout = (int) $this->post( 'layout' );
			
			$oUser = new Model_User();
			$oUser->user_id = (int) $this->oCurrentUser->user_id;
				
			if ( $iLayout != 0 ) {
				$oUser->layout_id = $iLayout;
			} else {
				$oUser->layout_id = 1;
			}
			
			if ( $oUser->save() ) {
				
				// zmiana widoku obecnie zalogowanego uzytkownika
				foreach ( $aAvailableLayouts as &$aLayout ) {
					if ( $aLayout['layout_id'] == $oUser->layout_id ) {
						$this->oCurrentUser->propertie( 'template', $aLayout[ 'path' ] );
						break;
					}
				}
				
				$this->oCurrentUser->layout_id = $oUser->layout_id;
				return true;
				
			} else {
				return 'Blad podczas zapisu.';
			}
				
		} elseif ( isset( $_POST[ 'oldpass' ] ) ) {
			
			// sprawdzamy czy stare haslo poprawne
			if ( md5( $_POST[ 'oldpass' ] ) != $this->oCurrentUser->password ) {
				return 'Podano niepoprawne stare haslo.';
			}
			
			// sprawdzamy czy pola nowych hasel sa identyczne
			if ( $_POST[ 'newpass' ] != $_POST[ 'newpassconfirm' ] ) {
				return 'Pola nowe i stare musza byc takie same.';
			}
			
			// sprawdzamy czy nie jest puste
			if ( $_POST[ 'newpass' ] == '' ) {
				return 'Pole nowe haslo nie moze byc puste.';
			}
			
			// zapisujemy
			$oUser = new Model_User();
			$oUser->user_id = (int) $this->oCurrentUser->user_id;
			
			$oUser->password = md5( $_POST[ 'newpass' ] );
			
			if ( $oUser->save() ) {
				return true;
			} else {
				return 'Blad podczas zapisu.';
			}
			
		}
		
	}
	
}

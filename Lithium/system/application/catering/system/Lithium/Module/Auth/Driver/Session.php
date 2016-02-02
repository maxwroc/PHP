<?php
class Module_Auth_Driver_Session extends Module_Auth_Driver {
	
	protected $aUserData;
	
	public function __construct() {
		
		session_start();
		
	}
	
	public function isLoggedIn() {
		
		if ( empty( $_SESSION[ 'auth_ip' ] ) || ( $_SESSION[ 'auth_ip' ] != $_SERVER['REMOTE_ADDR'] ) ) {
			return false;
		}
		
		if ( ! empty( $_SESSION[ 'auth_user' ] ) AND is_object( $_SESSION[ 'auth_user' ] ) ) {
			
			if ( ! empty( $this->iTimeout ) AND ! empty( $_SESSION[ 'auth_time' ] ) ) {
				
				$iDifference = time() - $_SESSION[ 'auth_time' ];
				if ( $this->iTimeout < $iDifference ) {
					$this->logout();
					return false;
				} else {
					$_SESSION[ 'auth_time' ] = time();
				}
				
			}
				
			return true;
			
		} else {
			
			return false;
			
		}
		
	}
	
	public function login( $oUser ) {
		
		if ( ! empty( $this->iTimeout ) ) {
			$_SESSION[ 'auth_time' ] = time();
		}
		
		$_SESSION[ 'auth_user' ] = $oUser;
		$_SESSION[ 'auth_ip' ] = $_SERVER['REMOTE_ADDR'];
		return true;
		
	}

	public function logout() {
		
		if ( isset( $_SESSION[ 'auth_user' ] ) ) {
			unset( $_SESSION[ 'auth_user' ] );
		}
		
		return true;
		
	}
	
	public function getLoggedInUser() {
		
		if ( ! empty( $_SESSION[ 'auth_user' ] ) AND is_object( $_SESSION[ 'auth_user' ] ) ) {
			
			return $_SESSION[ 'auth_user' ];
				
		}
		
	}
	
}
?>
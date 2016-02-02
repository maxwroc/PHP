<?php
class Module_Auth {
	
	const AUTH_DRV_SESSION 	= 1;	// only session
	const AUTH_DRV_DATABASE = 2;	// session and db
	
	protected $oDriver;
	
	public function __construct( $iDriverType = self::AUTH_DRV_SESSION ) {
		
		// loading driver
		$this->loadDriver( $iDriverType );

	}
	
	public function isLoggedIn() {
		return $this->oDriver->isLoggedIn();
	}
	
	public function setTimeout( $iTimeout ) {
		return $this->oDriver->setTimeout( $iTimeout );
	}
	
	public function login( $oUser, $sPass ) {
		
		if ( $oUser->password === $sPass ) {
			
			return $this->oDriver->login( $oUser );
			
		} else {
			
			$this->oDriver->logout();
			return false;
			
		}
		
	}
	
	public function logout() {
		return $this->oDriver->logout();
	}
	
	protected function loadDriver( $iDriverType ) {
		
		switch ( $iDriverType ) {
			
			case self::AUTH_DRV_DATABASE :
				throw new Lithium_Exception( 'Core.module_exception', 'Database driver not implemented yet.' );
				break;
				
			case self::AUTH_DRV_SESSION :
			default:
				$this->oDriver = new Module_Auth_Driver_Session();
				
		}
		
	}
	
	public function getLoggedInUser() {
		return $this->oDriver->getLoggedInUser();
	}
	
	public function getSecurityToken() {
		
		$sToken = md5( rand() );
		
		$_SESSION[ 'token' ] = $sToken;
		
		return $sToken;
		
	}
	
	public function isValidToken( $sToken ) {
		
		if ( ! isset( $_SESSION[ 'token' ] ) ) return false;
		
		if ( $_SESSION[ 'token' ] != $sToken ) return false;
		
		unset( $_SESSION[ 'token' ] );
		
		return true;
		
	}
	
}
?>
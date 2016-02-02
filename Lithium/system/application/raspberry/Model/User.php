<?php

class Model_User {

	public $login;
	public $password;
	
	private function __construct( $sLogin, $sPassword ) {
		$this->login = $sLogin;
		$this->password = $sPassword;
	}
	
	public static function tryCreate( $sLogin ) {
	
		$oLithium = Lithium::getInstance();
		
		$aUsers = $oLithium->getConfig( 'users' );
		
		if ( $aUsers == null || $aUsers[ $sLogin ] == null ) {
			return null;
		}
		
		return new Model_User( $sLogin, $aUsers[ $sLogin ] );
	}
}
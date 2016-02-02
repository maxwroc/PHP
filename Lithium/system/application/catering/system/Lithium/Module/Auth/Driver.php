<?php
abstract class Module_Auth_Driver {
	
	protected $iTimeout = 0;
	
	public abstract function isLoggedIn();
	
	public abstract function login( $oUser );

	public abstract function logout();
	
	public function setTimeout( $iSeconds ) {
		$this->iTimeout = (int) $iSeconds;
	}
	
}
?>
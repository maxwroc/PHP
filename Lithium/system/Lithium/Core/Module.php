<?php 

abstract class Core_Module {
	
	/**
	 * Lithium instance
	 * 
	 * @var Lithium
	 */
	protected static $oLithium;
	
	/**
	 * Setter for Lithium instance
	 * 
	 * @param Lithium $oLithium
	 */
	public static function setLithium( Lithium $oLithium ) {
		self::$oLithium = $oLithium;
	}
	
	/**
	 * Lithium raiseWarrning wrapper method
	 * 
	 * @param string $sMessage
	 * @param int $iWarningLevel
	 */
	protected function raiseWarrning( $sMessage, $iWarningLevel = 0 ) {
		self::$oLithium->riseWarning( $sMessage, $iWarningLevel );
	}
	
}
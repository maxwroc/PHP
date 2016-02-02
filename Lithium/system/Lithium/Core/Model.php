<?php 

/**
 * Lithium core model class
 * 
 * @package Lithium
 * @subpackage Core
 */
abstract class Core_Model {
	
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
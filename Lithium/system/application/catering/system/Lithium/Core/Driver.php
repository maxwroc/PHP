<?php
abstract class Core_Driver {
	
	protected $aConfig;
	
	protected $oLink;
	
	/**
	 * Chunk properties
	 */
	protected $iOffset = 0;
	protected $iQuantity = 0;
	
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
	public static function setLithium( $oLithium ) {
		self::$oLithium = $oLithium;
	}
	
	public abstract function connect();
	
	public abstract function query( $sSql, $aParams );
	
	public abstract function getError();
	
	public function setChunkArgs( $iOffset, $iQuantity ) {
		$this->iOffset = $iOffset; 
		$this->iQuantity = $iQuantity;
	}
	
}

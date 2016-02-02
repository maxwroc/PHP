<?php

class Module_Notifications {
	
	/**
	 * Brak zamowien na dany dzien
	 * 
	 * Wykonywane godzine przed koncowa godzina zamowien
	 */
	const NoOrders = 1;
	
	/**
	 * Zmiana zamowienia
	 * 
	 * Wykonywane po zapisie danych do bazy
	 */
	const OrdersChange = 2;
	
	public static function factory( $iNotifType ) {
		
		$oNotification = false;
		
		switch ( $iNotifType ) {
			
			case self::NoOrders :
				$oNotification = new Module_Notifications_Noorders();
				break;
				
			case self::OrdersChange :
				$oNotification = new Module_Notifications_Orderschange();
				break;
				
		}
		
		return $oNotification;
		
	}
	
}


abstract class Module_Notifications_Interface {
	
	protected $sFieldName;
	
	protected $sModelName = 'User';
	protected $sPrimeryKey = 'user_id';
	
	public function __construct() {
		
		if ( ! strstr( $this->sModelName, 'Model_' ) ) {
			$this->sModelName = 'Model_' . $this->sModelName;
		}
		
	}
	
	/**
	 * Ustawia lub usuwa powiadomienie
	 */
	public function setValue( $mRecord, $bValue = true ) {
		
		$bResult = true;
		
		$sClassName = $this->sModelName;
		
		if ( ! $mRecord instanceof Core_Model ) {
			
			if ( is_numeric( $mRecord ) ) {
				$oUpObject = new $sClassName( $mRecord );
				$mRecord = $oUpObject;
			} else {
				throw new Lithium_Exception( 'Newlasciwy argument funkcji' );
			}
			
		} else {
			
			$sPrimeryKey = $this->sPrimeryKey;
//			$oUpObject = new $sClassName( $mRecord->$sPrimeryKey );
			
		}
		
		$sFieldName = $this->sFieldName;
		$sProperty = $mRecord->$sFieldName;
		
			
		if ( (bool) $sProperty != $bValue ) {
			
			if ( ! isset ( $oUpObject ) ) $oUpObject = new $sClassName( $mRecord->$sPrimeryKey );
			
			// zapisywanie nowej wartosci
			$oUpObject->$sFieldName = (int) $bValue;
			
			// update uzytkownika przekazanego jako parametr (np w sesji)
			if ( ( $bResult = $oUpObject->update( $sFieldName ) ) && ( $mRecord instanceof Core_Model ) ) {
				$mRecord->$sFieldName = (int) $bValue;
			}
			
		}
		
		return $bResult;
		
	}
	
	/**
	 * Zwraca wartosc (ustawione: tak lub nie)
	 */
	public function getValue( $mRecord ) {
		
		$sClassName = $this->sModelName;
		
		if ( ! $mRecord instanceof Core_Model ) {
			
			if ( is_numeric( $mRecord ) ) {
				$mRecord = new $sClassName( $mRecord );
			} else {
				throw new Lithium_Exception( 'Newlasciwy argument funkcji' );
			}
			
		}
		
		$sFieldName = $this->sFieldName;
		
		return $mRecord->$sFieldName;
		
	}	
	
	public function getFieldName() {
		return $this->sFieldName;
	}
	
}

?>
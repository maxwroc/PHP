<?php 

class Module_Reservation {
	
	/**
	 * Bazowy url
	 * @var string
	 */
	private $sBaseUrl;
	
	private $aDays = array();
	
	private $iTimeStart;
	private $iTimeEnd;
	
	private $aFoundedResults = array();
	
	public function setBaseUrl( $sBaseUrl ) {
		$this->sBaseUrl = $sBaseUrl;
	}
	
	public function setDatePeriod( LDateTime $oStart, LDateTime $oEnd ) {
		
		$this->aDays[] = $oStart;
		
		$oNextDay = clone $oStart;
		// Dodajemy dni z pomiędzy przekazanych dat do tablicy
		while ( $oNextDay->isLowerThen( $oEnd ) ) {
			$oNextDay->change( '+1 day' );
			$this->aDays[] = clone $oNextDay;
		}
	}
	
	public function setTimePeriod( $iStart, $iEnd ) {
		$this->iTimeStart = $iStart;
		$this->iTimeEnd = $iEnd;
	}
	
	public function isFree() {
		// Sprawdzamy dzien po dniu 
		foreach ( $this->aDays as $oDay ) {
			$oParsedDay = $this->checkDay( $oDay );
			if ( $oParsedDay->isFree() ) {
				$this->aFoundedResults = array_merge( $this->aFoundedResults, $oParsedDay->getResults() );
			}
		}
		
		return count( $this->aFoundedResults ) > 0;
	}
	
	/**
	 * Zwraca datę i czas znalezionego wolnego terminu
	 * 
	 * @return array of Module_FreeCourt
	 */
	public function getResults() {
		return $this->aFoundedResults;
	}
	
	public function doReservation( LDateTime $oDate ) {
		// wykonanie rezerwacji dla zachowanych danych
	}
	
	private function checkDay( LDateTime $oDay ) {
		$oParser = new Module_Parser_Wroclaw( $this->sBaseUrl, $oDay );
		$oParser->setTimePeriod( $this->iTimeStart, $this->iTimeEnd );
		return $oParser;
	}
	
}

class Module_FreeCourt {
	
	private $oDTime;
	
	private $sCourt;
	
	public function __construct( LDateTime $oDTime, $sCourt ) {
		$this->oDTime = $oDTime;
		$this->sCourt = $sCourt;
	}
	
	public function __toString() {
		return sprintf( 'Czas: %s, Kort: %d', $this->oDTime, $this->sCourt );
	}
	
}
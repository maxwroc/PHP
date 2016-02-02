<?php 

abstract class Module_Parser {
	
	protected $sBaseUrl;
	protected $oDay;
	
	private $iCorts = 4;
	
	protected $iTimeStart;
	protected $iTimeEnd;
	
	protected $oFoundedResults;
	
	public function __construct( $sBaseUrl, $oDay ) {
		$this->sBaseUrl = $sBaseUrl;
		$this->oDay = $oDay;
	}
	
	public function isFree() {
		
		$this->oFoundedResults = array();
		
		$this->oXpath = new DOMXPath( $this->loadPage() ); 
		
		foreach ( $this->getDayObjectsForHours() as $oHour ) {
			for ( $iCourt = 1; $iCourt <= $this->iCorts; $iCourt++) {
				$sId = $this->getElementId( $oHour, $iCourt );
				
				if ( $this->isHourFree( $this->getClassNameFromElement( $sId ) ) ) {
					$this->oFoundedResults[] = new Module_FreeCourt( $oHour, $iCourt );
				}
			}
		}
		
		return count( $this->oFoundedResults ) > 0;
	}
	
	private function getDayObjectsForHours() {
		$aList = array();
		
		for( $iHour = $this->iTimeStart; $iHour <= $this->iTimeEnd; $iHour++ ) {
			$aList[] = new LDateTime( sprintf( '%s %d:00', $this->oDay->format( 'Y-m-d' ), $iHour ) );
		}
		
		return $aList;
	}
	
	protected abstract function getElementId( $oHour, $iCourt );
	
	protected abstract function isHourFree( $sClassName );
	
	protected abstract function buildUrl();
	
	public function getResults() {
		return $this->oFoundedResults;
	}
	
	public function setTimePeriod( $iStart, $iEnd ) {
		$this->iTimeStart = $iStart;
		$this->iTimeEnd = $iEnd;
	}
	
	protected function loadPage() {
		
		$bOldSetting = libxml_use_internal_errors( true ); 
		libxml_clear_errors();
		 
		$oHtml = new DOMDocument();
		$oHtml->loadHtmlFile( $this->buildUrl() );
		
		libxml_clear_errors(); 
		libxml_use_internal_errors( $bOldSetting ); 
		
		return $oHtml;
	}
}
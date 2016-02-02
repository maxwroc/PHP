<?php 

class Module_Parser_Wroclaw extends Module_Parser {
	
	private $sIdFormat = 'kort%d_hr_%s';
	
	private $sReservedString = 'reservation';
	
	protected $iCorts = 4;
	
	protected $oXpath;
	
	protected function buildUrl() {
		return $this->sBaseUrl . $this->oDay->format();
	}
	
	protected function getElementId( $oHour, $iCourt ) {
		return sprintf( $this->sIdFormat, $iCourt, $oHour->format( 'YmdHi' ) );
	}
	
	protected function getClassNameFromElement( $sId ) {
		$oNodeList = $this->oXpath->query( "//td[@id='$sId']/@class" );
		foreach ( $oNodeList as $oNode ) {
			return $oNode->nodeValue;
		}
	}
	
	protected function isHourFree( $sClassName ) {
		return !(bool)strstr( $sClassName, $this->sReservedString );
	}
	
}
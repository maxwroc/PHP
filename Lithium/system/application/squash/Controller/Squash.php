<?php 

class Controller_Squash extends Abstract_Squash {
	
	public function indexAction() {
		echo 'ok';
	}
	
	public function checkAction() {
		
		$oReservation = $this->getModule( 'Reservation' );
		
		$oReservation->setBaseUrl( 'http://squash.wroclaw.pl/rezerwacje/index.php?id=47&details=true&date=' );
		$oReservation->setDatePeriod( LDateTime::factory( '2011-03-01' ), LDateTime::factory( '2011-03-03' ) );
		$oReservation->setTimePeriod( 7, 9 );
		
		if ( $oReservation->isFree() ) {
			
			foreach ( $oReservation->getResults() as $oDay ) {
				printf( "<pre>%s (%d):\n%s</pre>", __FILE__, __LINE__, (string)$oDay );
			}
			
		} else {
			echo 'Brak wolnych kortów';
		}
	}
	
}
<?php 

class Controller_Blog extends Abstract_Blog {
	
	public function indexAction() {
		
		$this->getTemplate()->mContent = $this->getView( 'box/single', array( 'sTitle' => 'Testowy tytul' ) );
		
	}
	
}
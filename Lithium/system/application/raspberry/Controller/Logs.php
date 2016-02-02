<?php

class Controller_Logs extends Abstract_BaseController {
	
	public function indexAction() {
		
		$this->mTemplate->content = View::factory( 'logs/listing', array() );
	}

}
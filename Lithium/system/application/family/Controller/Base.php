<?php

abstract class Controller_Base extends Core_Controller {

	public $mTemplate = 'master';
	
	public function init() {
	
		parent::init();
		
		$this->mTemplate->aResources = [];
	}
}
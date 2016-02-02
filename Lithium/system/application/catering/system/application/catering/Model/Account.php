<?php
class Model_Account extends Core_Model {
	
	protected $sTable = 'accounts';
	protected $sPrimaryKey = 'account_id';
	
	protected $aHasMany = array( 'User' => 'account_id' );
	
}

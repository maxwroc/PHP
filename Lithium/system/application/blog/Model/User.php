<?php  

class Model_User extends Database_Model {
	
	protected $sTable = 'users';
	protected $sPrimaryKey = 'user_id';
	
	protected $aTableInfo = array(
		'user_id' => 'int',
		'email' => 'varchar',
		'password' => 'varchar',
		'date_created' => 'date',
		'date_login' => 'date'
	);
	
}
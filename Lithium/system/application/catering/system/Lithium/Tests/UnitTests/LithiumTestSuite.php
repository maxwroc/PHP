<?php

require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter( __FILE__ );

require_once 'PHPUnit/Framework/TestSuite.php';

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'RouterTest.php';

PHPUnit_Util_Filter::$filterPHPUnit = FALSE;

class LithiumTestSuite extends PHPUnit_Framework_TestCase{
	
	public static function suite() {
		
		$oSuite = new PHPUnit_Framework_TestSuite( 'LithiumTestSuite' );
		
		$oSuite->addTestSuite( 'RouterTest' );
		
		return $oSuite;
		
	}
	
	/**
	 * To prevent from warning appearing during execution of this suite
	 */
	public function testFakeTest() {
		$this->assertTrue( true );
	}
	
}
<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Router.php';

class RouterTest extends PHPUnit_Framework_TestCase {
	
	protected $oSubject;
	
	protected function setUp() {
		
		$aConfig = array(
			'Default_controller' => 'Index',
			'Default_function' => 'index'
		);
		
		$this->oSubject = new Router( $aConfig, array( 'some value' ) );
		
	}
	
	protected function tearDown() {
		unset( $this->oSubject );
	}
	
	public function testConstructor_ReturnsRouter() {
		$this->assertTrue( $this->oSubject instanceof Router );
	}
	
	public function getNameArray() {
		return array(
			array( 'index' ),
			array( 'Other' )
		);
	}
	
	public function getSamplePathInfoStrings() {
		return array(
			array( '/controller1/function1', 
				array( 
					'controller' => 'controller1',
					'function' => 'function1Action',
					'params' => array()
				) 
			),
			array( '/controller_name/name_of_some_func/', 
				array( 
					'controller' => 'controller_name',
					'function' => 'name_of_some_funcAction',
					'params' => array()
				) 
			),
			array( '/controller1/function1/1', 
				array( 
					'controller' => 'controller1',
					'function' => 'function1Action',
					'params' => array( '1' )
				) 
			),
			array( '/controller1/function1/1/2', 
				array( 
					'controller' => 'controller1',
					'function' => 'function1Action',
					'params' => array( '1', '2' )
				) 
			),
			array( '/controller1/function1/1/2/', 
				array( 
					'controller' => 'controller1',
					'function' => 'function1Action',
					'params' => array( '1', '2', '' )
				) 
			)
		);
	}
	
	/**
	 * @dataProvider getNameArray
	 */
	public function testGetControllerName_ReturnsCorrectDefaultString( $sDefaultName ) {
		
		$oRouter = new Router( 
			array(
				'Default_controller' => $sDefaultName,
				'Default_function' => 'index'
			) 
		);
		
		$this->assertTrue( $oRouter->getControllerName() == 'Controller_' . ucfirst( $sDefaultName ) );
		
		unset( $oRouter );
	}
	
	/**
	 * @dataProvider getNameArray
	 */
	public function testGetFunctionName_ReturnsCorrectDefaultString( $sDefaultName ) {
		
		$oRouter = new Router( 
			array(
				'Default_controller' => 'Ctrl',
				'Default_function' => $sDefaultName
			) 
		);
		
		$this->assertTrue( $oRouter->getFunctionName() == strtolower( $sDefaultName ) . 'Action' );
		
		unset( $oRouter );
		
	}
	
	/**
	 * Test getFunctionName with multiple urls given in PATH_INFO
	 * 
	 * @dataProvider getSamplePathInfoStrings
	 */
	public function testGetFunctionName_MultipleUrlsInPathInfo_ReturnString( $sPathInfo, $aCorrectValues ) {
		
		$_SERVER['PATH_INFO'] = $sPathInfo;
		
		$oRouter = new Router( 
			array(
				'Default_controller' => 'Ctrl',
				'Default_function' => 'indexAction'
			) 
		);
		error_log( __FILE__ . ' (' . __LINE__ . "): \n" . print_r( $oRouter->getFunctionName(), 1 ) );
		error_log( __FILE__ . ' (' . __LINE__ . "): \n" . print_r( $aCorrectValues['function'], 1 ) );
		
		$this->assertTrue( $oRouter->getFunctionName() === $aCorrectValues['function'] );
		
		unset( $oRouter );
		unset( $_SERVER['PATH_INFO'] );
		
	}
	
	/**
	 * Test getParams when PATH_INFO contains empty string
	 */
	public function testGetParams_DefaultUrl_ReturnArray() {
		$this->assertTrue( is_array( $this->oSubject->getParams() ) );
	}
	
	/**
	 * Test getParams with multiple urls given in PATH_INFO
	 * 
	 * @dataProvider getSamplePathInfoStrings
	 */
	public function testGetParams_MultipleUrlsInPathInfo_ReturnArray( $sPathInfo, $aCorrectValues ) {
		$_SERVER['PATH_INFO'] = $sPathInfo;
		
		$oRouter = new Router( 
			array(
				'Default_controller' => 'Ctrl',
				'Default_function' => 'indexAction'
			) 
		);
		
		$this->assertTrue( array_diff( $oRouter->getParams(), $aCorrectValues['params'] ) === array() );
		
		unset( $oRouter );
		unset( $_SERVER['PATH_INFO'] );
		
	}
	
	/**
	 * Test getCurrentPath with multiple urls given in PATH_INFO
	 * 
	 * @dataProvider getSamplePathInfoStrings
	 */
	public function testGetCurrentPath_MultipleUrlsInPathInfo_ReturnString( $sPathInfo ) {
		$_SERVER['PATH_INFO'] = $sPathInfo;
		
		$oRouter = new Router( 
			array(
				'Default_controller' => 'Ctrl',
				'Default_function' => 'indexAction'
			) 
		);
		
		$this->assertTrue( $oRouter->getCurrentPath() === $sPathInfo );
		
		unset( $oRouter );
		unset( $_SERVER['PATH_INFO'] );
		
	}
	
	/**
	 * Test getCurrentPath with multiple urls given in ORIG_PATH_INFO
	 * 
	 * @dataProvider getSamplePathInfoStrings
	 */
	public function testGetCurrentPath_MultipleUrlsInOrigPathInfo_ReturnString( $sPathInfo ) {
		
		unset( $_SERVER['PATH_INFO'] );
		$_SERVER['ORIG_PATH_INFO'] = $sPathInfo;
		
		$oRouter = new Router( 
			array(
				'Default_controller' => 'Ctrl',
				'Default_function' => 'indexAction'
			) 
		);
		
		$this->assertTrue( $oRouter->getCurrentPath() === $sPathInfo );
		
		unset( $oRouter );
		unset( $_SERVER['ORIG_PATH_INFO'] );
		
	}
	
}
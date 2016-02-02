<?php

require_once 'Tests/UnitTests/LithiumTestBase.php';
require_once 'Router.php';

class RouterTest extends LithiumTestBase {
	
	protected $oSubject;
	
	public function setUp() {
		parent::setUp();
		$_SERVER[ 'PATH_INFO' ] = '';
		$_SERVER[ 'HTTP_HOST' ] = 'localhost';
		$this->oSubject = new Router( $this->getMockConfig() );	
	}
	
	protected function tearDown() {
		unset( $this->oSubject );
	}
	
	public static function providerControllerNames() {
		return array(
			array( 'index', 'Controller_Index' ),
			array( 'Other', 'Controller_Other' ),
			array( 'nOrMaL', 'Controller_Normal' )
		);
	}
	
	/**
	 * @dataProvider providerControllerNames
	 */
	public function testGetControllerName_DefaultController_ReturnsCorrectString( $sConfigValue, $sControllerName ) {
		
		$oConfig = $this->getMockConfig();
		$oConfig->expects( $this->at( 0 ) )
			->method( 'hasValue' )
			->with( $this->equalTo( 'rules' ) )
			->will( $this->returnValue( false ) );
		$oConfig->expects( $this->at( 1 ) )
			->method( 'getValue' )
			->with( $this->equalTo( 'router.default_controller' ) )
			->will( $this->returnValue( $sConfigValue ) );
		
		$oConfig->expects( $this->at( 2 ) )
			->method( 'getValue' )
			->with( $this->equalTo( 'router.default_function' ) )
			->will( $this->returnValue( $sControllerName ) );
		
		$this->oSubject->init();
		
		$this->assertSame( $sControllerName , $this->oSubject->getControllerName() );
	}
	
	public static function provider()
    {
        return array(
          array(0, 0, 0),
          array(0, 1, 1),
          array(1, 0, 1)
        );
    }
 
    /**
     * @dataProvider provider
     */
    public function testAdd($a, $b, $c)
    {
        $this->assertEquals($c, $a + $b);
    }
	
//	public function getSamplePathInfoStrings() {
//		return array(
//			array( '/controller1/function1', 
//				array( 
//					'controller' => 'controller1',
//					'function' => 'function1Action',
//					'params' => array()
//				) 
//			),
//			array( '/controller_name/name_of_some_func/', 
//				array( 
//					'controller' => 'controller_name',
//					'function' => 'name_of_some_funcAction',
//					'params' => array()
//				) 
//			),
//			array( '/controller1/function1/1', 
//				array( 
//					'controller' => 'controller1',
//					'function' => 'function1Action',
//					'params' => array( '1' )
//				) 
//			),
//			array( '/controller1/function1/1/2', 
//				array( 
//					'controller' => 'controller1',
//					'function' => 'function1Action',
//					'params' => array( '1', '2' )
//				) 
//			),
//			array( '/controller1/function1/1/2/', 
//				array( 
//					'controller' => 'controller1',
//					'function' => 'function1Action',
//					'params' => array( '1', '2', '' )
//				) 
//			)
//		);
//	}
	
	
//	
//	/**
//	 * @dataProvider getNameArray
//	 */
//	public function testGetFunctionName_ReturnsCorrectDefaultString( $sDefaultName ) {
//		
//		$oRouter = new Router( 
//			array(
//				'Default_controller' => 'Ctrl',
//				'Default_function' => $sDefaultName
//			) 
//		);
//		
//		$this->assertTrue( $oRouter->getFunctionName() == strtolower( $sDefaultName ) . 'Action' );
//		
//		unset( $oRouter );
//		
//	}
//	
//	/**
//	 * Test getFunctionName with multiple urls given in PATH_INFO
//	 * 
//	 * @dataProvider getSamplePathInfoStrings
//	 */
//	public function testGetFunctionName_MultipleUrlsInPathInfo_ReturnString( $sPathInfo, $aCorrectValues ) {
//		
//		$_SERVER['PATH_INFO'] = $sPathInfo;
//		
//		$oRouter = new Router( 
//			array(
//				'Default_controller' => 'Ctrl',
//				'Default_function' => 'indexAction'
//			) 
//		);
//		error_log( __FILE__ . ' (' . __LINE__ . "): \n" . print_r( $oRouter->getFunctionName(), 1 ) );
//		error_log( __FILE__ . ' (' . __LINE__ . "): \n" . print_r( $aCorrectValues['function'], 1 ) );
//		
//		$this->assertTrue( $oRouter->getFunctionName() === $aCorrectValues['function'] );
//		
//		unset( $oRouter );
//		unset( $_SERVER['PATH_INFO'] );
//		
//	}
//	
//	/**
//	 * Test getParams when PATH_INFO contains empty string
//	 */
//	public function testGetParams_DefaultUrl_ReturnArray() {
//		$this->assertTrue( is_array( $this->oSubject->getParams() ) );
//	}
//	
//	/**
//	 * Test getParams with multiple urls given in PATH_INFO
//	 * 
//	 * @dataProvider getSamplePathInfoStrings
//	 */
//	public function testGetParams_MultipleUrlsInPathInfo_ReturnArray( $sPathInfo, $aCorrectValues ) {
//		$_SERVER['PATH_INFO'] = $sPathInfo;
//		
//		$oRouter = new Router( 
//			array(
//				'Default_controller' => 'Ctrl',
//				'Default_function' => 'indexAction'
//			) 
//		);
//		
//		$this->assertTrue( array_diff( $oRouter->getParams(), $aCorrectValues['params'] ) === array() );
//		
//		unset( $oRouter );
//		unset( $_SERVER['PATH_INFO'] );
//		
//	}
//	
//	/**
//	 * Test getCurrentPath with multiple urls given in PATH_INFO
//	 * 
//	 * @dataProvider getSamplePathInfoStrings
//	 */
//	public function testGetCurrentPath_MultipleUrlsInPathInfo_ReturnString( $sPathInfo ) {
//		$_SERVER['PATH_INFO'] = $sPathInfo;
//		
//		$oRouter = new Router( 
//			array(
//				'Default_controller' => 'Ctrl',
//				'Default_function' => 'indexAction'
//			) 
//		);
//		
//		$this->assertTrue( $oRouter->getCurrentPath() === $sPathInfo );
//		
//		unset( $oRouter );
//		unset( $_SERVER['PATH_INFO'] );
//		
//	}
//	
//	/**
//	 * Test getCurrentPath with multiple urls given in ORIG_PATH_INFO
//	 * 
//	 * @dataProvider getSamplePathInfoStrings
//	 */
//	public function testGetCurrentPath_MultipleUrlsInOrigPathInfo_ReturnString( $sPathInfo ) {
//		
//		unset( $_SERVER['PATH_INFO'] );
//		$_SERVER['ORIG_PATH_INFO'] = $sPathInfo;
//		
//		$oRouter = new Router( 
//			array(
//				'Default_controller' => 'Ctrl',
//				'Default_function' => 'indexAction'
//			) 
//		);
//		
//		$this->assertTrue( $oRouter->getCurrentPath() === $sPathInfo );
//		
//		unset( $oRouter );
//		unset( $_SERVER['ORIG_PATH_INFO'] );
//		
//	}
	
}
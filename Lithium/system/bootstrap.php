<?php defined( 'SYSPATH' ) or die( 'No direct access!' );

if ( IN_PRODUCTION ) {
	error_reporting( E_ALL ^ E_NOTICE );
} else {
	error_reporting( E_ALL );
}

define( 'EXT', '.php' );

define( 'DOCROOT', realpath( getcwd() . DIRECTORY_SEPARATOR . SYSPATH ) . DIRECTORY_SEPARATOR );

include( SYSPATH . 'Lithium/Loader' . EXT );

Loader::registerAutoload();

$oLithium = Lithium::getInstance();

$oConfig = new Config( $aConfig );
$oLithium->setConfig( $oConfig );
$oLithium->setRouter( new Router( $oConfig ) );

// let a magic happen :)
$oLithium->Dispatch();

// display warnings
if ( ! IN_PRODUCTION && ( ! empty( $aLithiumWarnings ) ) ) {
	echo implode( '<br />', $aLithiumWarnings );
}


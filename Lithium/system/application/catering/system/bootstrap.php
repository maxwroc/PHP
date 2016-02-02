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

$oLithium->setConfig( new Config( $aConfig ) );
$oLithium->setRouter( new Router( isset( $aConfig['router'] ) ? $aConfig['router'] : array() ) );

// let a magic happen :)
$oLithium->Dispatch();

// display warnings
if ( ! IN_PRODUCTION && ( ! empty( $aLithiumWarnings ) ) ) {
	echo implode( '<br />', $aLithiumWarnings );
}


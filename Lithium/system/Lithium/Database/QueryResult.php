<?php 

class Database_QueryResult {
	
	public $rResource;
	
	protected $sStatement = '';
	
	protected $sError = null;
	
	protected $aResult = array();
	
	public function __construct( $sSqlStatement ) {
		$this->sStatement = $sSqlStatement;
	}
	
	public function sql( $sStatement = null ) {
		if ( is_null( $sStatement ) ) return $this->sStatement;
		$this->sStatement = $sSqlStatement;
		return $this;
	}
	
	public function isError() {
		return ! is_null( $this->sError );
	}
	
	public function error( $sError = null ) {
		if ( is_null( $sError ) ) return $this->sError;
		$this->sError = $sError;
		return $this;
	}
	
	public function result( $aResult = null ) {
		if ( is_null( $aResult ) ) return $this->aResult;
		$this->aResult = $aResult;
		return $this;
	}
	
}
<?php

// TODO Funkcje walidujace w validatorze a nie w Fieldach

class Module_Validator {
	
	protected $aFields = array();
	
	protected $aErrors;
	
	/**
	 * Validete all defined fields
	 * 
	 * @return bool
	 */
	public function validate() {
		
		$bResult = true;
		
		foreach( $this->aFields as $sId => $oField ) {
			
			if ( ! $oField->validate() ) {
				$this->aErrors[ $sId ] =  $oField->getError();
				$bResult = false;
			} 
			
		}
		
		return $bResult;
		
	}
	
	/**
	 * Creates and/or returns validator field
	 * 
	 * @param string $sId - field id
	 * @param string $sValue - value (subject) which will be validated
	 * @param string $sName - field name which will be returned together with error msg
	 * @return ValidatorField
	 */
	public function field( $sId, & $sValue = null, $sName = null ) {
		
		// when user try to get non existing field
		if ( ! is_null( $sValue ) && in_array( $sId, $this->aFields ) ) {
			throw new Lithium_Exception( 'validator.field_set_already', $sId );
		}
		
		// if field does not exist we create it
		if ( ! in_array( $sId, $this->aFields ) ) {
			$this->aFields[ $sId ] = new ValidatorField( $sId, $sValue, $sName );
		}
		
		return $this->aFields[ $sId ];
		
	}
	
	/**
	 * Return error message for particular field or list of all errors
	 * 
	 * @param string $sId - field id
	 * @param array - Array of errors or array that contains values of one particular error
	 */
	public function getError( $sId = null ) {
		return is_null( $sId ) ? $this->aErrors : $this->aErrors[ $sId ];
	}
	
}

class ValidatorField {
	
	protected $aData;
	
	protected $sError;
	
	public function __construct( $sId, & $sValue, $sName = null ) {
		
		$this->aData['id'] = $sId;
		$this->aData['value'] = & $sValue;
		$this->aData['rules'] = array();
		$this->aData['name'] = is_null( $sName ) ? $sId : $sName;
		
	}
	
	public function rules( $sRules = null ) {
		
		if ( ! empty( $sRules ) ) {
			
			$aFunc = explode( '|', $sRules );
			
			foreach( $aFunc as $sFunc ) {
				
				if ( preg_match( '/^([a-z0-9]+)/', $sFunc, $aRes ) ) {
					
					$a = array( $this, $aRes[0] );
					if ( is_callable($a,true) ) {
						$this->aData[ 'rules' ][] = $sFunc;
					} else {
						throw new Lithium_Exception( 'user.invalid_rule', $sFunc );
					}
					
				} else {
					throw new Lithium_Exception( 'user.invalid_rule', $sFunc );
				}
				
			}
			
		}
		
		return $this->aData[ 'rules' ];
		
	}
	
	public function validate() {
		
		foreach( $this->aData[ 'rules' ] as $sFunc ) {
			
			if ( preg_match( '/([a-zA-Z0-9]+)\[([a-z0-9,]+)\]/', $sFunc, $aMatch ) ) {
				
				$aParams = explode( ',', $aMatch[2] );
				if ( ! call_user_func_array( array( $this, $aMatch[1] ), $aParams ) ) {
					return false;
				}
				
			} else {
				
				// call specified function
				if ( ! call_user_func( array( $this, $sFunc ) ) ) {
					return false;
				}
				
			}
			
		}
		
		return true;
		
	}
	
	protected function md5() {
		
		$this->aData[ 'value' ] = md5( $this->aData[ 'value' ] );
		return true;
		
	}
	
	protected function not( $aVal ) {
		
		if ( ! is_array( $aVal ) ) $aVal = array( $aVal );
		
		foreach( $aVal as $mVal ) {
			
			if ( $mVal == $this->aData[ 'value' ] ) {
				
				$this->sError = 'validator.not_allowed_value';
				
				return false;
				
			}
			
		}
		
		return true;
		
	}
	
	protected function numeric() {
		
		if ( empty( $this->aData[ 'value' ] ) ) return true;

		if ( is_numeric( $this->aData[ 'value' ] ) ) return true;
		
		$this->sError = 'validator.not_numeric_value';
		
		return false;
		
	}
	
	/**
	 * Check if date looks like this e.g. 2007-12-04 or 2004-5-6 23:06:00
	 *
	 */
	protected function date() {
		
		$aDateParsed = date_parse( $this->aData[ 'value' ] );
		
		if ( ereg( '^[0-9]{4}-[0-9]{2}-[0-9]{2}$', $this->aData[ 'value' ] ) && ( $aDateParsed[ 'error_count' ] == 0 ) ) {
			return true;
		}
		
		$this->sError = 'validator.not_date_value';
		return false;
		
	}

	protected function email() {
		
		if ( ! eregi( '^[a-z0-9_\\.-]+@([a-z0-9_-]+\\.)+[a-z]{2,}$', $this->aData[ 'value' ] ) ) {
			
			$this->sError = 'validator.not_valid_email';
			return false;
			
		}
		
		return true;
		
	}
	
	protected function toint() {
		
		$this->aData[ 'value' ] = (int) $this->aData[ 'value' ];
		return true;
		
	}
	
	protected function tofloat() {
		
		$this->aData[ 'value' ] = str_replace( ',', '.', $this->aData[ 'value' ] );
		$this->aData[ 'value' ] = (float) $this->aData[ 'value' ];
		return true;
		
	}
	
	protected function hsc() {
		
		$this->aData[ 'value' ] = htmlspecialchars( $this->aData[ 'value' ] );
		return true;
		
	}
	
	public function getError() {
		return array( 
			'msg' => $this->sError,
			'field_name' => $this->aData['name']
		);
	}
	
	protected function length( $iMin, $iMax ) {
		
		$iMax = (int) $iMax;
		$iMin = (int) $iMin;
		$iLen = strlen( $this->aData[ 'value' ] );
		
		if ( $iLen < $iMin ) {
			
			$this->sError = 'validator.lenth_to_low';
			return false;
			
		} elseif ( $iLen > $iMax ) {
			
			$this->sError = 'validator.lenth_to_big';
			return false;
			
		}
		
		return true;
		
	}
	
	protected function required() {
		
		if ( ( empty( $this->aData[ 'value' ] ) ) AND ( $this->aData[ 'value' ] != '0' ) ) {
			
			$this->sError = 'validator.required_val_not_set';
			return false;
			
		}
		
		return true;;
		
	}
	
	
}

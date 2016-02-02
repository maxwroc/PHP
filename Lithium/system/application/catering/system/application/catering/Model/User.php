<?php
class Model_User extends Core_Model {
	
	const SORT_NAME = 'u.name';
	const SORT_FIRSTNAME = 'u.fname';
	const SORT_EMAIL = 'u.email';
	const SORT_ROLE = 'role';
	
	protected $sTable = 'users';
	protected $sPrimaryKey = 'user_id';
	
	protected $aProperties = array();
	
	protected $aHasOne = array( 'role_id' => 'Role', 'account_id' => 'Account' );
	
	/**
	 * Zwraca liste uzytkownikow dla zadanego konta
	 * 
	 * @param int $iAccountId - id konta
	 * @param int $iOffset - przesuniecie (numer pierwszego rekodru)
	 * @param int $iQuantity - ilosc rekordow do zwrocenia
	 * @return array
	 */
	public function getUsersForAccount( $iAccountId, $iOffset, $iQuantity ) {
		
		if ( $this->oSorter instanceof Module_Sorter ) {
			$sOrder = $this->oSorter->getSqlOderByString( 'ORDER BY ' );
		} else {
			$sOrder = sprintf( 'ORDER BY %s ASC', self::SORT_NAME );
		}
		
		$iAccountId = (int) $iAccountId;
		
		$sSql = sprintf(
			'SELECT' .
			' u.user_id, u.email, u.fname, u.name, r.name as role ' .
			'FROM %s u ' .
			'INNER JOIN roles r ' .
			'ON u.role_id=r.role_id ' .
			'WHERE u.account_id=%d ' .
			'%s;',
			$this->sTable,
			$iAccountId,
			$sOrder
		);
		
		// ustawiamy przesuniecie i limit uzytkownikow
		$this->oDB->setChunkArgs( $iOffset, $iQuantity );
		
		$mResult = $this->oDB->query( $sSql );
		if ( $mResult === false ) {
			$mResult = array();
		}
		
		return $mResult;
		
	}
	
	/**
	 * Przechowuje dodatkowe wasciwosci obiektu
	 * 
	 * Gdy podamy nazwe wlasciwosci zostanie zwrocona jego wartosc,
	 * gdy podamy rowniez wartosc to zostanie ona przypisana do 
	 * wlasciwosci
	 * 
	 * @param string $sName - nazwa wlasciwosci
	 * @param mixed $mValue - wartosc wlasciwosci
	 * @return mixed
	 */
	public function propertie( $sName, $mValue = null ) {
		
		if ( ! is_null( $mValue ) ) {
			$this->aProperties[ $sName ] = $mValue;
		} else {
			if ( isset( $this->aProperties[ $sName ] ) ) {
				return $this->aProperties[ $sName ];
			} else {
				return null;
			}
		}
		
	}
	
}
?>
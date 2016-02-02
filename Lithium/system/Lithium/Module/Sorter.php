<?php

class Module_Sorter {
	
	const SORT_ASC = 1;
	const SORT_DESC = 2;
	
	protected $aSortValues = array();
	
	protected $sBaseUrl;
	
	protected static $aSortImages;
	
	protected static $sCurrentSortKey = 'default';
	protected static $bSortReverse = false;
	
	protected static $oRouter;
	protected static $sUriSortOrder = 'SortOrder';
	protected static $sUriSortReverse = 'SortedReverse';
	
	/**
	 * Sets Router
	 * 
	 * @return void
	 */
	public static function setRouter( $oRouter ) {
		
		self::$oRouter = $oRouter;
		
		$aUrlParams = self::$oRouter->getParams();
		
		for ( $i = 0, $c = sizeof( $aUrlParams ); $i < $c; $i++ ) {
			
			if ( $aUrlParams[ $i ] == self::$sUriSortOrder ) {
				self::$sCurrentSortKey = (int)$aUrlParams[ $i + 1 ];
				self::$oRouter->removeParam( self::$sUriSortOrder, true );
			}
			if ( $aUrlParams[ $i ] == self::$sUriSortReverse ) {
				self::$bSortReverse = (bool)$aUrlParams[ $i + 1 ];
				self::$oRouter->removeParam( self::$sUriSortReverse, true );
			}
			
		}
		
	}
	
	public static function setImageUrl( $sUrl, $iType ) {
		
		// prepend url with base app url if necessary
		if ( ! strstr( $sUrl, 'http://' ) ) {
			$sUrl = self::$oRouter->getFileUrl( $sUrl, 'image' );
		}
		
		self::$aSortImages[ $iType ] = $sUrl;
		
	}
	
	public static function setUriStrings( $sUriSortOrder, $sUriSortReverse ) {
		self::$sUriSortOrder = $sUriSortOrder;
		self::$sUriSortReverse = $sUriSortReverse;
	}
	
	public function __construct( $sDefSortValue, $sDefSortDirection = self::SORT_ASC ) {
		
		if ( ! ( self::$oRouter instanceof Router ) ) {
			throw new Lithium_exception( 'core.object_property_not_set', 'Router' );
		}
		
		$this->aSortValues['default'] = array( 'value' => $sDefSortValue, 'direction' => $sDefSortDirection );
		
		$this->init();
		
	}
	
	protected function init() {
		
		$this->sBaseUrl = self::$oRouter->getPageUrl();
		
		// get base url
		if ( self::$sCurrentSortKey != 'default' ) {
			$this->sBaseUrl = str_replace( '/'. self::$sUriSortOrder . '/'. self::$sCurrentSortKey, '', $this->sBaseUrl );
		}
		if ( self::$bSortReverse ) {
			$this->sBaseUrl = str_replace( '/'. self::$sUriSortReverse . '/1', '', $this->sBaseUrl );
		}
		
		$this->sBaseUrl = rtrim( $this->sBaseUrl, '/' );
		
	}
	
	public function addSortOption( $sSortValue, $iSortDirection = self::SORT_ASC, $sUrlKey = null ) {
		
		$sUrlKey = is_null( $sUrlKey ) ? count( $this->aSortValues ) : $sUrlKey;
		
		$this->aSortValues[ $sUrlKey ] = array(
			'value' => $sSortValue,
			'direction' => $iSortDirection
		);
		
	}
	
	public function getSortValue() {
		return $this->aSortValues[ self::$sCurrentSortKey ]['value'];
	}
	
	public function getSortDirection() {
		
		$aDirectionStrings = array(
			self::SORT_ASC => 'ASC',
			self::SORT_DESC => 'DESC'
		);
		
		$iDirection = $this->aSortValues[ self::$sCurrentSortKey ]['direction'];
		if ( self::$bSortReverse ) {
			$iDirection = ( $iDirection == self::SORT_ASC ) ? self::SORT_DESC : self::SORT_ASC;
		}
		
		return $aDirectionStrings[ $iDirection ];
		
	}
	
	public function getSortDirectionImg( $sUrlKey = 'default' ) {
		
		$sImgUrl = '';
		
		if ( self::$sCurrentSortKey == $sUrlKey ) {
			
			$iDirection = $this->aSortValues[ self::$sCurrentSortKey ]['direction'];
			if ( self::$bSortReverse ) {
				$iDirection = ( $iDirection == self::SORT_ASC ) ? self::SORT_DESC : self::SORT_ASC;
			}
			
			$sImgUrl = self::$aSortImages[ $iDirection ];
			
		}
		
		return $sImgUrl;
		
	}
	
	public function getSqlOderByString( $sPrependString = ' ' ) {
		return sprintf( '%s%s %s', $sPrependString, $this->getSortValue(), $this->getSortDirection() );
	}
	
	public function getSortUrl( $sUrlKey = 'default', $bReverse = null ) {
		
		$sUrl = $this->sBaseUrl;
		
		if ( $sUrlKey != 'default' ) {
			$sUrl .= '/' . self::$sUriSortOrder . '/' . $sUrlKey;
		}
		
		// add reverse param if necessary
		if ( !is_null( $bReverse ) && $bReverse ) {
			$sUrl .= '/' . self::$sUriSortReverse . '/1';
		} elseif ( ( ! self::$bSortReverse ) && ( self::$sCurrentSortKey == $sUrlKey ) ) {
			$sUrl .= '/' . self::$sUriSortReverse . '/1';
		}
		
		return $sUrl;
		
	}
	
}
<?php

/**
 * Pagination class
 */
class Module_Pagination {
	
	protected $sBaseUrl;
	
	protected $iCurrentPage = 1;
	
	protected $iOffset = 0;
	
	protected $iItemsPerPage;
	
	protected $iTotalItems;
	
	protected $sUriSegment;
	
	protected $aStrings;
	
	protected $aSettings;
	
	protected static $oRouter;
	
	/**
	 * Sets Router
	 * 
	 * @return void
	 */
	public static function setRouter( $oRouter ) {
		self::$oRouter = $oRouter;
	}
	
	/**
	 * Class constructor
	 * 
	 * @param string $sUrl - current url
	 * @param array $aActionParams - parameters from action call
	 * @param int $iItemsPerPage - items per page
	 * @param int $iTotalItems - quantity of items (total)
	 * @param string $sUriSegment - string which will be used in url
	 */
	public function __construct( $iItemsPerPage, $iTotalItems, $sUriSegment = 'page' ) {
		
		if ( ! ( self::$oRouter instanceof Router ) ) {
			throw new Lithium_exception( 'core.object_property_not_set', 'Router' );
		}
		
		$this->iItemsPerPage 	= $iItemsPerPage;
		$this->iTotalItems 		= $iTotalItems;
		$this->sUriSegment 		= $sUriSegment;
		
		$this->init();
		
	}
	
	/**
	 * Initialize class params
	 * 
	 * @return void
	 */
	protected function init() {
		
		// get curret page url
		$sUrl = self::$oRouter->getPageUrl();
		$aActionParams = self::$oRouter->getParams();
		
		for ( $i = 0, $c = sizeof( $aActionParams ); $i < $c; $i++ ) {
			
			if ( $aActionParams[ $i ] == $this->sUriSegment ) {
				$this->iCurrentPage = (int)$aActionParams[ $i + 1 ];
				break;
			}
			
		}
		
		if ( $this->iCurrentPage != 0 ) {
			$this->sBaseUrl = str_replace( '/'. $this->sUriSegment . '/'. $this->iCurrentPage, '', $sUrl );
			$this->iOffset = ( $this->iCurrentPage - 1 ) * $this->iItemsPerPage;
		} else {
			$this->sBaseUrl = $sUrl;
		}
		
		$this->sBaseUrl = rtrim( $this->sBaseUrl, '/' );
		
		$this->aSettings = array(
			'num_of_pages_to_display' => 5,
			'display_prevnext' => true
		);
		
		$this->aStrings = array( 
			'label' => 'Page', 
			'previous' => 'previous', 
			'next' => 'next',
			'first' => null, 
			'last' => null
		);
		
	}
	
	/**
	 * Set strings which will be used within pagination
	 * 
	 * @param array $aString - array of strings to set
	 * @return void
	 */
	public function setString( $aString ) {
		
		// valid keys of strings
		$aValidStrings = array( 'label' => '', 'previous' => '', 'next' => '', 'first' => '', 'last' => '' );
		
		// take valid strings and merge them with current strings
		$this->aStrings = array_merge( $this->aStrings, array_intersect_key( $aString, $aValidStrings ) );
		
	}
	
	/**
	 * Return current offset
	 * 
	 * @return int
	 */
	public function getOffset() {
		return $this->iOffset;
	}
	
	/**
	 * Return page url
	 * 
	 * @param int $iPage - page number
	 * @return string
	 */
	protected function getPageUrl( $iPage ) {
		
		if ( $iPage == 1 ) {
			return $this->sBaseUrl;
		}
		
		return $this->sBaseUrl . '/' . $this->sUriSegment . '/' . $iPage;
		
	}
	
	/**
	 * Return data for view
	 * 
	 * @return array
	 */
	public function getViewData() {
		
		$aData = array();
		
		$iNumOfPages = ceil( $this->iTotalItems / $this->iItemsPerPage );
		
		$aData['sLabel'] = $this->aStrings['label'];
		
		$aData['aNext']['text'] 	= $this->aStrings['next'];
		$aData['aPrevious']['text'] = $this->aStrings['previous'];
		$aData['aFirst']['text'] 	= is_null( $this->aStrings['first'] ) ? 1 : $this->aStrings['first'];
		$aData['aLast']['text'] 	= is_null( $this->aStrings['last'] ) ? $iNumOfPages : $this->aStrings['last'];
		
		$iPageOffset = 1;
		
		// check do we have more pages then we can display
		if ( $iNumOfPages > $this->aSettings['num_of_pages_to_display'] ) {
			
			$iTmp = round( ( $this->aSettings['num_of_pages_to_display'] -1 ) / 2 );
			
			$iPageOffset = $this->iCurrentPage - $iTmp;
			
			// if there left less pages then iTmp we move back page offset
			if ( ( $this->iCurrentPage + $iTmp ) > $iNumOfPages ) {
				$iPageOffset -= $this->iCurrentPage + $iTmp - $iNumOfPages;
			}
			
			if ( $iPageOffset < 1 ) $iPageOffset = 1;
			
		}
		
		// generate page links
		for ( $i = 0; $i < $this->aSettings['num_of_pages_to_display']; $i++ ) {
			
			$iActualPage = $iPageOffset + $i;
			
			if ( $iActualPage > $iNumOfPages ) {
				break;
			}
			
			$aData['aPages'][ $i ] = array(
				'url' => $this->iCurrentPage == $iActualPage ? '' : $this->getPageUrl( $iActualPage ),
				'text' => $iActualPage
			);
			
		}
		
		// add "first" and "previous" if necessary
		if ( $iPageOffset != 1 ) {
			$aData['aFirst']['url'] 	= $this->getPageUrl( 1 );
		}
		if ( $this->iCurrentPage != 1 ) {
			$aData['aPrevious']['url'] 	= $this->getPageUrl( $this->iCurrentPage - 1 );
		}
		// add "last" and "next" if necessary
		if ( $iActualPage < $iNumOfPages ) {
			$aData['aLast']['url'] = $this->getPageUrl( $iNumOfPages );
		}
		if ( $this->iCurrentPage != $iNumOfPages ) {
			$aData['aNext']['url'] = $this->getPageUrl( $this->iCurrentPage + 1 );
		}
		
		return $aData;
		
	}
	
}
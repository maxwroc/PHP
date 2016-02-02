<?php
class Controller_Account extends Abstract_Catering {
	
	protected $aRolesAllowed = array( 'admin', 'moderator' );
	
	public function init() {
		
		parent::init();
		
		if ( ! $this->oAuth->isLoggedIn() ) {
			$this->redirect( '/' );
			echo ' ';
			return;
		}
		
		// sprawdzamy czy moze uzywac kontrolera
		if ( ! in_array( $this->oCurrentUser->get( 'role_id' )->name, $this->aRolesAllowed ) ) {
			$this->redirect( '/' );
			echo ' ';
		}
		
	}
	
	public function indexAction() {
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_settings' );
		
		if ( $this->sRole !== 'admin' ) {
			$aData[ 'info' ] = $this->getLang( 'access_denied' );
			$this->mTemplate->content = View::factory( 'account/item_edit', $aData )->render();
			return;
		}
		
		if ( isset( $_POST[ 'submit' ] ) ) {
			$aData[ 'error' ] = $this->saveAccount();
			if ( $aData[ 'error' ] === true ) {
				return;
			}
		}
		
		// pobieramy dane konta
		$oAccount = new Model_Account( (int) $this->oCurrentUser->account_id );
		$aAccount = $oAccount->getRow();
		
		$aInputs = array(
			array(
				'type' => 'text',
				'label' => $this->getLang( 'company_name' ),
				'name' => 'name',
				'value' => $aAccount[ 'name' ]
			),
			array(
				'type' => 'text',
				'label' => $this->getLang( 'order_end_time' ),
				'name' => 'day_end',
				'value' => $aAccount[ 'day_end' ]
			),
			array(
				'type' => 'text',
				'label' => $this->getLang( 'max_price_of_meal' ),
				'name' => 'max_price',
				'value' => $aAccount[ 'max_price' ]
			),
			array(
				'type' => 'text',
				'label' => $this->getLang( 'employee_cost' ),
				'name' => 'employee_percent',
				'value' => $aAccount[ 'employee_percent' ]
			),
//			array(
//				'type' => 'text',
//				'label' => 'Wlasny plik css',
//				'name' => 'css',
//				'value' => $aAccount[ 'css' ]
//			)
		);
		
		$aData[ 'aInputs' ] = $aInputs;
		$aData[ 'bPrintForm' ] = true;
		$aData[ 'submit' ] = $this->getLang( 'Catering.save' );
		
		$this->mTemplate->content = View::factory( 'account/item_edit', $aData )->render();
		
	}
	
	protected function saveAccount() {
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_settings_saving' );
		
		$sName 				= $this->post( 'name' );
		$sTime 				= $this->post( 'day_end' );
		$fMaxPrice 			= $this->post( 'max_price' );
		$iEmployeePercent 	= $this->post( 'employee_percent' );
		$sCss 				= $this->post( 'css' );
		
		$oValidator = new Module_Validator();
		
		$oValidator->field( 'company_name', $sName )->rules( 'required|hsc' );
		$oValidator->field( 'order_end_time', $sTime )->rules( 'required' );
		$oValidator->field( 'max_price_of_meal', $fMaxPrice )->rules( 'required|tofloat' );
		$oValidator->field( 'employee_cost', $iEmployeePercent )->rules( 'required|toint' );
		
		if ( $oValidator->validate() ) {
			
			// zapisujemy ustawienia
			$oAccaount = new Model_Account( $this->oCurrentUser->account_id );
			$oAccaount->getRow();
			$oAccaount->name = $sName;
			$oAccaount->day_end = $sTime;
			$oAccaount->max_price = $fMaxPrice;
			$oAccaount->employee_percent = $iEmployeePercent;
			$oAccaount->css = $sCss;
			
			if ( $oAccaount->save() ) {
				
				$aMeta = $this->mTemplate->aMeta;
				$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $this->mTemplate->anchor() . '" />';
				$this->mTemplate->aMeta = $aMeta;
				
				$this->mTemplate->content = $this->getLang( 'save_settings_successfull' );
				
				return true;
				
			} else {
				return $this->getLang( 'save_settings_failed' );
			}
			
		} else {
			
			$aErrors = $oValidator->getError();
			foreach( $aErrors as $sField => $aError ) {
				$sMsg .= '<br />' . $this->getLang( $aError['msg'], $this->getLang( $sField ) ) ; 
			}
			return $sMsg;
			
		}
		
	}
	
	public function userAction( $iId = null ) {
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_useredit' );
		
		// sprawdzamy czy user jest adminem
		if ( $this->sRole !== 'admin' ) {
			$aData[ 'info' ] = $this->getLang( 'access_denied' );
			$this->mTemplate->content = View::factory( 'account/item_edit', $aData )->render();
			return;
		}
		
		if ( isset( $iId ) AND ( $iId !== 0 ) AND ( ! isset( $_POST[ 'submit' ] ) ) ) {							// edycja usera
			
			$bDelete = func_get_arg(0) == 'delete';
			
			if ( $bDelete ) {
				$iId = func_get_arg(1);
			}
			
			$iId = (int) $iId;
			
			$oUser = new Model_User( $iId );
			
			$aUser = $oUser->getRow();
			
			if ( ( $iId ) AND ( ! empty( $aUser ) ) AND ( $aUser[ 'account_id' ] == $this->oCurrentUser->account_id ) ) {
				
				if ( $bDelete ) {		// usuwanie uzytkownika

					if ( ( func_num_args() == 4 ) AND ( $this->oAuth->isValidToken( func_get_arg(2) ) ) ) {
					
						if ( $oUser->delete() ) {
							
							$aMeta = $this->mTemplate->aMeta;
							$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $this->mTemplate->anchor( '/account/users/' ) . '" />';
							$this->mTemplate->aMeta = $aMeta;
						
							$aData[ 'info' ] = $this->getLang( 'delele_user_successfull' );
							
						} else {
							$aData[ 'info' ] = $this->getLang( 'delele_user_failed' );
						}
					
					} else {
						
						// potwierdzenie usuniecia
						
						$aData = array(
							'sQuestion' => $this->getLang( 'delele_user_question', $aUser[ 'name' ] )
							, 'sTextYes' => $this->getLang( 'Catering.ok' )
							, 'sLinkYes' => '/account/user/delete/' . $iId . '/' . $this->oAuth->getSecurityToken() . '/'
							, 'sTextNo' => $this->getLang( 'Catering.cancel' )
							, 'sLinkNo' => '/account/user/' . $iId . '/'
						);
						
					}
					
					$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_userdelete' );
					
				} else {		// edycja danych
				
					$aOptions = array();
					$aLayoutList = array();
			
					$oRole = new Model_Role();
					$aRoles = $oRole->getAll();
					
					foreach( $aRoles as $aRole ) {
						$aOptions[] = array(
							'value' => $aRole[ 'role_id' ]
							, 'name' => $aRole[ 'name' ]
						);
					}
					
					// pobieramy dostepne layouty
					$oLayout = new Model_Layout();
					$aLayouts = $oLayout->getAll();
					foreach( $aLayouts as $aLayout ) {
						$aLayoutList[] = array(
							'value' => $aLayout[ 'layout_id' ]
							, 'name' => $aLayout[ 'name' ]
						);
					}
					
					$aInputs[] = array(
						'type' => 'text'
						, 'label' => $this->getLang( 'first_name' )
						, 'name' => 'fname'
						, 'value' => $aUser[ 'fname' ]
					);
					$aInputs[] = array(
						'type' => 'text'
						, 'label' => $this->getLang( 'sure_name' )
						, 'name' => 'name'
						, 'value' => $aUser[ 'name' ]
					);
					$aInputs[] = array(
						'type' => 'text'
						, 'label' => $this->getLang( 'email' )
						, 'name' => 'email'
						, 'value' => $aUser[ 'email' ]
					);
					$aInputs[] = array(
						'type' => 'password'
						, 'label' => $this->getLang( 'password' )
						, 'name' => 'pass'
						, 'value' => ''
					);
					$aInputs[] = array(
						'type' => 'text'
						, 'label' => $this->getLang( 'registered' )
						, 'name' => 'since'
						, 'value' => $aUser[ 'since' ]
						, 'disabled' => true
					);
					$aInputs[] = array(
						'type' => 'text'
						, 'label' => $this->getLang( 'last_login' )
						, 'name' => 'last_login'
						, 'value' => $aUser[ 'last_login' ]
						, 'disabled' => true
					);
					$aInputs[] = array(
						'type' => 'select'
						, 'label' => $this->getLang( 'layout' )
						, 'name' => 'layout'
						, 'value' => $aUser[ 'layout_id' ]
						, 'items' => $aLayoutList
					);
					$aInputs[] = array(
						'type' => 'select'
						, 'label' => $this->getLang( 'role' )
						, 'name' => 'role'
						, 'value' => $aUser[ 'role_id' ]
						, 'items' => $aOptions
					);
					$aInputs[] = array(
						'type' => 'hidden'
						, 'name' => 'user_id'
						, 'value' => $aUser[ 'user_id' ]
					);
					
					$aData = array(
						'bPrintForm' => true
						, 'aInputs' => $aInputs
						, 'sTextDelete' => $this->getLang( 'delete' )
						, 'sLinkDelete' => '/account/user/delete/' . $aUser[ 'user_id' ] . '/'
					);
					
					$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_useredit' );
				
				}
				
			} else {
				$aData[ 'info' ] = $this->getLang( 'user_not_found' );
			}
			
		} elseif ( isset( $_POST[ 'submit' ] ) ) {		// zapis usera
			
			$sFName = $this->post( 'fname' );
			$sName = $this->post( 'name' );
			$sPass = $this->post( 'pass' );
			$sEmail = $this->post( 'email' );
			$iRole = $this->post( 'role' );
			$iLayout = $this->post( 'layout' );
			$user_id = $this->post( 'user_id' );
			
			$oValidator = new Module_Validator();
			$oValidator->field( 'first_name', $sFName )->rules( 'required' );
			$oValidator->field( 'sure_name', $sName )->rules( 'required' );
			$oValidator->field( 'email', $sEmail )->rules( 'required|email' );
			$oValidator->field( 'layout', $iLayout )->rules( 'required|toint' );
			$oValidator->field( 'role', $iRole )->rules( 'required|toint' );
			$oValidator->field( 'user_id', $user_id )->rules( 'toint' );
			if ( $user_id == 0 ) {
				$oValidator->field( $this->getLang( 'password' ), $sPass )->rules( 'required|md5' );
			} elseif ( strlen( $sPass ) ) {
				$oValidator->field( $this->getLang( 'password' ), $sPass )->rules( 'md5' );
			}
			
			if ( $oValidator->validate() ) {
				
				if ( $user_id == 0 ) { //zapis nowego usera
					
					$oUser = new Model_User();
					
					$oUser->fname = $sFName;
					$oUser->name = $sName;
					$oUser->email = $sEmail;
					$oUser->password = $sPass;
					$oUser->layout_id = $iLayout;
					$oUser->role_id = $iRole;
					$oUser->account_id = $this->oCurrentUser->account_id;
					
					if ( $oUser->save() ) {
						
						$aMeta = $this->mTemplate->aMeta;
						$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $this->mTemplate->anchor( '/account/users/' ) . '" />';
						$this->mTemplate->aMeta = $aMeta;
						
						$aData[ 'info' ] = $this->getLang( 'save_user_successfull' );
						
					} else {
						$aData[ 'info' ] = $this->getLang( 'save_user_failed' );
					}
					
				} else { // zapis edytowanego usera
					
					$oUser = new Model_User( $user_id );
					
					$aUser = $oUser->getRow();
					
					if ( ( ! empty( $aUser ) ) AND ( $aUser[ 'account_id' ] == $this->oCurrentUser->account_id ) ) {
						
						$oUser->fname = $sFName;
						$oUser->name = $sName;
						$oUser->email = $sEmail;
						$oUser->layout_id = $iLayout;
						$oUser->role_id = $iRole;
						if ( ! empty( $sPass ) ) {
							$oUser->password = $sPass;
						}
						
						if ( $oUser->save() ) {
							
							$aMeta = $this->mTemplate->aMeta;
							$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $this->mTemplate->anchor( '/account/users/' ) . '" />';
							$this->mTemplate->aMeta = $aMeta;
						
							$aData[ 'info' ] = $this->getLang( 'save_user_successfull' );
							
						} else {
							$aData[ 'info' ] = $this->getLang( 'save_user_failed' );
						}
					}
					
				}
				
			} else {
				
				$aErrors = $oValidator->getError();
				foreach( $aErrors as $sField => $aError ) {
					$sMsg .= '<br />' . $this->getLang( $aError['msg'], $this->getLang( $sField ) ) ; 
				}
				$aData[ 'info' ] = $sMsg;
				
			}
			
			$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_user_save' );
			
		} else {										// akcja domyślna czyli dodawanie usera
			
			$aOptions = array();
			$aLayoutList = array();
			
			// pobieramy dostepne role
			$oRole = new Model_Role();
			$aRoles = $oRole->getAll();
			
			foreach( $aRoles as $aRole ) {
				$aOptions[] = array(
					'value' => $aRole[ 'role_id' ]
					, 'name' => $aRole[ 'name' ]
				);
			}
			
			
			// pobieramy dostepne layouty
			$oLayout = new Model_Layout();
			$aLayouts = $oLayout->getAll();
			
			foreach( $aLayouts as $aLayout ) {
				$aLayoutList[] = array(
					'value' => $aLayout[ 'layout_id' ]
					, 'name' => $aLayout[ 'name' ]
				);
			}
			
			$aInputs[] = array(
				'type' => 'text'
				, 'label' => $this->getLang( 'first_name' )
				, 'name' => 'fname'
				, 'value' => ''
			);
			$aInputs[] = array(
				'type' => 'text'
				, 'label' => $this->getLang( 'sure_name' )
				, 'name' => 'name'
				, 'value' => ''
			);
			$aInputs[] = array(
				'type' => 'text'
				, 'label' => $this->getLang( 'email' )
				, 'name' => 'email'
				, 'value' => ''
			);
			$aInputs[] = array(
				'type' => 'password'
				, 'label' => $this->getLang( 'password' )
				, 'name' => 'pass'
				, 'value' => ''
			);
			$aInputs[] = array(
				'type' => 'select'
				, 'label' => $this->getLang( 'layout' )
				, 'name' => 'layout'
				, 'value' => 2
				, 'items' => $aLayoutList
			);
			$aInputs[] = array(
				'type' => 'select'
				, 'label' => $this->getLang( 'role' )
				, 'name' => 'role'
				, 'value' => 3
				, 'items' => $aOptions
			);
			
			$aData = array(
				'bPrintForm' => true
				, 'aInputs' => $aInputs
			);
			
			$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_user_add' );
			
		}
		
		$aData[ 'submit' ] = $this->getLang( 'Catering.save' );
		
		$this->mTemplate->content = View::factory( '/account/item_edit', $aData )->render();
		
	}
	
	/**
	 * Lista uzytkownikow (wyswietlanie)
	 */
	public function usersAction() {
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'account.admin_users' );
		
		// sprawdzamy czy user jest adminem
		if ( $this->sRole !== 'admin' ) {
			$aData[ 'info' ] = $this->getLang( 'access_denied' );
			$this->mTemplate->content = View::factory( 'account/item_edit', $aData )->render();
			return;
		}
		
		$aList = array();
		
		// ustawianie klasy sortuacej
		$oSorter = new Module_Sorter( Model_User::SORT_NAME );
		$oSorter->addSortOption( Model_User::SORT_FIRSTNAME );
		$oSorter->addSortOption( Model_User::SORT_EMAIL );
		$oSorter->addSortOption( Model_User::SORT_ROLE, Module_Sorter::SORT_DESC );
		
		// tworzenie modelu typu uzytkownik
		$oUser = new Model_User();
		// przekazanie klasy sortujacej do modulu
		$oUser->setSorter( $oSorter );
		
		$iItemsPerPage = 30;
		$oPagination = new Module_Pagination( $iItemsPerPage, $oUser->getRowsCount() );
		$oPagination->setString( array(
			'previous' => $this->getLang( 'catering.prev_f' ),
			'next' => $this->getLang( 'catering.next_f' ),
			'label' => $this->getLang( 'catering.page' )
		) );
		
		$aUsers = $oUser->getUsersForAccount( $this->oCurrentUser->account_id, $oPagination->getOffset(), $iItemsPerPage );
		
		foreach ( $aUsers as $aUser ) {
			$aList[] = array(
				array(
					'sLink' => '/account/user/' . $aUser[ 'user_id' ] . '/',
					'sText' => $aUser[ 'fname' ]
				),
				array(
					'sLink' => '/account/user/' . $aUser[ 'user_id' ] . '/',
					'sText' => $aUser[ 'name' ]
				),
				array(
					'sLink' => '/account/user/' . $aUser[ 'user_id' ] . '/',
					'sText' => $aUser[ 'email' ]
				),
				array(
					'sLink' => '/account/user/' . $aUser[ 'user_id' ] . '/',
					'sText' => $aUser[ 'role' ]
				)
			);
		}
		
		$aData = array(
			'aColumns' => array( 
				array(
					'text' => $this->getLang( 'first_name' ),
					'url' => $oSorter->getSortUrl( 1 ),
					'img' => $oSorter->getSortDirectionImg( 1 )
				), 
				array( 
					'text' => $this->getLang( 'sure_name' ),
					'url' => $oSorter->getSortUrl(),
					'img' => $oSorter->getSortDirectionImg()
				), 
				array( 
					'text' => $this->getLang( 'email' ),
					'url' => $oSorter->getSortUrl( 2 ),
					'img' => $oSorter->getSortDirectionImg( 2 )
				), 
				array( 
					'text' => $this->getLang( 'role' ),
					'url' => $oSorter->getSortUrl( 3 ),
					'img' => $oSorter->getSortDirectionImg( 3 )
				),
			),
			'aList' => $aList,
			'mPagination' => View::factory( 'pagination', $oPagination->getViewData() )
		);
		
		$this->mTemplate->content = View::factory( 'account/item_list', $aData )->render();
		
	}
	
	
	public function coursesAction() {
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_coursesadmin' );
		
		$aList = array();
		
		// setup sorter
		$oSorter = new Module_Sorter( Model_Course::SORT_NAME );
		$oSorter->addSortOption( Model_Course::SORT_PRICE, Module_Sorter::SORT_DESC );
		$oSorter->addSortOption( Model_Course::SORT_TYPE );
		
		$oCourse = new Model_Course();
		$oCourse->setSorter( $oSorter );
		
		$iItemsPerPage = 30;
		$oPagination = new Module_Pagination( $iItemsPerPage, $oCourse->getRowsCount() );
		$oPagination->setString( array(
			'previous' => $this->getLang( 'catering.prev_f' ),
			'next' => $this->getLang( 'catering.next_f' ),
			'label' => $this->getLang( 'catering.page' )
		) );
		
		$aCourses = $oCourse->getCoursesForAccount( $this->oCurrentUser->account_id, $oPagination->getOffset(), $iItemsPerPage );
		
		foreach( $aCourses as $aCourse ) {
			$aList[] = array(
				array(
					'sLink' => '/account/course/' . $aCourse[ 'course_id' ] . '/'
					, 'sText' => $aCourse[ 'name' ]
				),
				array(
					'sText' => $aCourse[ 'price' ]
				),
				array(
					'sText' => $aCourse[ 'type' ]
				)
				
			);
		}
		
		$aData = array(
			'aColumns' => array( 
				array(
					'text' => $this->getLang( 'course_name' ),
					'url' => $oSorter->getSortUrl(),
					'img' => $oSorter->getSortDirectionImg()
				),
				array(
					'text' => $this->getLang( 'course_price' ),
					'url' => $oSorter->getSortUrl( 1 ),
					'img' => $oSorter->getSortDirectionImg( 1 )
				),
				array(
					'text' => $this->getLang( 'course_type' ),
					'url' => $oSorter->getSortUrl( 2 ),
					'img' => $oSorter->getSortDirectionImg( 2 )
				)
			),
			'aList' => $aList,
			'mPagination' => View::factory( 'pagination', $oPagination->getViewData() )
		);
		
		$this->mTemplate->content = View::factory( 'account/item_list', $aData )->render();
		
	}
	
	public function courseAction( $iId = 0 ) {
		
		if ( ( $iId !== 0 ) AND ( ! isset( $_POST[ 'submit' ] ) ) ) {							// edycja skladnika
			
			$bDelete = func_get_arg(0) == 'delete';
			
			if ( $bDelete ) {
				$iId = func_get_arg(1);
			}
			
			$iId = (int) $iId;
			
			$oModel = new Model_Course( $iId );
			
			$aRows = $oModel->getRow();
			
			$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_courseedit' );
			
			if ( ( $iId ) AND ( ! empty( $aRows ) ) AND ( $aRows[ 'account_id' ] == $this->oCurrentUser->account_id ) ) {
				
				if ( $bDelete ) {
					
					if ( ( func_num_args() == 4 ) AND ( $this->oAuth->isValidToken( func_get_arg(2) ) ) ) {
					
						if ( $oModel->delete() ) {
							
							$aMeta = $this->mTemplate->aMeta;
							$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $this->mTemplate->anchor( '/account/courses/' ) . '" />';
							$this->mTemplate->aMeta = $aMeta;
							
							$aData[ 'info' ] = $this->getLang( 'delete_course_successful' );
						} else {
							$aData[ 'info' ] = $this->getLang( 'delete_course_failed' );
						}
					
					} else {
						
						// potwierdzenie usuniecia
						
						$aData = array(
							'sQuestion' => $this->getLang( 'delete_course_question', $aRows[ 'name' ] )
							, 'sTextYes' => $this->getLang( 'Catering.ok' )
							, 'sLinkYes' => '/account/course/delete/' . $iId . '/' . $this->oAuth->getSecurityToken() . '/'
							, 'sTextNo' => $this->getLang( 'Catering.cancel' )
							, 'sLinkNo' => '/account/courses/'
						);
						
					}
					
					$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_coursedelete' );
					
				} else { // edycja
					
					$oType = new Model_Type();
	
					$aTypes = $oType->where( 'account_id', (int) $this->oCurrentUser->account_id )->getAll();
					
					// gdy nie zdefiniowano jeszcze typow przekierowujemy
					if ( ! count( $aTypes ) ) {
						$this->redirect( '/account/types/' );
						echo ' ';
					}
					
					// budujemy opcje select'a
					foreach ( $aTypes as $aType ) {
						$aOptions[] = array(
							'value' => $aType[ 'type_id' ]
							, 'name' => $aType[ 'name' ]
						);
					}
					
					$aInputs[] = array(
						'type' => 'text'
						, 'label' => $this->getLang( 'course_name' )
						, 'name' => 'name'
						, 'value' => $aRows[ 'name' ]
					);
					$aInputs[] = array(
						'type' => 'text'
						, 'label' => $this->getLang( 'course_price' )
						, 'name' => 'price'
						, 'value' => $aRows[ 'price' ]
					);
					$aInputs[] = array(
						'type' => 'select'
						, 'label' => $this->getLang( 'course_optional' )
						, 'name' => 'optional'
						, 'value' => $aRows[ 'optional' ]
						, 'items' => array(
							array(
								'value' => 0
								, 'name' => $this->getLang( 'Catering.no' )
							),
							array(
								'value' => 1
								, 'name' => $this->getLang( 'Catering.yes' )
							)
						)
					);
					$aInputs[] = array(
						'type' => 'text'
						, 'label' => $this->getLang( 'course_discount' )
						, 'name' => 'discount'
						, 'value' => $aRows[ 'discount' ]
					);
					$aInputs[] = array(
						'type' => 'select'
						, 'label' => $this->getLang( 'course_type' )
						, 'name' => 'type'
						, 'value' => $aRows[ 'type_id' ]
						, 'items' => $aOptions
					);
			
					$aData = array(
						'sTextDelete' => $this->getLang( 'Catering.delete' )
						, 'sLinkDelete' => '/account/course/delete/' . $aRows[ 'course_id' ] . '/'
						, 'item_id' => $aRows[ 'course_id' ]
						, 'aInputs' => $aInputs
					);
					
					$aData[ 'bPrintForm' ] = true;
				
				}
				
			} else {
				$aData[ 'info' ] = $this->getLang( 'course_not_found' );
			}
			
			$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_courseedit' );
			
		} elseif ( isset( $_POST[ 'submit' ] ) ) {		// zapis posilku
			
			$iId = (int) $iId;
			
			$this->saveCourse( $iId );
			return;
			
		} else {										// akcja domyślna czyli dodawanie
			
			$oType = new Model_Type();

			$aTypes = $oType->where( 'account_id', (int) $this->oCurrentUser->account_id )->getAll();
			
			// gdy nie zdefiniowano jeszcze typow przekierowujemy
			if ( ! count( $aTypes ) ) {
				$this->redirect( '/account/types/' );
				echo ' ';
			}
			
			// budujemy opcje select'a
			foreach ( $aTypes as $aType ) {
				$aOptions[] = array(
					'value' => $aType[ 'type_id' ]
					, 'name' => $aType[ 'name' ]
				);
			}
			
			$aInputs[] = array(
				'type' => 'text'
				, 'label' => $this->getLang( 'course_name' )
				, 'name' => 'name'
				, 'value' => ''
			);
			$aInputs[] = array(
				'type' => 'text'
				, 'label' => $this->getLang( 'course_price' )
				, 'name' => 'price'
				, 'value' => ''
			);
			$aInputs[] = array(
				'type' => 'select'
				, 'label' => $this->getLang( 'course_optional' )
				, 'name' => 'optional'
				, 'value' => 0
				, 'items' => array(
					array(
						'value' => 0
						, 'name' => $this->getLang( 'Catering.no' )
					),
					array(
						'value' => 1
						, 'name' => $this->getLang( 'Catering.yes' )
					)
				)
			);
			$aInputs[] = array(
				'type' => 'text'
				, 'label' => $this->getLang( 'course_discount' )
				, 'name' => 'discount'
				, 'value' => ''
			);
			$aInputs[] = array(
				'type' => 'select'
				, 'label' => $this->getLang( 'course_type' )
				, 'name' => 'type'
				, 'value' => $aTypes[0][ 'type_id' ]
				, 'items' => $aOptions
			);
	
			$aData = array(
				'aInputs' => $aInputs
				, 'bPrintForm' => true
			);
			
			
			$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_courseadd' );
			
		}
		
		$aData[ 'submit' ] = $this->getLang( 'Catering.save' );
		
		$this->mTemplate->content = View::factory( 'account/item_edit', $aData )->render();
		
	}
	
	protected function saveCourse( $iId ) {
		
		$sName = $this->post( 'name' );
		$fPrice = str_replace( ',', '.', $this->post( 'price' ) );
		$iOptional = $this->post( 'optional' );
		$iDiscount = $this->post( 'discount' );
		$iType = $this->post( 'type' );
		
		// ustawianie pol walidatora
		$oValidator = new Module_Validator();
		$oValidator->field( 'course_name', $sName )->rules( 'required|hsc' );
		$oValidator->field( 'course_price', $fPrice )->rules( 'required|tofloat' );
		$oValidator->field( 'course_optional', $iOptional )->rules( 'required|toint' );
		$oValidator->field( 'course_discount', $iDiscount )->rules( 'toint' );
		$oValidator->field( 'course_type', $iType )->rules( 'required|toint|not[0]' );
		
		// sprawdzenie poprawnosci danych
		if ( $oValidator->validate() ) {
			
			if ( $iId == 0 ) { //zapis nowego skladnika
				
				$oModel = new Model_Course();
				
				$oModel->name = $sName;
				$oModel->type_id = $iType;
				$oModel->optional = $iOptional;
				$oModel->discount = $iDiscount;
				$oModel->price = $fPrice;
				$oModel->account_id = $this->oCurrentUser->account_id;
				
				if ( $oModel->save() ) {
					
					$aMeta = $this->mTemplate->aMeta;
					$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $this->mTemplate->anchor( '/account/courses/' ) . '" />';
					$this->mTemplate->aMeta = $aMeta;
					
					$aData[ 'info' ] = $this->getLang( 'save_course_successful' );
					
				} else {
					$aData[ 'info' ] = $this->getLang( 'save_course_failed' );
				}
				
			} else {
				
				// zapis edytowanego posilku
				$oModel = new Model_Course( $iId );
				
				$aRows = $oModel->getRow();
				
				if ( ( ! empty( $aRows ) ) AND ( $aRows[ 'account_id' ] == $this->oCurrentUser->account_id ) ) {
					
					$oModel->name = $sName;
					$oModel->type_id = $iType;
					$oModel->optional = $iOptional;
					$oModel->discount = $iDiscount;
					$oModel->price = $fPrice;
					
					if ( $oModel->save() ) {
						
						$aMeta = $this->mTemplate->aMeta;
						$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $this->mTemplate->anchor( '/account/courses/' ) . '" />';
						$this->mTemplate->aMeta = $aMeta;
						
						$aData[ 'info' ] = $this->getLang( 'save_course_successful' );
						
					} else {
						$aData[ 'info' ] = $this->getLang( 'save_course_failed' );
					}
					
				} else {
					$aData[ 'info' ] = $this->getLang( 'save_course_failed' );
				}
				
			}
			
		} else { // walidacja nie przebiega pomylnie
		
			$oType = new Model_Type();

			$aTypes = $oType->where( 'account_id', (int) $this->oCurrentUser->account_id )->getAll();
			
			// gdy nie zdefiniowano jeszcze typow przekierowujemy
			if ( ! count( $aTypes ) ) {
				$this->redirect( '/account/types/' );
				echo ' ';
			}
			
			// budujemy opcje select'a
			foreach ( $aTypes as $aType ) {
				$aOptions[] = array(
					'value' => $aType[ 'type_id' ]
					, 'name' => $aType[ 'name' ]
				);
			}
			
			$aInputs[] = array(
				'type' => 'text'
				, 'label' => $this->getLang( 'course_name')
				, 'name' => 'name'
				, 'value' => $sName
			);
			$aInputs[] = array(
				'type' => 'text'
				, 'label' => $this->getLang( 'course_price')
				, 'name' => 'price'
				, 'value' => $fPrice
			);
			$aInputs[] = array(
				'type' => 'select'
				, 'label' => $this->getLang( 'course_optional')
				, 'name' => 'optional'
				, 'value' => $iOptional
				, 'items' => array(
					array(
						'value' => 0
						, 'name' => $this->getLang( 'Catering.no')
					),
					array(
						'value' => 1
						, 'name' => $this->getLang( 'Catering.yes')
					)
				)
			);
			$aInputs[] = array(
				'type' => 'text'
				, 'label' => $this->getLang( 'course_discount')
				, 'name' => 'discount'
				, 'value' => $iDiscount
			);
			$aInputs[] = array(
				'type' => 'select'
				, 'label' => $this->getLang( 'course_type')
				, 'name' => 'type'
				, 'value' => $iType
				, 'items' => $aOptions
			);
	
			$aErrors = $oValidator->getError();
			foreach( $aErrors as $sField => $aError ) {
				$sMsg .= '<br />' . $this->getLang( $aError['msg'], $this->getLang( $sField ) ) ; 
			}
			
			$aData = array(
				'error' 		=> $sMsg
				, 'submit'		=> $this->getLang( 'Catering.name')
				, 'aInputs' 	=> $aInputs
				, 'bPrintForm' 	=> true
			);
	
		}
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_coursesave');
		
		$this->mTemplate->content = View::factory( 'account/item_edit', $aData )->render();
		
	}
	
	public function mealsAction() {
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_mealadmin');
		
		// check if there is submited form - if yes we're invoking save function
		if ( isset( $_POST[ 'submit' ] ) ) {
			$aParams = func_get_args();
			$this->mealAction( $aParams );
			return;
		}
		
		$iTime = time();
		
		$aMeals = array();
		
		if ( func_num_args() > 0 ) {
			
			switch( func_get_arg(0) ) {
				
				case 'day' :
					
					if ( func_num_args() != 5 ) {
						throw new Lithium_404_Exception( 'account.invalid_number_of_params' );
					}
					
					$iYear = (int) func_get_arg(1);
					$iMonth = (int) func_get_arg(2);
					$iDay = (int) func_get_arg(3);
					
					$iSelectedDay = $iDay;
					
					$iTime = mktime( 0, 0, 0, $iMonth, $iDay, $iYear );
					
					// pobieramy posiki dla danego dnia
					$aMeals = $this->getMealsForDay( $this->oCurrentUser->account_id, $iYear, $iMonth, $iDay );
					
					$oCourse = new Model_Course();
					$aAddMeals[ 'aCourses' ] = $this->getSortedCourses();
					
					$aAddMeals[ 'aCurrentWeek' ] = array( date( 'Y-m-d', $iTime ) );
					
					
					break;
					
				case 'week' :
				
					if ( func_num_args() != 5 ) {
						throw new Lithium_404_Exception( 'account.invalid_number_of_params' );
					}	
					
					$iYear = (int) func_get_arg(1);
					$iMonth = (int) func_get_arg(2);
					$iSelectedWeek = (int) func_get_arg(3);
					
					$iTime = mktime( 0, 0, 0, $iMonth, 1, $iYear );
					
					// pobieramy posiki dla danego tygodnia
					$aMeals = $this->getMealsForWeek( $this->oCurrentUser->account_id, mktime( 0, 0, 0, $iMonth, 1 + ( $iSelectedWeek * 7 ), $iYear ) );
					
					$oCourse = new Model_Course();
					$aAddMeals[ 'aCourses' ] = $this->getSortedCourses();
					$aTmp = $this->getMonthData( $iTime, $iSelectedWeek );
					foreach( $aTmp as $iTDay ) {
						if ( ! empty( $iTDay ) ) {
							$aAddMeals[ 'aCurrentWeek' ][] = $iYear . '-' . str_pad( $iMonth, 2, '0', STR_PAD_LEFT ) . '-' . str_pad( $iTDay, 2, '0', STR_PAD_LEFT );
						}
					}
					
					
					break;
					
				case 'month' :
				
					if ( func_num_args() != 4 ) {
						throw new Lithium_404_Exception( 'account.invalid_number_of_params' );
					}
					
					$iYear = (int) func_get_arg(1);
					$iMonth = (int) func_get_arg(2);
					
					$iTime = mktime( 0, 0, 0, $iMonth, 1, $iYear );
					break;
					
			}
			
		} 
		
		// generowanie listy
		$aList = array();
		if ( ! empty( $aMeals ) ) {
			
			foreach( $aMeals as $sMealId => $aMeal ) {
				$aList[] = array(
					array(
						'sLink' => '/account/meal/' . $sMealId . '/'
						, 'sText' => $aMeal[ 'date' ]
					),
					array(
						'sLink' => '/account/meal/' . $sMealId . '/'
						, 'sText' => $aMeal[ 'fname' ]
					),
					array(
						'sLink' => '/account/meal/' . $sMealId . '/'
						, 'sText' => $aMeal[ 'lname' ]
					),
					array(
						'sText' => $aMeal[ 'price' ]
					)
					
				);
			}
			
		} 
		
		$iYear = date( 'Y', $iTime ); 
		$iMonth = date( 'n', $iTime ); 
		
		
		// sterowanie kalendarzem
		$iYearPrev = $iYearNext = $iYear;
		$iMonthPrev = $iMonth - 1;
		if( $iMonthPrev < 1 ) {
			$iMonthPrev = 12;
			$iYearPrev = $iYear - 1;
		}
		$iMonthNext = $iMonth + 1;
		if( $iMonthNext > 12 ) {
			$iMonthNext = 1;
			$iYearNext = $iYear + 1;
		}
		
		$aMonth = $this->getMonthData( $iTime );
		$aCalendar = array();
		$iWeekIndex = 0;
		foreach ( $aMonth as $aWeek ) { 
			foreach ( $aWeek as $iDayIndex => $sDay ) { 
				if ( ! empty( $sDay ) ) {
					
					$aCalendar[ $iWeekIndex ][ $iDayIndex ][ 'sLink' ] = '/account/meals/day/' . $iYear . '/' . $iMonth . '/' . $sDay . '/';
					$aCalendar[ $iWeekIndex ][ $iDayIndex ][ 'sText' ] = $sDay;
					if ( ( isset( $iSelectedWeek ) && ( $iWeekIndex == $iSelectedWeek ) ) || ( isset( $iSelectedDay ) && ( $iSelectedDay == $sDay ) ) ) 
						$aCalendar[ $iWeekIndex ][ $iDayIndex ][ 'iClass' ] = 0;
					
				} else {
					$aCalendar[ $iWeekIndex ][] = '';
				}
			}
			$aCalendar[ $iWeekIndex ][] = array( 'sLink' => '/account/meals/week/' . $iYear . '/' . $iMonth . '/' . $iWeekIndex . '/', 'sText' => 'week' );
			$iWeekIndex++;
		}
		
		// dane do kalendarza
		$aCalendarData = array(
			'sPrevLink' => '/account/meals/month/' . $iYearPrev . '/' . $iMonthPrev . '/'
			, 'sNextLink' => '/account/meals/month/' . $iYearNext . '/' . $iMonthNext . '/'
			, 'aCalendar' => $aCalendar
			, 'sMonthName' => $this->getLang( 'catering.month_names[' . ( $iMonth - 1 ) . ']' ) . ' ' . $iYear
			, 'aWeekDays' => $this->getLang( 'catering.short_week_days' )
		);
		
		
		// dane do listy dan
		$aListData = array(
			'aColumns' => array( $this->getLang( 'meal_date'), $this->getLang( 'meal_name'), $this->getLang( 'meal_ingeredients'), $this->getLang( 'meal_price') )
			, 'aList' => $aList
		);
		
		
		// pozostale dane
		$aAddMeals[ 'submit' ] = $this->getLang( 'Catering.save');
		$aAddMeals[ 'sAddMeal' ] = $this->getLang( 'meal_add');
		$aAddMeals[ 'sSendMail' ] = $this->getLang( 'send_mail');
		$aAddMeals[ 'sSendMailLink' ] = '/account/mailer/';
		$aAddMeals[ 'sDate' ] = $this->getLang( 'meal_date');
		$aAddMeals[ 'sName' ] = $this->getLang( 'meal_name');
		$aAddMeals[ 'sPrice' ] = $this->getLang( 'meal_price');
		$aAddMeals[ 'sNull' ] = '';
		$aAddMeals[ 'action' ] = '/account/meal/';
		
		
		$aData = array(
			'calendar' 		=> View::factory( 'calendar', $aCalendarData )->render()
			, 'list' 		=> View::factory( 'account/item_list', $aListData )->render()
			, 'sNull' 		=> ''
			, 'aAddMeals'	=> ( isset( $aAddMeals ) ? $aAddMeals : array() )
			, 'aCurrentWeek' => ( isset( $aCurrentWeek ) ? $aCurrentWeek : array() )
		);
		
		$this->mTemplate->content = View::factory( 'account/meals', $aData )->render();
		
	}
	
	public function mealAction( $iId = 0 ) {
		
		if ( ( func_num_args() > 2 ) AND ( func_get_arg(0) == 'delete' ) ) { // usuwanie dania
			
			$iId = (int) func_get_arg(1);

			$oModel = new Model_Meal( $iId );
			
			$aMeal = $oModel->getRow();
			
			$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_mealdelete');
			
			if ( ( $iId ) AND ( ! empty( $aMeal ) ) AND ( $aMeal[ 'account_id' ] == $this->oCurrentUser->account_id ) ) {
				
				if ( ( func_num_args() == 4 ) AND ( $this->oAuth->isValidToken( func_get_arg(2) ) ) ) {
				
					if ( $oModel->delete() ) {
						
						$aMeta = $this->mTemplate->aMeta;
						$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $this->mTemplate->anchor( '/account/meals/' ) . '" />';
						$this->mTemplate->aMeta = $aMeta;
						
						$aData[ 'info' ] = $this->getLang( 'delete_meal_successful');
					} else {
						$aData[ 'info' ] = $this->getLang( 'delete_meal_failed');
					}
				
				} else {
					
					// potwierdzenie usuniecia
					
					$aData = array(
						'sQuestion' => $this->getLang( 'delete_meal_question')
						, 'sTextYes' => $this->getLang( 'Catering.ok')
						, 'sLinkYes' => '/account/meal/delete/' . $iId . '/' . $this->oAuth->getSecurityToken() . '/'
						, 'sTextNo' => $this->getLang( 'Catering.cancel')
						, 'sLinkNo' => '/account/meal/' . $iId . '/'
					);
					
				}
				
			} else {
				$aData[ 'info' ] = $this->getLang( 'meal_not_found');
			}
			
		} elseif ( isset( $_POST[ 'submit' ] ) ) {
			
			$aData[ 'error' ] = $this->saveMeal( $this->oCurrentUser->account_id, (int) $iId );
			if ( $aData[ 'error' ] === true ) {
				return;
			}
		
		} elseif ( ( (int) $iId ) != 0 ) {		// edycja danych
			
			$oMeal = new Model_Meal( $iId );
			
			$aMeal = $oMeal->where( 'account_id', (int) $this->oCurrentUser->account_id )->getRow();
			
			if ( ! empty( $aMeal ) ) {
				
				// pozostale dane
				$aAddMeals[ 'submit' ] = $this->getLang( 'Catering.save');
				$aAddMeals[ 'sAddMeal' ] = $this->getLang( 'meal_add');
				$aAddMeals[ 'sDate' ] = $this->getLang( 'meal_date');
				$aAddMeals[ 'sName' ] = $this->getLang( 'meal_name');
				$aAddMeals[ 'sPrice' ] = $this->getLang( 'meal_price');
				$aAddMeals[ 'sNull' ] = '';
				$aAddMeals[ 'aCourses' ] = $this->getSortedCourses();
				$aAddMeals[ 'aCurrentWeek' ] = array( $aMeal[ 'date' ] );
				$aAddMeals[ 'sDeleteText' ] = $this->getLang( 'Catering.delete');
				$aAddMeals[ 'sDeleteLink' ] = '/account/meal/delete/' . $iId . '/';
				$aAddMeals[ 'action' ] = '/account/meal/' . $iId . '/';
				
				$aData[ 'sName' ] = $aMeal[ 'name' ];
				$aData[ 'fPrice' ] = $aMeal[ 'price' ];
				
				$aData[ 'aChoosedCourses' ] = $oMeal->getCoursesIds();
				$aData[ 'aAddMeals' ] = $aAddMeals;
				$aData[ 'bShowForm' ] = true;
			
			} else {
				$aData[ 'info' ] = $this->getLang( 'meal_not_found');
			}
			
		} else { // niepoprawny adres strony wiec przekierowujemy
			
			$this->redirect( '/account/meals/' );
			echo ' ';
			return;
			
		}
		
		
		$this->mTemplate->content = View::factory( 'account/meals', $aData )->render();
		
	}
	
	public function typesAction() {
		
		$this->oXajax->register( array( 'typesSaveAjax', $this, 'typesSaveAjax' ) );
		
		if ( isset( $_POST[ 'submit' ] ) ) {
			$this->saveType();
			return;
		}
		
		$aList = array();
		$aListAction = array();
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_typeadmin');
		
		$oType = new Model_Type();
		
		$aTypes = $oType->where( 'account_id', (int) $this->oCurrentUser->account_id )->getAll();
		
		foreach ( $aTypes as $aType ) {
			$aList[] = array(
				'id' => array(
					'id' => 'list_id_' . $aType[ 'type_id' ],
					'value' => $aType[ 'type_id' ]
				),
				'name' => array(
					'id' => 'list_name_' . $aType[ 'type_id' ],
					'value' => $aType[ 'name' ]
				),
				'action' => array(
					'id' => 'list_action_' . $aType[ 'type_id' ],
					'value' => $this->getLang( 'Catering.edit'),
					'onclick' => 'ShowEditField(\'list_name_' . $aType[ 'type_id' ] . '\',this);'
				)
			);
		} // foreach
		
		$aData[ 'sName' ] = $this->getLang( 'type_name');
		$aData[ 'submit' ] = $this->getLang( 'Catering.save');
		$aData[ 'sAddType' ] = $this->getLang( 'type_add');
		$aData[ 'aColumns' ] = array(
			$this->getLang( 'Catering.id'),
			$this->getLang( 'type_name'),
			''
		);
		$aData[ 'aList' ] = $aList;
		$aData[ 'aListAction' ] = $aListAction;
		
		$this->mTemplate->content = View::factory( 'account/item_list_type', $aData )->render();
		
	}
	
	public function typesSaveAjax( $sId, $sValue, $sButtonId ) {
		
		$oResp = new xajaxResponse();
		
		// wydobywanie id
		$iTypeId =  str_replace( 'list_name_', '', $sId );
		
		// walidacja danych
		$oValidator = new Module_Validator();
		$oValidator->field( 'type_id', $iTypeId )->rules( 'required|toint|not[0]' );
		$oValidator->field( 'type_name', $sValue )->rules( 'required|hsc' );
		
		if ( $oValidator->validate() ) {
			
			// zapis w bazie
			$oType = new Model_Type();
			$oType->type_id = $iTypeId;
			$oType->name = $sValue;
			
			try {
				if ( $oType->save() ) {
					$oResp->script( sprintf( 'FieldEditManager.CancelEditing( "%s","%s" );', $sId, $sButtonId ) );
					$oResp->assign( $sId, 'innerHTML', $sValue );
				} else {
					$oResp->script( sprintf( 'alert("%s");', $this->getLang( 'save_type_failed' ) ) );
				}
			} catch ( Exception $e ) {
				$oResp->script( sprintf( 'alert("%s");', $this->getLang( 'save_type_failed' ) ) );
			}
			
		} else {
			$oResp->script( sprintf( 'alert("%s");', $this->getLang( 'save_type_failed' ) ) );
		}
		
		return $oResp;
		
	}
	
	protected function saveType() {
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_typesave');
		
		$sName = $this->post( 'name' );
		
		$oValidator = new Module_Validator();
		$oValidator->field( 'type_name', $sName )->rules( 'required' );
		
		if ( $oValidator->validate() ) {
			
			$oType = new Model_Type();
			
			$oType->name = $sName;
			$oType->account_id = $this->oCurrentUser->account_id;
			
			if ( $oType->save() ) {
				
				$aMeta = $this->mTemplate->aMeta;
				$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $this->mTemplate->anchor() . '" />';
				$this->mTemplate->aMeta = $aMeta;
				
				$sInfo = $this->getLang( 'save_type_successful');
				
			} else {
				$sInfo = $this->getLang( 'save_type_failed');
			}
			
		} else {
			$aErrors = $oValidator->getError();
			foreach( $aErrors as $sField => $aError ) {
				$sMsg .= '<br />' . $this->getLang( $aError['msg'], $this->getLang( $sField ) ) ; 
			}
			$sInfo = $this->getLang( 'input_validation_failed') . $sMsg;
		}
		
		$this->mTemplate->content = $sInfo;
		
	}
	
	public function ordersAction() {
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_orderedit');
		
		
		$iUserId = 0;
		$iWeekNumber = 0;
		if ( isset( $_POST[ 'user_id' ] ) ) {
			$this->redirect( '/account/orders/' . ( (int) $_POST[ 'user_id' ] ) . '/' );
			echo ' ';
			return;
			$iUserId = (int) $_POST[ 'user_id' ];
		} elseif ( func_num_args() > 0 ) {
			
			$iUserId = (int) func_get_arg(0);
			
			if ( func_num_args() > 1 ) {
				$iWeekNumber = (int) func_get_arg(1);
			}
			
		}
		
		
		$oUser = new Model_User();
		$aUsers = $oUser->where( 'account_id', (int) $this->oCurrentUser->account_id )->orderby( 'name' )->getAll();
		
		$aUserIds = array();
		$aOptions = array();
		foreach ( $aUsers as $aUser ) {
			
			$aUserIds[] = $aUser[ 'user_id' ];
			$aOptions[] = array(
				'value' => $aUser[ 'user_id' ],
				'name' => $aUser[ 'name' ] . ' ' . $aUser[ 'fname' ]
			);
			
		}
		
		$aEditData[ 'bPrintForm' ] = true;
		$aEditData[ 'sAction' ] = '/account/orders/';
		$aEditData[ 'submit' ] = $this->getLang( 'order_show');
		$aEditData[ 'aInputs' ][] = array(
			'type' => 'select',
			'label' => $this->getLang( 'order_owner'),
			'name' => 'user_id',
			'value' => $iUserId,
			'items' => $aOptions
		);
		
		$aData[ 'view_item_edit' ] = View::factory( 'account/item_edit', $aEditData )->render();
		
		
		
		
		// gdy wybrano uzytkownika
		if ( $iUserId != 0 ) {
			
			// sprawdzamy czy wybrany user nalezy do tego konta
			if ( in_array( $iUserId, $aUserIds ) ) {
				
				$mData = $this->generateEnrolData( $iUserId, $iWeekNumber );
				
				if ( is_array( $mData ) ) {
					$aData[ 'view_enrol' ] = View::factory( 'catering/enrol', $mData )->render();
				} else {
					$aData[ 'view_enrol' ] = $mData;
				}
		
			}
			
		}
		
		$this->mTemplate->content = View::factory( 'account/order_edit', $aData )->render();
		
	}
	
	
	protected function generateEnrolData( $iUserId, $iWeekNumber = 0 ) {
		
		// gdy nie wybrano zadnej daty
		$iDay = date( 'j' ); 
		$iMonth = date( 'n' ); 
		$iYear = date( 'Y' );
		$iTime = time();
		
		// jezeli wybrano inny tydzien
		if ( $iWeekNumber != 0 ) {
			
			$iWeekNumber = (int) $iWeekNumber;
			$iTime = mktime( 0, 0, 0, $iMonth, $iDay + ( $iWeekNumber * 7 ), $iYear );
			
			$iDay = date( 'j', $iTime ); 
			$iMonth = date( 'n', $iTime ); 
			$iYear = date( 'Y', $iTime );
			
		}
		
		$iWeekDay = ( date( 'w', $iTime ) == 0 ? 7 : date( 'w', $iTime ) );
		
		
		// obliczamy poczatek i koniec tygodnia
		$iStartTime = mktime( 0, 0, 0, $iMonth, $iDay - ( $iWeekDay - 1 ) , $iYear );
		$sStartDate = date( 'Y-m-d', $iStartTime );
		
		$iEndTime = mktime( 0, 0, 0, $iMonth, $iDay + ( 7 - $iWeekDay ) , $iYear );
		$sEndDate = date( 'Y-m-d', $iEndTime );
		
		
		$aWeek = array();
		

		$oMeal = new Model_Meal();
		
		// zapisujemy
		if ( isset( $_POST[ 'submit' ] ) AND ( ! isset( $_POST[ 'user_id' ] ) ) ) {
			
			$aMeals = $oMeal->getMeals( $this->oCurrentUser->account_id, $sStartDate, $sEndDate );
			$bSaved = $this->saveOrders( $this->oCurrentUser->account_id, $iUserId, $aMeals );
			if ( $bSaved ) {
				
				$aMeta = $this->mTemplate->aMeta;
				$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $this->mTemplate->anchor() . '" />';
				$this->mTemplate->aMeta = $aMeta;
		
				return $this->getLang( 'Catering.save_meals_successfully');
				
			} else {
				return $this->getLang( 'Catering.save_meals_failed');
			}
			
		}
		
		// wyszukujemy posików i grupujemy po dacie
		$aMeals = $oMeal->getMeals( $this->oCurrentUser->account_id, $sStartDate, $sEndDate, 'date' );
		
		// wyszukujemy zamowienia
		$aOrderedMeals = array();
		$oOrder = new Model_Order();
		$aOrders = $oOrder->where( 'user_id', $iUserId )->getAll();
		foreach( $aOrders as $aOrder ) {
			$aOrderedMeals[] = $aOrder[ 'meal_id' ];
		}
		
		foreach ( $aMeals as $sDate => $aMeal ) {
			
			$aWeek[ $sDate ][ 'aMeals' ][0] = array(
				'name' 			=> $this->getLang( 'Catering.no_order')
				, 'meal_id' 	=> 0
				, 'optional' 	=> 0
				, 'bChecked' 	=> false
				, 'bDisabled' 	=> false
//				, 'bDisabled' 	=> ( strtotime( $sDate . ' ' . $sOrderEndTime ) < time() )
			);
			
			$aWeek[ $sDate ][ 'sWeekday' ] 	= date( 'l', strtotime( $sDate ) );
			
			foreach ( $aMeal as $iMealId => $aMealData ) {
				
				$aWeek[ $sDate ][ 'aMeals' ][]		= array(
					'name' 			=> $aMealData[ 'lname' ]
					, 'fname'		=> $aMealData[ 'fname' ]
					, 'meal_id' 	=> $iMealId
					, 'price' 		=> $aMealData[ 'price' ] . $this->getLang( 'Catering.currency')
					, 'optional' 	=> $aMealData[ 'optional' ]
					, 'bChecked' 	=> ( in_array( $iMealId, $aOrderedMeals ) ? true : false )
					, 'bDisabled' 	=> false
//					, 'bDisabled' 	=> ( strtotime( $sDate . ' ' . $sOrderEndTime ) < time() )
				);
			
			}
			
		}
		
		$aData[ 'aWeek' ] = $aWeek;
		$aData[ 'sNextText' ] = $this->getLang( 'Catering.next');
		$aData[ 'sNextLink' ] = '/account/orders/' . $iUserId . '/' . ( $iWeekNumber + 1 ) . '/';
		$aData[ 'sPrevText' ] = $this->getLang( 'Catering.prev');
		$aData[ 'sPrevLink' ] = '/account/orders/' . $iUserId . '/' . ( $iWeekNumber - 1 ) . '/';
		$aData[ 'submit' ] = 'Zapisz';
		
		return $aData;
		
	}
	
	protected function saveOrders( $iAccountId, $iUserId, $aMeals ) {
		
		$aMealIdsAll = array();
		
		// get submited meals id
		for ( $i = 0; $i < 7; $i++ ) {
			if ( isset( $_POST[ 'day' . $i ] ) ) {
				$aMealIdsAll = array_merge( $aMealIdsAll, $_POST[ 'day' . $i ] );
			}
		}
		
		
		// wyszukujemy zamowienia
		$aOrderedMeals = array();
		$aOrderIndex = array();
		$oOrder = new Model_Order();
		$oCourse = new Model_Course();

		$iIndex = 0;
		$sDate = '';
		
		// rozpoczynamy transakcje
		$oOrder->transaction( true );
		
		// iterujemy po posilkach w danym tyg.
		foreach( $aMeals as $iMealId => $aMeal ) {
			
			// zmieniamy na kolejny dzien index tablicy $aMealIds
			if ( $sDate != $aMeal[ 'date' ] ) {
				
				// czyscimy zapisy na wszystkie dania tego dnia
				$oOrder->removeEnrols( $iAccountId, $iUserId, $aMeal[ 'date' ] );

				$sDate = $aMeal[ 'date' ];
				$iIndex++;
				
			}
			
			// sprawdzamy czy posilek z danego tyg zostal wybrany przez uzytkownika
			if ( in_array( $iMealId, $aMealIdsAll ) ) {
				
				// zapisujemy nowy
				$oOrder->reset();
				
				$oOrder->date = $aMeal[ 'date' ];
				$oOrder->user_id = (int) $iUserId;
				$oOrder->meal_id = (int) $iMealId;
				$oOrder->price = (float) $aMeal[ 'price' ];
				$oOrder->account_id = (int) $iAccountId;
				
				if ( ! $oOrder->save() ) {
					throw new Lithium_Exception( 'Catering.saving_failed' );
				}
				
				// sprawdzamy ilosc zamowien na ten dzien
				$iCount = $oOrder->countOrders( (int) $iUserId, $aMeal[ 'date' ] );
				if ( $iCount > 1 ) {
					
					$aOrders = $oOrder->getOrdersForDiscount( (int) $iUserId, $aMeal[ 'date' ] );
					
					foreach ( $aOrders as $aOrder ) {
						
						$aOrder[ 'discount' ] = (int) $aOrder[ 'discount' ];
						
						// gdy znizka rowna 0 nie ma co przeliczac
						if ( $aOrder[ 'discount' ] ) {
							
							$oOrder->reset();
							$oOrder->order_id = $aOrder[ 'order_id' ];
							$oOrder->price = round( ( $aOrder[ 'price' ] * $aOrder[ 'discount' ] ) / 100, 2 );
							
							if ( ! $oOrder->save() ) {
//								throw new Lithium_Exception( 'catering.saving_failed' );
							}
				
						}
						
					}
					
				}
				
			}
			
		}
		
		if ( ! $oOrder->transaction() ) {
			if ( IN_PRODUCTION ) {
				return false;
			} else {
				throw new Lithium_Exception( 'catering.saving_failed' );
			}
		}
		
		return true;
		
	}
	
	
	/**
	 * Saves meal in db
	 */
	protected function saveMeal( $iAccountId, $iId = 0 ) {
		
		$iAccountId = (int) $iAccountId;
		$aCoursesIds = array();
		
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'section_title_mealsave');
		
		$sDate = $this->post( 'date' );
		$aCourses = $this->post( 'courses' );
		$fPrice = $this->post( 'price' );
		$sName = $this->post( 'name' );
		
		foreach ( $aCourses as $iCourseId ) {
			if ( ( (int) $iCourseId ) > 0 ) {
				$aCoursesIds[] = $iCourseId;
			}
		}
		
		$iCountCourses = count( $aCoursesIds );
		
		$oValidator = new Module_Validator();
		$oValidator->field( 'meal_date', $sDate, $this->getLang( 'meal_date') )->rules( 'required|date' );
		$oValidator->field( 'meal_price', $fPrice, $this->getLang( 'meal_price') )->rules( 'required|tofloat' );
		$oValidator->field( 'meal_ingeredients', $iCountCourses, $this->getLang( 'meal_ingeredients') )->rules( 'required|not[0]' );
		
		for ( $i = 0; $i < count( $aCourses ); $i++ ) {
			$oValidator->field( 'meal_ingeredient' . $i, $aCourses[ $i ], $this->getLang( 'meal_ingeredient') . ( $i + 1 ) )->rules( 'required|toint' );
		}
		
		if ( $oValidator->validate() ) {
			
			// sprawdzamy czy posi�ki naleza do tego konta
			$oCourse = new Model_Course();

			$aResult = $oCourse->where( 'account_id', $iAccountId )->in( 'course_id', $aCoursesIds )->getAll();
			if ( count( $aResult ) != count( $aCoursesIds ) ) {
				$aData[ 'info' ] = $this->getLang( 'courses_not_found');
			}
			
			if ( $iId == 0 ) {
				
				// tworzenie nowego dania
				$oMeal = new Model_Meal();
		
				$oMeal->date = $sDate;
				$oMeal->price = $fPrice;
				if ( ! empty( $sName ) ) $oMeal->name = $sName;
				$oMeal->account_id = $iAccountId;
				
				if ( $oMeal->save() ) {
					
					$iMealId = $oMeal->getInsertId();
					unset( $oMeal );
					// tworzenie polaczen dania ze skladnikami
					$oMeal = new Model_Meal( $iMealId );
					$oMeal->getRow();
					if ( $oMeal->setCourses( $aCoursesIds ) ) {
						
						$aMeta = $this->mTemplate->aMeta;
						$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $_SERVER[ 'HTTP_REFERER' ] . '" />';
						$this->mTemplate->aMeta = $aMeta;
						
						$aData[ 'info' ] = $this->getLang( 'save_meal_successful');
						
					} else {
						$aData[ 'info' ] = $this->getLang( 'save_meal_courses_failed');
					}
					
					
					
				} else {
					$aData[ 'error' ] = $this->getLang( 'save_meal_failed');
				}
			
			} else {
				
				// edycja dania
				$oMeal = new Model_Meal( $iId );
				$oMeal->getRow();
		
				$oMeal->date = $sDate;
				$oMeal->price = $fPrice;
				if ( ! empty( $sName ) ) $oMeal->name = $sName;
				$oMeal->account_id = $iAccountId;
				
				if ( $oMeal->save() ) {
					
					// sprawdzamy czy skladniki ulegly zmianie
					$oldCourses = $oMeal->getCoursesIds();
					
					$aDiff = array_diff( $oldCourses, $aCoursesIds );
					
					if ( count( $aDiff ) ) {
					
						// usowamy stare skladniki dania
						$oMeal->removeCourses();
						
						// tworzenie polaczen dania ze skladnikami
						if ( $oMeal->setCourses( $aCoursesIds ) ) {
							
							$aMeta = $this->mTemplate->aMeta;
							$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $this->mTemplate->anchor() . '" />';
							$this->mTemplate->aMeta = $aMeta;
							
							$aData[ 'info' ] = $this->getLang( 'save_meal_successful');
							
						} else {
							$aData[ 'info' ] = $this->getLang( 'save_meal_courses_failed');
						}
					
					} else {
						$aMeta = $this->mTemplate->aMeta;
						$aMeta[] = '<meta http-equiv="refresh" content="1;url=' . $this->mTemplate->anchor() . '" />';
						$this->mTemplate->aMeta = $aMeta;
						$aData[ 'info' ] = $this->getLang( 'save_meal_successful');
					}
					
				} else {
					$aData[ 'error' ] = $this->getLang( 'save_meal_courses_failed');
				}
				
			}
				
			
		} else {
			
			$aAddMeals[ 'aCurrentWeek' ] = array( $sDate );
			
			$aErrors = $oValidator->getError();
			foreach( $aErrors as $sField => $aError ) {
				$sMsg .= '<br />' . $this->getLang( $aError['msg'], $aError['field_name'] ) ; 
			}
			$aData[ 'error' ] = $this->getLang( 'input_validation_failed') . $sMsg;
			
		}
		
		$aAddMeals[ 'aCourses' ] = $this->getSortedCourses();
		
		// pozostale dane
		$aAddMeals[ 'submit' ] = $this->getLang( 'Catering.save');
		$aAddMeals[ 'sAddMeal' ] = $this->getLang( 'meal_add');
		$aAddMeals[ 'sDate' ] = $this->getLang( 'meal_date');
		$aAddMeals[ 'sName' ] = $this->getLang( 'meal_name');
		$aAddMeals[ 'sPrice' ] = $this->getLang( 'meal_price');
		$aAddMeals[ 'sNull' ] = '';
		if ( (int) $iId != 0 ) {
			$aAddMeals[ 'sDeleteText' ] = $this->getLang( 'Catering.delete');
			$aAddMeals[ 'sDeleteLink' ] = '/account/meal/delete/' . $iId . '/';
		}
		
		if ( isset( $aCoursesIds ) ) {
			$aData[ 'aOrderedCourses' ] = $aCoursesIds;
		}
		$aData[ 'aAddMeals' ] = $aAddMeals;
		$aData[ 'bShowForm' ] = true;
		$aData[ 'sName' ] = $sName;
		$aData[ 'fPrice' ] = $fPrice;
		
		
		$this->mTemplate->content = View::factory( 'account/meals', $aData )->render();
		
		return true;
		
	}
	
	/**
	 * Zwraca skladniki w tablicy asocjacyjnej pogrupowane wzgledem typu
	 *
	 */
	protected function getSortedCourses() {
		
		$aResult = array();
		
		$oType = new Model_Type();
		$oCourse = new Model_Course();
		
//		$aTypes = $oType->where( 'account_id', (int) $this->oCurrentUser->account_id )->
		
		$aCourses = $oCourse->getCoursesForAccount( (int) $this->oCurrentUser->account_id );
		
		foreach ( $aCourses as $aCourse ) {
			
			$aResult[ $aCourse[ 'type' ] ][] = array(
				'course_id' => $aCourse[ 'course_id' ]
				, 'name' => $aCourse[ 'name' ]
				, 'price' => $aCourse[ 'price' ]
			);
			
		}
		
		return $aResult;
		
	}
	
	public function summaryAction() {
		
		if ( func_num_args() > 0 ) {
			
			switch ( func_get_arg(0) ) {
				case 'users' :
					$this->summaryUsers();
					break;
				default :
					$this->summaryEnrols();
					
			}
			
		} else {
			$this->summaryEnrols();
		}
		
	}
	
	
	protected function summaryEnrols() {
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'summary_meals');
		
//		$aCalendarData = $this->generateCalendarData( time(), '/account/summary/' );		
//		$aData[ 'calendar' ] = View::factory( 'calendar', $aCalendarData )->render();
		
		$sCurrentDate = date( 'Y-m-d' );
		
		$bSeparateDay = false;
		
		if ( isset( $_POST[ 'submit' ] ) ) {
			
			$sFrom = $this->post( 'from' );
			$sTo = $this->post( 'to' );
			
			$oValidator = new Module_Validator();
			
			$oValidator->field( 'summary_from', $sFrom, $this->getLang( 'summary_from') )->rules( 'required|date' );
			$oValidator->field( 'summary_to', $sTo, $this->getLang( 'summary_to') )->rules( 'required|date' );
			
			if ( $oValidator->validate() ) {
				
				$bSeparateDay = isset( $_POST[ 'separate_day' ] ) ? (bool) $_POST[ 'separate_day' ] : false;
				
				$oOrder = new Model_Order();
				
				$aSummary = $oOrder->getSummaryDay( (int) $this->oCurrentUser->account_id, $sFrom, $sTo, $bSeparateDay );
				
//				$aData[ 'error' ] = '[<pre>' . print_r( $aSummary, 1 ) . '</pre>]';
				
				$iSuma = 0;
				
				// start
				
				if ( $bSeparateDay ) {
					
					foreach( $aSummary as $aPosition ) {
						
						if ( ! isset( $aData[ 'aSummary' ][ $aPosition[ 'date' ] ][ 'aColumns' ] ) ) {
							$aData[ 'aSummary' ][ $aPosition[ 'date' ] ][ 'aColumns' ] = $this->getLang( 'account.summary_columns' );
							$aData[ 'aSummary' ][ $aPosition[ 'date' ] ][ 'sDate' ] = $aPosition[ 'date' ];
							$aData[ 'aSummary' ][ $aPosition[ 'date' ] ][ 'sWeekDay' ] = $this->getLang( 'Catering.week_days[' . date( 'w', strtotime( $aPosition[ 'date' ] ) ) . ']' );
						}
							
						$aData[ 'aSummary' ][ $aPosition[ 'date' ] ][ 'aPositions' ][] = $aPosition;
						
						$iSuma = isset( $aData[ 'aSummary' ][ $aPosition[ 'date' ] ][ 'aFooter' ] ) ? $aData[ 'aSummary' ][ $aPosition[ 'date' ] ][ 'aFooter' ][ 3 ] + $aPosition[ 'price' ] : $aPosition[ 'price' ];
						$aData[ 'aSummary' ][ $aPosition[ 'date' ] ][ 'aFooter' ] = array ( '', '', '', $iSuma );
						
					}
					
				} else {
				
					foreach( $aSummary as $aPosition ) {
						$iSuma += $aPosition[ 'price' ];
					}
					
					$aData[ 'aSummary' ][ 0 ][ 'aColumns' ] = $this->getLang( 'summary_columns' );
					$aData[ 'aSummary' ][ 0 ][ 'aPositions' ] = $aSummary;
					$aData[ 'aSummary' ][ 0 ][ 'aFooter' ] = array ( '', '', '', $iSuma );
					$aData[ 'aSummary' ][ 0 ][ 'sDate' ] = $sFrom . ' - ' . $sTo;
					
				}
				
				// end
				
			} else {
			
				$aErrors = $oValidator->getError();
				foreach( $aErrors as $sField => $aError ) {
					$sMsg .= '<br />' . $this->getLang( $aError['msg'], $aError['field_name'] ) ; 
				}
				$aData[ 'error' ] = $this->getLang( 'input_validation_failed' ) . $sMsg;
				
			}
			
		} else {
			$sFrom = date( 'Y-m-d' );
			$sTo = date( 'Y-m-d' );
		}

		

		$aData[ 'aForm' ] = array(
			'sPeriod' => $this->getLang( 'summary_period' ),
			'sFrom' => $this->getLang( 'summary_from' ),
			'sTo' => $this->getLang( 'summary_to' ),
			'sSeparateDay' => $this->getLang( 'summary_separate_days' ),
			'sDateFrom' => $sFrom,
			'sDateTo' => $sTo,
			'bSeparateDay' => $bSeparateDay,
			'sSubmit' => $this->getLang( 'summary_generate' ),
		);
		
		$this->mTemplate->content = View::factory( 'account/summary_current', $aData )->render();
		
	}
	
	protected function summaryUsers() {
		
		$this->mTemplate->sSectionTitle = $this->getLang( 'summary_meals' );
		
		
		$sCurrentDate = date( 'Y-m-d' );
		
		if ( isset( $_POST[ 'submit' ] ) ) {
			
			$sFrom = $this->post( 'from' );
			$sTo = $this->post( 'to' );
			
			$oValidator = new Module_Validator();
			
			$oValidator->field( 'summary_from', $sFrom, $this->getLang( 'summary_from' ) )->rules( 'required|date' );
			$oValidator->field( 'summary_to', $sTo, $this->getLang( 'summary_to' ) )->rules( 'required|date' );
			
			if ( $oValidator->validate() ) {
				
				$oOrder = new Model_Order();
				
				// pobieramy wartosc jaka zwraca firma
				$iEmployeePercent = $this->oCurrentUser->get( 'account_id' )->employee_percent;
		
				$aSummary = $oOrder->getSummaryForUsers( (int) $this->oCurrentUser->account_id, $sFrom, $sTo );
				
				$aData[ 'aSummary' ] = $this->generateSummaryDataForUsers( $aSummary, $iEmployeePercent );
				
			} else {
			
				$aErrors = $oValidator->getError();
				foreach( $aErrors as $sField => $aError ) {
					$sMsg .= '<br />' . $this->getLang( $aError['msg'], $aError['field_name'] ) ; 
				}
				$aData[ 'error' ] = $this->getLang( 'input_validation_failed' ) . $sMsg;
				
			}
			
			// sprawdzamy czy podpiac widok dla excela czy normalny
			if ( $_POST[ 'submit' ] == $this->getLang( 'summary_generate_excel' ) ) {
				$this->mTemplate = View::factory( 'account/summary_users_excel', $aData );
				return;		
			}
			
		} else {
			$sFrom = date( 'Y-m-' ) . '01';
			$sTo = date( 'Y-m-' ) . date( 'd', mktime( 0, 0, 0, date( 'n' ) + 1, 0, date( 'Y' ) ) );
		}
		
		$aData[ 'aForm' ] = array(
			'sPeriod' => $this->getLang( 'summary_period' ),
			'sFrom' => $this->getLang( 'summary_from' ),
			'sTo' => $this->getLang( 'summary_to' ),
			'sDateFrom' => $sFrom,
			'sDateTo' => $sTo,
			'sSubmit' => $this->getLang( 'summary_generate' ),
			'sSubmitExcel' => $this->getLang( 'summary_generate_excel' )
		);
		
		$this->mTemplate->content = View::factory( 'account/summary_users', $aData )->render();
		
	}
	
	protected function generateSummaryDataForUsers( & $aData, $iEmployeePercent ) {
		
		$aResult = array();
		$aSumX = array( $this->getLang( 'summary_sum' ) );
		$aSumZwrot = array( $this->getLang( 'summary_company_expense' ) );
		$aSumY = array( '' );
		$iSumaSumX = 0;
		$iSumZwrot = 0;
		
		// rog tabeli pusty
		$aResult[ 0 ][ 0 ] = '';
		
//		echo '[<pre>' . print_r( $aData, 1 ) . '</pre>]';
		
		foreach ( $aData as $aRow ) {
			
			// w pierwszym wierszu umieszczamy daty
			if ( ! in_array( $aRow[ 'date' ], $aResult[ 0 ] ) ) {
				$aResult[ 0 ][] = $aRow[ 'date' ];
			}
			
			// na poczatku kazdego wiersza dodajemy nazwe usera
			if ( ! isset( $aResult[ $aRow[ 'user_id' ] ] ) ) {
				$aResult[ $aRow[ 'user_id' ] ][ 0 ] = $aRow[ 'name' ] . ' ' . $aRow[ 'fname' ];
			}
			
			// dopelniamy tablice
			if ( count( $aResult[ $aRow[ 'user_id' ] ] ) != ( count( $aResult[ 0 ] ) -1 ) ) {
				$aResult[ $aRow[ 'user_id' ] ] = array_pad( $aResult[ $aRow[ 'user_id' ] ], count( $aResult[ 0 ] ) - 1, '' );
			}
			
			$aResult[ $aRow[ 'user_id' ] ][ $aRow[ 'date' ] ] = $aRow[ 'price' ];
			
			//sumujemy po wierszach
			if ( isset( $aSumX[ $aRow[ 'user_id' ] ] ) ) {
				
				$aSumX[ $aRow[ 'user_id' ] ] += $aRow[ 'price' ];
				
				// obliczame kolumne kosztow do zwrotu
				if ( $aRow[ 'role_name' ] == 'guest' ) {
					$iPrice = $aRow[ 'price' ];
				} else {
					$iPrice = ( $aRow[ 'price' ] - ( ( $aRow[ 'price' ] * $iEmployeePercent ) / 100 ) );
				}
				$aSumZwrot[ $aRow[ 'user_id' ] ] += $iPrice;
				$iSumZwrot += $iPrice;
				
			} else {
				
				$aSumX[ $aRow[ 'user_id' ] ] = $aRow[ 'price' ];
				
				// obliczame kolumne kosztow do zwrotu
				if ( $aRow[ 'role_name' ] == 'guest' ) {
					$iPrice = $aRow[ 'price' ];
				} else {
					$iPrice = ( $aRow[ 'price' ] - ( ( $aRow[ 'price' ] * $iEmployeePercent ) / 100 ) );
				}
				$aSumZwrot[ $aRow[ 'user_id' ] ] = $iPrice;
				$iSumZwrot += $iPrice;
				
			}
			
			$iSumaSumX += $aRow[ 'price' ];
			
			// sumujemy po kolumnach
			if ( isset( $aSumY[ $aRow[ 'date' ] ] ) ) {
				$aSumY[ $aRow[ 'date' ] ] += $aRow[ 'price' ];
			} else {
				$aSumY[ $aRow[ 'date' ] ] = $aRow[ 'price' ];
			}
			
		}
		
		$aResult[] = $aSumY;
		
		$aSumX = array_values( $aSumX );
		$aSumX[] = $iSumaSumX;
		
		$aSumZwrot = array_values( $aSumZwrot );
		$aSumZwrot[] = $iSumZwrot;
		
		
//		echo '[<pre>' . print_r( $aSumZwrot, 1 ) . '</pre>]';
		
		// dodajemy sumy wierszy do tabeli
		$iIndex = 0;
		foreach ( $aResult as & $aRow ) {
			
			// dopelniamy tablice
			if ( count( $aRow ) != ( count( $aResult[ 0 ] ) - 2 ) ) {
				$aRow = array_pad( $aRow, count( $aResult[ 0 ] ) - 2, '' );
			}
			
			$aRow[] = $aSumX[ $iIndex ];
			$aRow[] = $aSumZwrot[ $iIndex++ ];
			
		}
		
//		echo '[<pre>' . print_r( $aResult, 1 ) . '</pre>]';
		
		
		return $aResult;
		
	}
	
	public function mailerAction() {
		
		if ( $this->mailEnrolReminder() )
			$aData[ 'info' ] = 'Mail wyslany';
		else 
			$aData[ 'info' ] = 'Problem z wyslaniem maila';
		
		$this->mTemplate->content = View::factory( 'account/item_edit', $aData )->render();
		
	}
	
	protected function mailEnrolReminder() {
		
		$oUser = new Model_User();
		
		$aUsers = $oUser->where( 'account_id', (int) $this->oCurrentUser->account_id )->getAll();
		
		$sSubject = '[Catering] Lista obiadowa na kolejny tydzien dodana';
		$sMessage = 'Nie zapomnij zapisac sie na obiadki :)' . "\nhttp://ciao.focus.idl.pl/";
		
		$sEmail = 'noreply@ciao.focus.idl.pl';
		
		$sHeaders = 'From: ' . $sEmail . "\r\n" .
		    'Reply-To: ' . $sEmail . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();
		
		
		return mail( 'ciaopl@ciao-group.com', $sSubject, $sMessage, $sHeaders );
//		return mail( 'maksymilian.chodorowski@ciao-group.com', $sSubject, $sMessage, $sHeaders );
		
	}
	
}
?>
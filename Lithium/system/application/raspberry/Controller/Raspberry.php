<?php

class Controller_Raspberry extends Abstract_BaseController {

	public function indexAction() {
	
		$aData['sMsg'] = 'Raspberry';
		
		$this->mTemplate->content = View::factory( 'raspberry/main', $aData );
	}
	
	public function serviceAction() {
		
		$this->mTemplate->content = View::factory( 'raspberry/service', [ 'oServerManager' => $this->getServerManager() ] );
	}
	
	public function serviceChangeAjax( $sService, $sAction ) {
		$oResp = new xajaxResponse();
		
		$oService = $this->getServerManager()->getService( $sService );
		
		if ( $oService == null ) {
			$oResp->script( 'alert("Service not found: ' . $sService . '");' );
			return $oResp;
		}
		
		$aActionResult = $oService->ExecuteAction( $sAction );
		
		if( $aActionResult === false ) {
			$oResp->script( 'alert("Problems with executing action: ' . $sAction . ' [' . $sService . ']");' );
			return $oResp;
		}
		
		sleep(2);
		
		$oResp = $this->updateStatusAjax( $sService );
		
		return $oResp;
	}
	
	public function updateStatusAjax( $sService ) {
		$oResp = new xajaxResponse();
    
    $oService = $this->getServerManager()->getService( $sService );
    
    if( $oService != null) {
      // make sure that we will get correct actions (not cached)
      $oService->ResetStatus();
    
      $oResp->script( sprintf( 'ServiceManager.update("%s", %s)', $sService, $oService->IsActiveForceCheck() ? 'true' : 'false' ) );
      
      $aData[ 'oService' ] = $oService;
      $oResp->assign( $oService->getSystemServiceName() . 'Actions', "innerHTML", (string)View::factory( 'raspberry/services/actions', $aData ) );
    }
		
		return $oResp;
	}
	
	public function performanceAction() {
		
		$this->mTemplate->content = View::factory( 'raspberry/performance', $aData );
		
		$aDiskUsage = $this->getServerManager()->GetDiskUsage();
		
		$sDesc = sprintf( 'Free: %s (%s)', $this->GetHumanReadableSizeVal( $aDiskUsage[ 'free' ] ), $this->GetHumanReadableSizeVal( $aDiskUsage[ 'all' ] ) );
		
		$this->mTemplate->content->sUptime = $this->getServerManager()->GetUpTime();
		
		preg_match( '/([0-9:]+).up ([a-z0-9\s,:]+),\s+([0-9]+).+rage: ([0-9\.,\s]+)/',
			$this->getServerManager()->GetUpTime(),
			$aMatches);
		
		$this->mTemplate->content->aStatus = array(
			'time' => $aMatches[1],
			'up' => $aMatches[2],
			'users' => $aMatches[3],
			'load' => $aMatches[4],
			'proc_temp' => $this->getServerManager()->GetProcTemp()
		);
		
		$this->mTemplate->aResources = array(
			'raphael/popup.js' => 'lib',
			'raspberry/grid.js' => 'js',
      'http://www.google.com/jsapi' => 'ext',
      'raspberry/performance.js' => 'js'
		);
		
		$this->mTemplate->content->sDiskUsage = $this->GetProgressBarControl( $sDesc, $aDiskUsage[ 'all' ], $aDiskUsage[ 'all' ] - $aDiskUsage[ 'free' ] );
	}
	
	public function getUrl( $sPath = '' ) {
		return parent::getPageUrl( $sPath );
	}
	
	private function GetProgressBarControl( $sDesc, $iMax, $iCurrent ) {
	
		$iBarSize = 200;
	
		$fRatio = $iCurrent / $iMax;
		$iPercentRatio = round( $fRatio * 100 );
		
		$aViewData = [];
		$aViewData[ 'sDesc' ] = $sDesc;
		
		$aViewData[ 'iStatus' ] = round( $iBarSize * $fRatio );
		$aViewData[ 'sStatusLabel' ] = $iPercentRatio . '%';
		
		$aViewData[ 'iStatusRest' ] = $iBarSize - $aViewData[ 'iStatus' ];;
		$aViewData[ 'sStatusRestLabel' ] = ( 100 - $iPercentRatio ) . '%';
		
		if ( $iPercentRatio > 20 ) {
			$aViewData[ 'sStatusRestLabel' ] = '';
		}
		else {
			$aViewData[ 'sStatusRest' ] = '';
		} 
	
		return View::factory( 'control/progress_bar', $aViewData );
	}
	
	private function GetHumanReadableSizeVal( $iKiloBytes, $iPrecission = 2 ) {
		$base = log( $iKiloBytes ) / log( 1024 );
		$suffixes = array( 'KB', 'MB', 'GB', 'TB' );   

		return round( pow( 1024, $base - floor( $base ) ), $iPrecission ) . $suffixes[ floor( $base ) ];
	}
	
	private function getServerManager() {
		
		static $oServerManager;
		
		if ( $oServerManager != null) {
			return $oServerManager;
		}
		
		$oServerManager = new Module_ServerManager($this);
		$oServerManager->AddService('Deluge', '/usr/bin/python', 'deluge-daemon', array(
			'log' => '/var/log/deluge/daemon/warning.log'
		));
		$oServerManager->AddService('MiniDLNA', '/usr/bin/minidlna', 'minidlna');
		$oServerManager->AddService('DDClient', 'ddclient', 'ddclient');
		$oServerManager->AddService('Samba', '/usr/sbin/smbd', 'samba');
		$oServerManager->AddService('Squid', '/usr/sbin/squid', 'squid');
		$oServerManager->AddService('BitTorrent Sync', '/usr/lib/btsync/btsync-daemon', 'btsync');
		
		return $oServerManager;
	}
}
<?php

class Module_ServerManager {
	
	private $aServiceList = [];
	private $oController;

	public function __construct($oController) {
		$this->oController = $oController;
	}
	
	public function GetUpTime() {
		return exec( 'uptime' );
	}
	
	public function AddService($sName, $sProcessName, $sSystemServiceName, $aData) {
		$this->aServiceList[$sSystemServiceName] = new Service($sName, $sProcessName, $sSystemServiceName, $this->oController, $aData);
	}
	
	public function GetServices() {
		return $this->aServiceList;
	}
	
	public function GetService( $sName ) {
		return $this->aServiceList[ $sName ];
	}
	
	public function GetDiskUsage() {
		$sUsage = exec( 'df -k | grep rootfs | awk \'{ print $2, $4; }\'' ); 
		return $this->getUsage( $sUsage );
	}
  
  public function GetMemoryUsage() {
    $sUsage = exec( 'free -m | grep Mem | awk \'{print $2,$4}\'' );
    return $this->getUsage( $sUsage );
  }
	
	public function GetProcTemp() {
		$sOutput = exec( 'cat /sys/bus/w1/devices/28-000005962828/w1_slave' );
		
		if(preg_match('/t=([0-9]+)$/', $sOutput, $aMatches)) {
			$sOutput = $aMatches[1] / 1000;
		}
		
		return $sOutput;
	}
  
  private function getUsage( $sTextResult ) {
    $aValues = explode( ' ', $sTextResult );
    return array(
      'all' => $aValues[0],
      'free' => $aValues[1]
    );
  }
}

class Service {
	
	private $sName;
	private $sProcessName;
	private $bStatus = null;
	private $oController;
	private $sSystemServiceName;
	private $aData;
	private $sActions = [];

	public function __construct($sName, $sProcessName, $sSystemServiceName, $oController, $aData) {
		$this->sName = $sName;
		$this->sProcessName = $sProcessName;
		$this->oController = $oController;
		$this->sSystemServiceName = $sSystemServiceName;
		$this->aData = $aData;
	}
	
	public function GetName() {
		return $this->sName;
	}
	
  public function GetSystemServiceName() {
		return $this->sSystemServiceName;
	}
  
	public function IsActive() {
	
		if($this->bStatus !== null) {
			return $this->bStatus;
		}
		
		$this->bStatus = false;
	
		$aProcesses = [];
		exec(sprintf("ps aux | grep %s | awk '{ print $11; }'", $this->sProcessName), $aProcesses);
		
		foreach($aProcesses as $sProcess) {
			if(strpos($sProcess, $this->sProcessName) === 0) {
				$this->bStatus = true;
				break;
			}
		}
		
		return $this->bStatus;
	}
	
	public function IsActiveForceCheck() {
		$this->bStatus = null;
		return $this->IsActive();
	}
  
  public function ResetStatus() {
    $this->bStatus = null;
    $this->aActions = null;
  }
	
	public function GetActions() {
		
		if(!empty($this->aActions)) {
			return $this->aActions;
		}
		
		if($this->IsActive()) {
			$this->CreateNewAction("Restart");
			$this->CreateNewAction("Stop");
		}
		else {
			$this->CreateNewAction("Start");
		}
		
		return $this->aActions;
	}
	
	public function ExecuteAction( $sName ) {
		
		$aActions = $this->GetActions();
		
		$oAction = $aActions[ strtolower( $sName ) ];
		if ( $oAction == null ) {
			return false;
		}
		
		return $oAction->Execute();
	}
	
	public function GetLogs() {
		return 'Log file not defined for service: ' . $this->GetName();
	}
	
	private function CreateNewAction($sActionName, $sActionValue = null) {
		$oAction = new ServiceAction($this->oController, $this->sSystemServiceName, $sActionName, $sActionValue);
		$this->aActions[$oAction->GetActionName()] = $oAction;
	}
}

class ServiceAction {

	private $oController;
	private $sServiceName;
	private $sText;
	
	public function __construct($oController, $sServiceName, $sText, $sAction = null) {
		$this->oController = $oController;
		$this->sServiceName = $sServiceName;
		$this->sText = $sText;
		$this->sAction = $sAction == null ? strtolower($sText) : $sAction;
	}
	
	public function GetText() {
		return $this->sText;
	}
	
	public function GetUrl() {
		return $this->oController->GetUrl( sprintf( '/service/%s/%s', $this->sServiceName, $this->sAction ) );
	}
	
	public function GetServiceName() {
		return $this->sServiceName;
	}
	
	public function GetActionName() {
		return $this->sAction;
	}
	
	public function Execute() {
		$aResult = [];
		$sCommand = sprintf("/home/www/system/application/raspberry/Module/shell/service_controller %s %s", $this->sServiceName, $this->sAction);
		error_log($sCommand);
		exec($sCommand, $aResult);
		return $aResult;
	}
}
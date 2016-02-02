<?php

foreach($oService->GetActions() as $oAction) { 
  printf( '<a href="javascript:void(0);" metadata="{\'s\':\'%s\',\'a\':\'%s\',\'i\':\'%s\'}" onclick="ServiceManager.click(this)">%s</a>', 
      $oAction->GetServiceName(), 
      $oAction->GetActionName(), 
      's' . $count,
      $oAction->GetText() );
}
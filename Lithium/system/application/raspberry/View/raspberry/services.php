<div>    
	Uptime: <?=$oServerManager->GetUpTime() ?>
</div>
<table id="serviceList">
	<tr>
		<th>Service</th>
		<th>Status</th>
		<th>Action</th>
	</tr>
<? foreach($oServerManager->GetServices() as $oService) { ?>
<? $count = 0; ?>
	<tr>
		<td><?=$oService->GetName() ?></td>
		<td><div class="status <?=$oService->IsActive() ? '' : 'in' ?>active" id="s<?=$count?>"> </div></td>
		<td id="s<?=$oService->GetSystemServiceName() ?>Actions">
			<?php include('services/actions.php'); ?>
		</td>
	</tr>
<? } ?>
<table id="serviceList" class="list">
	<tr>
		<th>Service</th>
		<th>Status</th>
		<th>Action</th>
	</tr>
<? $count = 0; ?>
<? foreach($oServerManager->GetServices() as $oService) { ?>
	<tr>
		<td><?=$oService->GetName() ?></td>
		<td><div class="status <?=$oService->IsActive() ? '' : 'in' ?>active" id="s<?=++$count?>"> </div></td>
		<td id="<?=$oService->GetSystemServiceName() ?>Actions">
			<?php include('services/actions.php'); ?>
		</td>
	</tr>
<? } ?>
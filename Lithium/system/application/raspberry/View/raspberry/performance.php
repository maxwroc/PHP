<div class="rasp_status">    
	<div><span class="lbl">Current time: </span><span><?=$aStatus[ 'time' ] ?></span></div>
	<div><span class="lbl">Up for: </span> <span><?=$aStatus[ 'up' ] ?></span></div>
	<div><span class="lbl">User(s): </span> <span><?=$aStatus[ 'users' ] ?></span></div>
	<div><span class="lbl">Load: </span> <span><?=$aStatus[ 'load' ] ?></span></div>
	<div><span class="lbl">Proc. temp.: </span> <span><?=$aStatus[ 'proc_temp' ] ?></span></div>
</div>
<div class="gauge_list cf">
  <div id="perf_proc"></div>
  <div id="perf_mem"></div>
  <div id="perf_drive"></div>
</div>
<?=$sDiskUsage; ?>
<div id="holder"></div>
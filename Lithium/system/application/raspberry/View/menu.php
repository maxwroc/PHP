<div id="menu">
	Raspberry
	<span class="links">
	<?php foreach ($aMenu as $aLink) { ?>
	<a href="<?=$aLink['link'] ?>" class="<?=$aLink['active'] ? 'active' : '' ?>"><?=$aLink['text'] ?></a>
	<?php } ?>
	</span>
</div>
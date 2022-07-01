<li class="{if $module=='logging'}active{/if} has-submenu">
	<a href="javascript:void(0);"><i class="ion-compose"></i> <span class="nav-label">Логирование</span> <span class="caret pull-right m-t-10"></span></a>
	<ul class="list-unstyled">
		<li class="{if $currentMenu=='log_index'}active{/if}">
			<a href="/{ADMIN}/logging">Посещения</a>
		</li>
		<li class="{if $currentMenu=='log_settings'}active{/if}">
			<a href="/{ADMIN}/logging/settings">Настройки</a>
		</li>
	</ul>
</li>
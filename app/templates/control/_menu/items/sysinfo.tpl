<li class="{if $module=='sysinfo'}active{/if} has-submenu">
	<a href="javascript:void(0);"><i class="ion-settings" style="color: red;"></i> <span class="nav-label">Система</span> <span class="caret pull-right m-t-10"></span></a>
	<ul class="list-unstyled">
		<li class="{if $currentMenu=='phpinfo'}active{/if}">
			<a href="/{ADMIN}/sysinfo/phpinfo">PHP Info</a>
		</li>
		<li class="{if $currentMenu=='constants'}active{/if}">
			<a href="/{ADMIN}/sysinfo/constants">Константы</a>
		</li>
		<li class="{if $currentMenu=='cacheinfo'}active{/if}">
			<a href="/{ADMIN}/sysinfo/cacheinfo">КЭШ</a>
		</li>
	</ul>
</li>
<li class="{if 'statistics'==$module}active{/if} has-submenu">
	<a href="javascript:void(0);"><i class="ion-stats-bars"></i> <span class="nav-label">Статистика</span> <span class="caret pull-right m-t-10"></span></a>
	<ul class="list-unstyled">
		<li class="{if 'index'==$currentMenu}active{/if}">
			<a href="/{ADMIN}/statistics">Общая</a>
		</li>
		<li class="{if 'managers'==$currentMenu}active{/if}">
			<a href="/{ADMIN}/statistics/managers">Отдел продаж</a>
		</li>
		<li class="{if 'operations'==$currentMenu}active{/if}">
			<a href="/{ADMIN}/statistics/operations">Операции</a>
		</li>
	</ul>
</li>
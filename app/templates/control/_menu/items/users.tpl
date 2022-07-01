<li class="{if 'users'==$module}active{/if} has-submenu">
	<a href="javascript:void(0);"><i class="ion-person-stalker"></i> <span class="nav-label">Пользователи</span> <span class="caret pull-right m-t-10"></span></a>
	<ul class="list-unstyled">
		<li class="{if 'index'==$currentMenu}active{/if}">
			<a href="/{ADMIN}/users">Список</a>
		</li>
		<li class="{if 'summary'==$currentMenu}active{/if}">
			<a href="/{ADMIN}/users/summary">Сводка</a>
		</li>
	</ul>
</li>
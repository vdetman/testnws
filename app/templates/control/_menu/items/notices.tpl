<li class="{if 'notices'==$module}active{/if} has-submenu">
	<a href="javascript:void(0);"><i class="ion-paper-airplane"></i> <span class="nav-label">Уведомления</span> <span class="caret pull-right m-t-10"></span></a>
	<ul class="list-unstyled">
		<li>
			<a class="{if 'index'==$currentMenu}active{/if}" href="/{ADMIN}/notices">Логи</a>
		</li>
		<li>
			<a class="{if 'events'==$currentMenu}active{/if}" href="/{ADMIN}/notices/events">События</a>
		</li>
		<li>
			<a class="{if 'emails'==$currentMenu}active{/if}" href="/{ADMIN}/notices/emails">E-mail аккаунты</a>
		</li>
		<li>
			<a class="{if 'letters'==$currentMenu}active{/if}" href="/{ADMIN}/notices/letters">Шаблоны писем</a>
		</li>
		<li>
			<a class="{if 'telegram'==$currentMenu}active{/if}" href="/{ADMIN}/notices/telegram">Telegram</a>
		</li>
	</ul>
</li>
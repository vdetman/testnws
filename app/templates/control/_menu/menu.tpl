<ul class="list-unstyled">
	<li class="{if 'index' == $module}active{/if}">
		<a href="/{ADMIN}"><i class="ion-home"></i> <span class="nav-label">Dashboard</span></a>
	</li>

	{assign var='__menuDir' value=VF_TPLS_DIR|cat:'/'|cat:ADMIN|cat:'/_menu/items/'}
	
	{* Собираем меню из файлов _template/nav_items/{$section}.php, проверяя права доступа в текущий раздел *}
	{if isset($_menu) && is_array($_menu) && $_menu}
		{foreach from=$_menu key=$section item=$access}
			{assign var='__menuFile' value=$__menuDir|cat:$section|cat:'.tpl'}
			{if in_array($currentUser->getRole(), $access) && file_exists($__menuFile)}
				{include file="./items/$section.tpl"}
			{/if}
		{/foreach}
	{/if}
</ul>

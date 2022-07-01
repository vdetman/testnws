{include file="../../_units/header.tpl"}

	<div class="row"> 
		<div class="col-lg-12"> 
			<ul class="nav nav-tabs"> 
				<li class="{if 'templates'==$currentTab}active{/if}">
					<a href="/{ADMIN}/{$module}/telegram/templates">
						<i class="fa fa-file-code-o"></i>
						<span class="hidden-xs">Шаблоны сообщений</span>
					</a>
				</li>
				<li class="{if 'accounts'==$currentTab}active{/if}">
					<a href="/{ADMIN}/{$module}/telegram/accounts">
						<i class="fa fa-users"></i>
						<span class="hidden-xs">Аккаунты</span>
					</a>
				</li>
				<li class="{if 'preparing'==$currentTab}active{/if}">
					<a href="/{ADMIN}/{$module}/telegram/preparing">
						<i class="fa fa-exchange"></i>
						<span class="hidden-xs">Подключение</span>
					</a>
				</li>
				<li class="{if 'settings'==$currentTab}active{/if}">
					<a href="/{ADMIN}/{$module}/telegram/settings">
						<i class="fa fa-cog"></i>
						<span class="hidden-xs">Настройки</span>
					</a>
				</li>
				<li class="{if 'bot'==$currentTab}active{/if}">
					<a href="/{ADMIN}/{$module}/telegram/bot">
						<i class="fa fa-android"></i>
						<span class="hidden-xs">Работа с Ботом</span>
					</a>
				</li>
			</ul>
						
			{assign var='__File' value=VF_TPLS_DIR|cat:'/'|cat:ADMIN|cat:'/'|cat:$module|cat:'/telegram/_'|cat:$currentTab|cat:'.tpl'}
			{if file_exists($__File)}
				<div class="tab-content">
					<div class="tab-pane active">
						{include file="./_$currentTab.tpl"}
					</div> 
				</div>
			{/if}
			
		</div>
	</div>

{include file="../../_units/footer.tpl"}
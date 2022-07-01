<tr id="id_{$u->getId()}">
	<td class="user_info">
		<a href="/{ADMIN}/{$module}/edit/{$u->getId()}">#{$u->getId()} {$u->getName()}</a>
		<label class="badge bg-info">{$u->getCreated()->format('d.m.Y H:i')}</label>
		{if 3 != $u->getRoleId()}
			{if 'new' == $u->getRoleId()}
				{assign var='cls' value='bg-info'}
			{elseif 'locked' == $u->getStatus()}
				{assign var='cls' value='bg-primary'}
			{elseif 'success' == $u->getStatus()}
				{assign var='cls' value='bg-success'}
			{else}
				{assign var='cls' value='bg-danger'}
			{/if}
			<label class="badge {$cls}">{$u->getRoleName()}</label>
		{/if}
		<br />
		<i class="fa fa-envelope text-success"></i> {$u->getEmail()}<br />
		<i class="fa fa-phone text-info"></i> {$u->getPhone()}<br />
	</td>
	<td class="add_info">
		{if $u->getData()->getCompany()}
			<i class="fa fa-home text-info" title="Компания"></i> {$u->getData()->getCompany()}<br />
		{/if}
		{if $u->getData()->getAddress()}
			<i class="fa fa-envelope text-info" title="Адрес"></i> {$u->getData()->getAddress()}<br />
		{/if}
		{if $u->getData()->getInn()}
			<i class="fa fa-info text-info" title="ИНН"></i> {$u->getData()->getInn()}<br />
		{/if}
		{if $u->getData()->getKpp()}
			<i class="fa fa-info text-info" title="КПП"></i> {$u->getData()->getKpp()}
		{/if}
	</td>
	<td class="operations">
		<div onclick="switcher(this);" class="switcher {if 'active' == $u->getStatus()}true{else}false{/if}"
			data-field="Status"
			data-value="{if 'active' == $u->getStatus()}1{else}0{/if}"
			data-id="{$u->getId()}">
		</div>
		<a href="/{ADMIN}/{$module}/edit/{$u->getId()}" title="Редактировать"><i class="fa fa-edit btn-edit"></i></a>
		<i class="fa fa-money text-info pointer" title="Финансы" onclick="showUserFinance('{$u->getId()}');"></i>
		<i class="fa fa-sign-in text-success" title="Авторизоваться" onclick="loginByUser('{$u->getId()}');"></i>
	</td>
</tr>
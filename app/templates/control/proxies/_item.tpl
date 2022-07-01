<tr id="item_{$p->getId()}">
	<td class="main_info">
		<div><a href="/{ADMIN}/{$module}/edit/{$p->getId()}"><span class="id">#{$p->getId()}</span> <span class="ip">{$p->getIp()}</span>:{$p->getPort()}</a></div>
		<div>{$p->getName()}</div>
	</td>
	<td class="login">
		<i class="fa fa-user"></i> <i>{$p->getLogin()}</i><br/>
		<i class="fa fa-key"></i> <i>{$p->getPassword()}</i><br/>
	</td>
	<td class="expires">
		{if $p->getExpires()}
			<label class="badge {if $p->getExpires()->getTimestamp() > time()}bg-success{else}bg-danger{/if}">
				<i class="fa fa-calendar"></i>
				{$p->getExpires()->format('d.m.Y')}
			</label>
		{/if}
	</td>
	<td class="operations">
		<div onclick="switcher(this);" class="switcher {if 'active' == $p->getStatus()}true{else}false{/if}"
			data-field="Status"
			data-value="{if 'active' == $p->getStatus()}1{else}0{/if}"
			data-id="{$p->getId()}">
		</div>
		<i class="fa fa-times text-danger" title="Удалить" onclick="deleteProxy({$p->getId()});"></i>
		<i class="fa fa-refresh text-success" title="Проверить" onclick="checkProxy({$p->getId()});"></i>
	</td>
</tr>
<tr class="status_{$op->getStatus()}" id="item_{$op->getId()}">
	<td class="main_info">
		<div><a href="/{ADMIN}/{$module}/operation/{$op->getId()}">#{$op->getId()} {$op->getCode()} {$op->getType()->getDescription()}</a></div>
		<div>
				<i class="fa fa-calendar"></i>
				<small>{$op->getCreated()->format('d.m.Y H:i:s')}</small>
				
				{if OPERATION_TYPE_REFILL == $op->getTypeId() &&  'cashless' == $op->getMethod()}
					<button onclick="downloadDocument({$op->getId()}, 'invoice');" class="btn btn-default btn-xs btn-rounded"><i class="fa fa-download"></i> счет</button>
					<button onclick="downloadDocument({$op->getId()}, 'act');" class="btn btn-default btn-xs btn-rounded"><i class="fa fa-download"></i> акт</button>
				{/if}
				
		</div>
		{* 'new', 'locked', 'success', 'canceled', 'error' *}
		{if 'new' == $op->getStatus()}
			{assign var='cls' value='bg-info'}
		{elseif 'locked' == $op->getStatus()}
			{assign var='cls' value='bg-primary'}
		{elseif 'success' == $op->getStatus()}
			{assign var='cls' value='bg-success'}
		{else}
			{assign var='cls' value='bg-danger'}
		{/if}
		<label class="badge {$cls}">{$op->getStatusDescription()}
			{if $op->getCompleted()} {$op->getCompleted()->format('d.m.Y H:i:s')}{/if}
		</label>
		{if $op->getIsFirstRefill()}
			<label class="badge bg-dribbble">Первичная оплата</label>
		{/if}
		{if $op->getIsFirstRefillCustom()}
			<label class="badge bg-fb">Считается первичной</label>
		{/if}
		{if $op->getDescription()}
			<div><i class="fa fa-comment"></i> <small><em>{$op->getDescription()}</em></small></div>
		{/if}
	</td>
	<td class="amount">
		{$op->getAmount()}
	</td>
	<td class="user_info">
		{if $op->getUser()}
			<span class="name"><a href="/{ADMIN}/users/edit/{$op->getUser()->getId()}" target="_blank">{$op->getUser()->getName()}</a></span>
			<i class="fa fa-money text-info m-l-10 pointer" title="Финансы" onclick="showUserFinance('{$op->getUser()->getId()}');"></i>
			<i class="fa fa-sign-in text-success m-l-10 pointer" title="Авторизоваться" onclick="loginByUser('{$op->getUser()->getId()}');"></i>
			<br />
			<span class="email">{$op->getUser()->getEmail()}</span><br />
			{*<label class="badge bg-primary"><i class="fa fa-ruble"></i> {$op->getUser()->getBalance()}</label>*}
		{/if}
	</td>
	<td class="user_manager">
		{if $op->getManagerId() && isset($managers[$op->getManagerId()])}
			{assign var='man' value=$managers[$op->getManagerId()]}
			<span class="name">{$man->getName()}<i class="fa fa-refresh m-l-10 pointer" title="Изменить" onclick="changeManagerModal({$op->getId()});"></i></span><br />
			<span class="email">({$man->getEmail()})</span>
		{/if}
	</td>
	<td class="operations">
		{if OPERATION_TYPE_WITHDRAWAL == $op->getTypeId() && in_array($op->getStatus(), array('new', 'pending'))}
			<button onclick="setStatus({$op->getId()}, 'success', '{md5('success!~!'|cat:$op->getId())}');" class="btn btn-success btn-xs">Исполнить</button>
			<button onclick="setStatus({$op->getId()}, 'canceled', '{md5('canceled!~!'|cat:$op->getId())}', true);" class="btn btn-danger btn-xs">Отменить</button>
		{elseif OPERATION_TYPE_REFILL == $op->getTypeId()}
			{if in_array($op->getStatus(), array('new', 'canceled', 'error'))}
				<button onclick="setStatus({$op->getId()}, 'success', '{md5('success!~!'|cat:$op->getId())}');" class="btn btn-success btn-xs">Исполнить</button>
			{/if}
			{if in_array($op->getStatus(), array('new', 'success'))}
				<button onclick="setStatus({$op->getId()}, 'canceled', '{md5('canceled!~!'|cat:$op->getId())}');" class="btn btn-danger btn-xs">Отменить</button>
				{if !$op->getIsFirstRefill()}
					{if !$op->getIsFirstRefillCustom()}
					<button onclick="setIsFirstRefillCustom({$op->getId()}, '1', '{md5('1!~!'|cat:$op->getId())}');" class="btn btn-info btn-xs">Считать как первичную</button>
						{else}
						<button onclick="setIsFirstRefillCustom({$op->getId()}, '0', '{md5('0!~!'|cat:$op->getId())}');" class="btn btn-warning btn-xs">Не считать как первичную</button>
						{/if}
				{/if}
			{/if}
		{/if}
		<div>
			<br/>
			<i class="fa fa-times text-danger pull-right" style="cursor: pointer;" title="Удалить" onclick="deleteOperation({$op->getId()}, '{md5('delete!~!'|cat:$op->getId())}');"></i>
		</div>
	</td>
</tr>
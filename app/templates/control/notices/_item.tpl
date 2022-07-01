{if 'process'==$n->getStatus()}
	{assign var='class' value='info'}
	{assign var='status' value='В процессе'}
{elseif 'success'==$n->getStatus()}
	{assign var='class' value='success'}
	{assign var='status' value='Отправлено'}
{elseif 'error'==$n->getStatus()}
	{assign var='class' value='danger'}
	{assign var='status' value='Ошибка'}
{else}
	{assign var='class' value='default'}
	{assign var='status' value='В ожидании'}
{/if}

{if 'email'==$n->getType()}
	{assign var='icon' value='fa-envelope-o'}
{elseif 'telegram'==$n->getType()}
	{assign var='icon' value='fa-paper-plane-o'}
{elseif 'sms'==$n->getType()}
	{assign var='icon' value='fa-phone'}
{/if}
<tr id="item_{$n->getId()}" class="{$class}">
	<td class="type {$n->getType()}" title="{$n->getType()|capitalize}"></td>
	<td class="main_data">
		<span class="id">#{$n->getId()}</span>
		<span class="created">{$n->getCreated()->format('d #Hmg Y \в H:i')}</span>
		<span class="recipient"><i class="fa {$icon}"></i> {$n->getRecipient()}</span>
		{if 'error'==$n->getStatus()}
			<button class="btn btn-xs btn-info" onclick="editNotice({$n->getId()});"><i class="fa fa-edit"></i> Изменить</button>
		{/if}
	</td>
	<td class="message">
		{if 'email'==$n->getType()}
			<b>Тема</b>: {$n->getSubject()}<br/>
			<button class="btn btn-xs btn-info" onclick="showNoticeDetail({$n->getId()}, 'Message');"><i class="fa fa-eye"></i> Полный текст</button>
		{elseif 'telegram'==$n->getType()}
			{$n->getMessage()}
		{elseif 'sms'==$n->getType()}
			{$n->getMessage()}
		{/if}
	</td>
	<td class="result">
	  <span class="label label-{$class}">
			{$status}
			{if $n->getAttempts()} ({$n->getAttempts()}){/if}
		</span>
		{if $n->getError()}
			<button class="btn btn-xs btn-danger btn-rounded" title="Детали ошибки" onclick="showNoticeDetail({$n->getId()}, 'Error');"><i class="fa fa-question"></i></button>
			<button class="btn btn-xs btn-info btn-rounded" title="Сбросить ошибку" onclick="resetNoticeError({$n->getId()});"><i class="fa fa-refresh"></i></button>
		{/if}
		{if 'success'!=$n->getStatus()}
			<button class="btn btn-xs btn-success btn-rounded" title="Исполнить" onclick="executeNotice({$n->getId()});"><i class="fa fa-paper-plane-o"></i></button>
		{/if}
		{if $n->getExecuted()}
			<span class="executed">{$n->getExecuted()->format('d #Hmg Y \в H:i')}</span>
		{/if}
	</td>
</tr>
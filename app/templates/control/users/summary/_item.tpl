<tr id="item_{$s->getUserId()}" class="{if $s->getStatus()}{$s->getStatus()->getStyle()}{/if}">
	<td class="user_info">
		<a href="/{ADMIN}/{$module}/edit/{$s->getUserId()}">#{$s->getUserId()} {$s->getUser()->getFirstName()} {$s->getUser()->getLastName()}</a>
		<br />
		<label class="badge bg-info" title="Дата регистрации">{$s->getUser()->getCreated()}</label>
		<i class="fa fa-sign-in text-success" title="Авторизоваться" onclick="loginByUser('{$s->getUserId()}');"></i>
		<br />
		<i class="fa fa-envelope text-success"></i> {$s->getUser()->getEmail()}<br />
		<i class="fa fa-phone text-info"></i> {$s->getUser()->getPhone()}<br />
		<hr>
		<div class="status">
			{if $s->getStatus()}
				<label class="badge bg-{$s->getStatus()->getStyle()}">{$s->getStatus()->getName()}</label>
			{/if}
		</div>
	</td>
	<td class="finance">
		<div><i class="fa fa-bank text-primary" title="Баланс"></i> {$s->getBalance()|default:'0'} руб</div>
		<div>
			<i class="fa fa-money text-success" title="Сумма пополнений"></i> {$s->getRefills()|default:'0'} руб
			{if $s->getLastRefillAt()}
				<br />
				<i class="fa fa-calendar text-success" title="Дата последнего пополнения"></i> 
				<label class="badge bg-success" title="Дата последнего пополнения">{$s->getLastRefillAt()->format('d.m.Y')}</label>
			{/if}
		</div>
		<div><i class="fa fa-trophy text-primary" title="Сумма бонусов"></i> {$s->getBonuses()|default:'0'} руб</div>
	</td>
	<td class="costs">
		<div>
			<i class="fa fa-calculator text-info" title="Суточное списание"></i> {$s->getDailyAmount()|default:'0'} руб
			<br />
			<i class="fa fa-money text-success" title="Сумма последнего списания"></i> {$s->getDailyCosted()|default:'0'} руб
			{if $s->getLastCostedAt()}
				<br />
				<i class="fa fa-calendar text-primary" title="Дата последнего списания"></i>
				<label class="badge bg-primary" title="Дата последнего списания">{$s->getLastCostedAt()->format('d.m.Y')}</label>
			{/if}
		</div>
		<div><i class="fa fa-clock-o text-dark" title="Баланса хватит на..."></i> ~ {$s->getEnoughDays()|default:'0'} {$s->getEnoughDaysStr()}</div>
	</td>
	<td class="modules">
		{foreach from=$s->getModules() key=$mpid item=$mdls}
			<div class="mps">{$marketplaces[$mpid]}
				<ul class="list-unstyled">
				{foreach from=$mdls key=$mid item=$status}
					<li class="{if $status}text-success{else}text-danger{/if}">
						{$modules[$mid]}
					</li>
				{/foreach}
				</ul>
			</div>
		{/foreach}
	</td>
	<td class="comment">
		{if $s->getManagerId() && isset($managers[$s->getManagerId()])}
			{assign var='man' value=$managers[$s->getManagerId()]}
			<span class="manager">
				<i class="fa fa-user text-primary"></i>
				{$man->getName()} ({$man->getEmail()})
			</span>
			<hr>
		{/if}
		{if $s->getComment()}
			<i class="fa fa-comment text-info"></i> {$s->getComment()}
		{/if}
	</td>
	<td class="operations">
		<i class="fa fa-edit text-primary pointer" title="Изменить" onclick="editSummaryModal({$s->getUserId()});"></i>
		<i class="fa fa-refresh text-success pointer" title="Обновить сводку" onclick="refreshSummary({$s->getUserId()});"></i>
		<i class="fa fa-envelope text-info pointer" title="Сформировать оффер" onclick="formOffer({$s->getUserId()});"></i>
	</td>
</tr>
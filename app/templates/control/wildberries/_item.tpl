<tr id="id_{$i->getId()}">
	<td class="general">
		<a href="/{ADMIN}/{$module}/edit/{$i->getId()}" title="Редактировать">#{$i->getId()} {$i->getProject()->getName()}</a><br/>
		<b>pid:</b> <a href="/{ADMIN}/projects/?project_id={$i->getProjectId()}&tmp=no" title="Перейти к проекту">#{$i->getProjectId()}</a>
		{if 'deleted'==$i->getProject()->getStatus()}
			<label class="badge bg-danger">Удален: {$i->getProject()->getDeleted()->format('d.m.Y H:i')}</label>
		{/if}
		<br/>
		{if $i->getKeys()->getApiKey()}
			<i class="fa fa-key"></i> {$i->getKeys()->getApiKey()}
		{/if}
	</td>

	<td class="updates">
		<div>
			<span title="Заказы">
				<i class="fa fa-bookmark-o"></i>
				{if $i->getActivity()->getOrdersUpdated()}{$i->getActivity()->getOrdersUpdated()->format('d.m.Y H:i')}{else}нет данных{/if}
			</span>
			<span title="Продажи">
				<i class="fa fa-bookmark"></i>
				{if $i->getActivity()->getSalesUpdated()}{$i->getActivity()->getSalesUpdated()->format('d.m.Y H:i')}{else}нет данных{/if}
			</span>
		</div>
		<div>
			<span title="Цены">
				<i class="fa fa-ruble"></i>
				{if $i->getActivity()->getPricesUpdated()}{$i->getActivity()->getPricesUpdated()->format('d.m.Y H:i')}{else}нет данных{/if}
			</span>
			<span title="Остатки">
				<i class="fa fa-cart-plus"></i>
				{if $i->getActivity()->getStocksUpdated()}{$i->getActivity()->getStocksUpdated()->format('d.m.Y H:i')}{else}нет данных{/if}
			</span>
		</div>
		<div class="accounts_blocked">
			{if $i->getActivity()->getBlockedFor()}
				<label class="badge bg-danger" {if $i->getActivity()->getBlockedReason()}title="{$i->getActivity()->getBlockedReason()}"{/if}>
					{$i->getActivity()->getBlockedFor()}
					c {if $i->getActivity()->getBlockedSince()}{$i->getActivity()->getBlockedSince()->format('d.m.Y H:i')}{/if}
				</label>
			{/if}
		</div>
	</td>
	<td class="owner">
		<i class="fa fa-user text-info"></i> #{$i->getUser()->getId()} <a href="/{ADMIN}/users/edit/{$i->getUser()->getId()}" title="Просмотреть владельца">{$i->getUser()->getName()}</a>
		<i class="fa fa-money text-info m-l-10 pointer" title="Финансы" onclick="showUserFinance('{$i->getUser()->getId()}');"></i>
		<i class="fa fa-sign-in text-success m-l-10 pointer" title="Авторизоваться" onclick="loginByUser('{$i->getUser()->getId()}');"></i>
		<br />
		<i class="fa fa-envelope text-success"></i> {$i->getUser()->getEmail()}
	</td>
</tr>
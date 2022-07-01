<tr id="id_{$i->getId()}">
	<td class="marketplace {$i->getMarketplace()->getLabel()}" title="{$i->getMarketplace()->getName()}"></td>
	<td class="general">
		<a href="/{ADMIN}/{$module}/edit/{$i->getId()}" title="Редактировать">#{$i->getId()} {$i->getName()}</a>
		<label class="badge bg-success">{$i->getCreated()->format('d.m.Y H:i')}</label><br/>
		{if 'deleted'==$i->getStatus()}
			<label class="badge bg-danger">Удален: {$i->getDeleted()->format('d.m.Y H:i')}</label><br/>
		{/if}
		{*if 3 != $i->getRoleId()}
			<?
										if ($p->getAccountId()) {
											switch ($p->getMarketplace()) {
												case MARKETPLACE_LABEL_WILDBERRIES:?>
												<a class="btn btn-xs btn-info" href="/<?=ADMIN?>/a_wildberries/view/<?=$p->getAccountId()?>" title="Перейти к аккаунту">
													<i class="fa fa-external-link"></i> #<?=$p->getAccountId()?> <?=ucfirst(MARKETPLACE_LABEL_WILDBERRIES)?>
												</a>
												<?break;
											}
										}?>
		{/if*}
	</td>
	{*<td class="tariffs">
		<i>~ ~ ~ в разработке ~ ~ ~</i>
	</td>*}
	<td class="test_periods">
		{assign var='tps' value=$i->getTestPeriods()}

		{foreach from=$mods key=$mid item=$data}
			{if isset($tps[$mid]) && $tps[$mid]->getExpires()}
				<div>
					<span title="{$data['n']}">{$data['s']}:</span>
					<label class="badge {if $tps[$mid]->getExpires()->getTimestamp() > time()}bg-success{else}bg-danger{/if}">{$tps[$mid]->getExpires()->format('d.m.Y H:i')}</label>
				</div>
			{/if}
		{/foreach}
	</td>
	<td class="owner">
		<i class="fa fa-user text-info"></i> #{$i->getUser()->getId()} <a href="/{ADMIN}/users/edit/{$i->getUser()->getId()}" title="Просмотреть владельца">{$i->getUser()->getName()}</a>
		<i class="fa fa-money text-info m-l-10 pointer" title="Финансы" onclick="showUserFinance('{$i->getUser()->getId()}');"></i>
		<i class="fa fa-sign-in text-success m-l-10 pointer" title="Авторизоваться" onclick="loginByUser('{$i->getUser()->getId()}');"></i>
		<br />
		<i class="fa fa-envelope text-success"></i> {$i->getUser()->getEmail()}
	</td>
	<td class="operations">
		<div onclick="switcher(this);" class="switcher {if 'active' == $i->getStatus()}true{else}false{/if}"
			data-field="status"
			data-value="{if 'active' == $i->getStatus()}1{else}0{/if}"
			data-id="{$i->getId()}">
		</div>
	</td>
</tr>
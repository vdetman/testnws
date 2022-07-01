{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="clearfix"></div>
					<a href="/{ADMIN}/{$module}/createOperation" class="btn btn-success btn-xs pull-right"><i class="fa fa-plus"></i> Создать бонусную операцию</a>
					{include file="./filter_operations.tpl" filter=$filter}
					<div class="clearfix"></div>
				</div>
				<div class="panel-body h-scroll">
				{if $operations}
					{$pagination}
					<table class="table table-bordered table-hover" id="operations_listing">
						<thead>
							<tr>
								<th colspan="2">Информация</th>
								<th>
									Сумма
									{if $totalAmount}
										<div id="totalAmount">{$totalAmount}</div>
									{/if}
								</th>
								<th>Пользователь</th>
								<th>Менеджер</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$operations item=$op}
							<tr class="status_{$op->getStatus()}" id="item_{$op->getId()}">
								<td class="type type_{$op->getTypeId()}" title="{$op->getType()->getDescription()}"></td>
								<td class="main_info">
									<div>
										#{$op->getId()} 
										{$op->getType()->getDescription()}
										<label class="badge bg-blue">{$op->getCreated()->format('d.m.Y H:i:s')}</label>
										{if 'success' == $op->getStatus() && $op->getCompleted()}
											<label class="badge bg-success">Исполнена {$op->getCompleted()->format('d.m.Y H:i:s')}</label>
										{/if}
									</div>
									{if OPERATION_TYPE_REFILL == $op->getTypeId() &&  'cashless' == $op->getMethod()}
										<label class="badge bg-dark-green">безнал</label>
									{/if}
									{if $op->getIsFirstRefill()}
										<label class="badge bg-dribbble">Первичная оплата</label>
									{/if}
									{if $op->getIsFirstRefillCustom()}
										<label class="badge bg-fb">Считается первичной</label>
									{/if}
								</td>
								<td class="amount">
									{$op->getAmount()}
								</td>
								<td class="user_info">
									{if $op->getUser()}
										<span class="name"><a href="/{ADMIN}/users/edit/{$op->getUser()->getId()}" target="_blank">{$op->getUser()->getName()}</a></span>
										<i class="fa fa-money text-info m-l-10 pointer" title="Финансы" onclick="showUserFinance('{$op->getUser()->getId()}');"></i>
										<br />
										<span class="email">{$op->getUser()->getEmail()}</span><br />
									{/if}
								</td>
								<td class="user_manager">
									{if $op->getManagerId() && isset($managers[$op->getManagerId()])}
										{assign var='man' value=$managers[$op->getManagerId()]}
										<span class="name">{$man->getName()}</span><br />
										<span class="email">({$man->getEmail()})</span>
									{/if}
								</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
					{$pagination}
				{else}
					<h4>Не найдено ни одного элемента</h4>
				{/if}
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}
{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="text-right">
						<a href="/{ADMIN}/{$module}/create" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Создать операцию</a>
					</div>
					<div class="clearfix"></div>
					{include file="./filter.tpl" filter=$filter}
					<div class="clearfix"></div>
				</div>
				<div class="panel-body h-scroll">
				{if $operations}
					{$pagination}
					<table class="table table-bordered table-hover" id="operations_listing">
						<thead>
							<tr>
								<th>Информация</th>
								<th>
									Сумма
									{if $totalAmount}
										<div id="totalAmount">{$totalAmount}</div>
									{/if}
								</th>
								<th>Пользователь</th>
								<th>Менеджер</th>
								<th>Действия</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$operations item=$op}
							{include file="./_item.tpl" op=$op}
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
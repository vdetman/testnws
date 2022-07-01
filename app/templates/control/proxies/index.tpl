{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					{include file="./filter.tpl" filter=$filter}
					<button onclick="exportList();" class="btn btn-default btn-xs pull-right" title="Сохранить список IP"><i class="fa fa-list"></i></button>
					<button onclick="exportProxies();" class="btn btn-info btn-xs pull-right m-r-10"><i class="fa fa-sign-out"></i> Экспорт</button>
					<a href="/{ADMIN}/{$module}/import" class="btn btn-primary btn-xs pull-right m-r-10"><i class="fa fa-sign-in"></i> Импорт</a>
					<button onclick="createProxy();" class="btn btn-success btn-xs pull-right m-r-10"><i class="fa fa-plus"></i> Добавить</button>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
				{if $proxies}
					{$pagination}
					<table class="table table-bordered table-hover" id="listing">
						<thead>
							<tr>
								<th>Информация</th>
								<th>Пользователь</th>
								<th>Истекает</th>
								<th>Действия</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$proxies item=$p}
							{include file="./_item.tpl" p=$p}
						{/foreach}
						</tbody>
					</table>
					{$pagination}
					<input type="hidden" id="query" value='{$query}' />
				{else}
					<h4>Не найдено ни одного элемента</h4>
				{/if}
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}
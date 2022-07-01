{include file="../../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="statusesModal();" class="btn btn-success btn-xs pull-right"><i class="fa fa-server"></i> Статусы</button>
					<a href="/{ADMIN}/statistics/createOperation" target="_blank" class="btn btn-info btn-xs pull-right m-r-10"><i class="fa fa-plus"></i> Начислить бонус</a>
					{include file="./filter.tpl" filter=$filter}
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
				{if $users}
					{$pagination}
					<table class="table table-bordered table-hover" id="users_summary">
						<thead>
							<tr>
								<th>Данные</th>
								<th>Финансы</th>
								<th>Списания</th>
								<th>Модули</th>
								<th>Менеджер / Комментарий</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$users item=$s}
								{include file="./_item.tpl" s=$s}
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

{include file="../../_units/footer.tpl"}
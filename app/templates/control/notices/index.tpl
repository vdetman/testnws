{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					{include file="./filter.tpl" filter=$filter}
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
				{if $notices}
					{$pagination}
					<table class="table table-bordered table-hover" id="logs_listing">
						<thead>
							<tr>
								<th colspan="2">Информация</th>
								<th>Сообщение</th>
								<th>Статус</th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$notices item=$n}
								{include file="./_item.tpl" n=$n}
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
	<div id="modalContainer" class="modal fade" aria-hidden="true" style="display: none;"></div>
	
{include file="../_units/footer.tpl"}
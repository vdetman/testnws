{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					{include file="./filter.tpl" filter=$filter}
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
				{if $list}
					{$pagination}
					<table class="table table-bordered table-hover" id="listing">
						<thead>
							<tr>
								<th>Данные</th>
								<th>Активность</th>
								<th>Владелец</th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$list item=$i}
								{include file="./_item.tpl" i=$i}
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
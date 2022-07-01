{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					{include file="./filter.tpl" filter=$filter}
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
				{if $users}
					{$pagination}
					<table class="table table-bordered table-hover" id="users_listing">
						<thead>
							<tr>
								<th>Данные</th>
								<th>Компания</th>
								<th>Действия</th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$users item=$u}
								{include file="./_item.tpl" u=$u}
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
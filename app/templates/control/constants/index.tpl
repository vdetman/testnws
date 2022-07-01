{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="addNew();" class="btn btn-info btn-xs pull-right"><i class="fa fa-plus"></i> Добавить</button>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					{if $constants}
					<div class="table-responsive">
						<table class="table table-bordered table-hover" id="listing">
							<thead>
								<tr>
									<th colspan="2">Описание</th>
									<th>Значение</th>
								</tr>
							</thead>
							<tbody>
							{foreach from=$constants item=$c}
								<tr id="item_{$c->getId()}">
									<td class="sort_handle"><i class="fa fa-navicon"></i></td>
									<td class="main_data">
										<span class="name"><a href="/{ADMIN}/{$module}/edit/{$c->getId()}" title="Редактировать">{$c->getName()}</a></span>
										{if $c->getType()}
											<span class="type">({$c->getType()})</span>
										{/if}
										<span class="description">{$c->getDescription()}</span>
									</td>
									<td class="value">
										{$c->getValue()}
									</td>
								</tr>
							{/foreach}
							</tbody>
						</table>
					</div>
					{else}
						<h4>Не найдено ни одного элемента</h4>
					{/if}
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}
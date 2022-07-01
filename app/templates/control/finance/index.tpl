{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="addNew();" class="btn btn-info btn-xs pull-right"><i class="fa fa-plus"></i> Добавить</button>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					{if $settings}
					<div class="table-responsive">
						<table class="table table-bordered table-hover" id="listing">
							<thead>
								<tr>
									<th>Описание</th>
									<th>Значение</th>
								</tr>
							</thead>
							<tbody>
							{foreach from=$settings item=$c}
								<tr id="item_{$c->getId()}">
									<td class="main_data">
										<div class="name"><a href="/{ADMIN}/finance/editSetting/{$c->getId()}" title="Редактировать">#{$c->getId()} {$c->getLabel()}</a></div>
										<div class="description">{$c->getDescription()}</div>
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

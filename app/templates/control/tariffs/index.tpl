{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="text-right">
						<button onclick="createItem();" class="btn btn-success btn-xs pull-right"><i class="fa fa-plus"></i> Добавить</button>
						{foreach from=$marketplaces item=$m}
							<a href="/{ADMIN}/{$module}?marketplace={$m->getLabel()}&tmp=no" class="btn {if $m->getLabel()==$marketplace->getLabel()}btn-success{else}btn-default{/if} btn-xs pull-left m-r-10">{$m->getName()}</a>
						{/foreach}
						<div class="clearfix"></div>
						<input type="hidden" id="marketplace_id" value="{$marketplace->getId()}" />
					</div>
				</div>
				<div class="panel-body h-scroll">
					<table class="table table-striped table-bordered table-hover" id="listing">
						<thead>
							<tr>
								<th>Тип</th>
								<th>Тип цены</th>
								<th>Элементы</th>
								<th>Цена (сутки)</th>
								<th>Мин. баланс</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							{if $tariffs}
								<tbody>
									{foreach from=$tariffs item=$t}
										<tr id="item_{$t->getId()}">
											<td class="type">
												<select id="ModuleId-{$t->getId()}" class="form-control">
													{foreach from=$modules item=$m}
														<option value="{$m->getId()}" {if $m->getId()==$t->getModuleId()}selected=""{/if}>{$m->getName()}</option>
													{/foreach}
												</select>
											</td>
										<td class="price-type">
											<select id="PriceType-{$t->getId()}" class="form-control">
											  <option value="items" {if 'items'==$t->getPriceType()}selected=""{/if}>Элементы</option>
											  <option value="period" {if 'period'==$t->getPriceType()}selected=""{/if}>Период</option>
											</select>
										</td>
										<td class="items">
											<div class="input-group">
												<input type="text" id="MinItems-{$t->getId()}" class="form-control only_number" value="{$t->getMinItems()}">
												<span class="input-group-addon"><i class="fa fa-minus"></i></span>
												<input type="text" id="MaxItems-{$t->getId()}" class="form-control only_number" value="{$t->getMaxItems()}">
											</div>
										</td>
										<td class="price">
											<input id="Price-{$t->getId()}" type="text" class="form-control only_number_point" value="{$t->getPrice()}" />
										</td>
										<td class="balance">
											<input id="MinBalance-{$t->getId()}" type="text" class="form-control only_number_point" value="{$t->getMinBalance()}" />
										</td>
										<td class="operations">
											<i class="fa fa-save" style="color: blue;" title="Сохранить" onclick="saveItem('{$t->getId()}');"></i>
											<i class="fa fa-times" style="color: red;" title="Удалить" onclick="deleteItem('{$t->getId()}');"></i>
										</td>
									</tr>
									{/foreach}
								</tbody>
							{else}
								<tfoot>
									<tr>
										<td colspan="6">Записи не найдены</td>
									</tr>
								</tfoot>
							{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}
{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a href="/{ADMIN}/{$module}/create" class="btn btn-info btn-xs pull-right"><i class="fa fa-plus"></i> Добавить</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					{if $pages}
					<div class="table-responsive">
						<table class="table table-bordered table-hover" id="listing">
							<thead>
								<tr>
									<th colspan="2">Информация</th>
									<th>Операции</th>
								</tr>
							</thead>
							<tbody>
							{foreach from=$pages item=$page}
								<tr id="item_{$page->getId()}">
									<td class="sort_handle"><i class="fa fa-navicon"></i></td>
									<td class="main_data">
										<span class="name"><a href="/{ADMIN}/{$module}/edit/{$page->getId()}" title="Редактировать">{$page->getName()}</a></span>
										<span class="add">
											<i class="fa fa-external-link" title="Alias"></i>
											<a href="/{if 'main' != $page->getAlias()}{$page->getAlias()}{/if}" target="_blank">/{$page->getAlias()}</a>
										</span>
									</td>
									<td class="operations">
										<div onclick="switcher(this);" class="switcher {if 'active' == $page->getStatus()}true{else}false{/if}"
											data-field="Status"
											data-value="{if 'active' == $page->getStatus()}1{else}0{/if}"
											data-id="{$page->getId()}">
										</div>
										<a href="/{ADMIN}/{$module}/edit/{$page->getId()}" title="Редактировать"><i class="fa fa-edit btn-edit"></i></a>
										<i class="fa fa-times btn-drop" title="Удалить" onclick="deleteItem('{$page->getId()}');"></i>
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
{include file="../../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a href="/{ADMIN}/{$module}/tariffCreate/{$course->getId()}" class="btn btn-success btn-xs pull-left"><i class="fa fa-plus"></i> Создать тариф</a>
					<a href="/{ADMIN}/{$module}/edit/{$course->getId()}" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к редактированию курса: {$course->getName()}</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
				{if $tariffs}
					<table class="table table-bordered table-hover" id="tariffs_listing">
						<thead>
							<tr>
								<th colspan="2">Название</th>
								<th>Цены</th>
								<th>Действия</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$tariffs item=$p}
							<tr id="item_{$p->getId()}">
								<td class="sort_handle"><i class="fa fa-navicon"></i></td>
								<td class="data">
									<span class="ident"><a href="/{ADMIN}/{$module}/tariffEdit/{$p->getId()}">#{$p->getId()} {$p->getName()}</a></span>
								</td>
								<td class="prices">
									{if $p->getOldPrice()}<div class="old">{$p->getOldPrice()}</div>{/if}
									{$p->getPrice()}
								</td>
								<td class="operations">
									<div onclick="switcherTariff(this);" class="switcher {if 'active' == $p->getStatus()}true{else}false{/if}"
										data-field="status"
										data-value="{if 'active' == $p->getStatus()}1{else}0{/if}"
										data-id="{$p->getId()}">
									</div>
									<a href="/{ADMIN}/{$module}/tariffEdit/{$p->getId()}" title="Редактировать"><i class="fa fa-edit text-info"></i></a>
									<i class="fa fa-times text-danger pointer" title="Удалить" onclick="deleteTariff({$p->getId()});"></i>
								</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				{else}
					<h4>Не найдено ни одного элемента</h4>
				{/if}
				</div>
			</div>
		</div>
	</div>

{include file="../../_units/footer.tpl"}

{include file="../../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="text-right">
						<a href="/{ADMIN}/{$module}/createSphere" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Создать сферу</a>
					</div>
					<div class="clearfix"></div>
					{include file="./filter.tpl" filter=$filter}
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
				{if $spheres}
					{$pagination}
					<table class="table table-bordered table-hover" id="sph_listing">
						<thead>
							<tr>
								<th>Имя</th>
								<th>Действия</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$spheres item=$p}
							<tr id="item_{$p->getId()}">
								<td><a href="/{ADMIN}/{$module}/editSphere/{$p->getId()}">#{$p->getId()} {$p->getName()}</a></td>
								<td class="operations">
									<div onclick="switcher(this,true);" class="switcher {if 'active' == $p->getStatus()}true{else}false{/if}"
										data-field="Status"
										data-value="{if 'active' == $p->getStatus()}1{else}0{/if}"
										data-id="{$p->getId()}">
									</div>
									<a href="/{ADMIN}/{$module}/editSphere/{$p->getId()}" title="Редактировать"><i class="fa fa-edit text-info"></i></a>
									<i class="fa fa-times text-danger pointer" title="Удалить" onclick="deletePartner({$p->getId()},true);"></i>
								</td>
							</tr>
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

{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="text-right">
						<a href="/{ADMIN}/{$module}/create" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Создать курс</a>
					</div>
					<div class="clearfix"></div>
{*					{include file="./filter.tpl" filter=$filter}*}
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
				{if $courses}
					<table class="table table-bordered table-hover" id="listing">
						<thead>
							<tr>
								<th colspan="2">Описание</th>
								<th></th>
								<th>Действия</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$courses item=$p}
							<tr id="item_{$p->getId()}">
								<td class="sort_handle"><i class="fa fa-navicon"></i></td>
								<td class="data">
									<div><a href="/{ADMIN}/{$module}/edit/{$p->getId()}">#{$p->getId()} {$p->getName()}</a></div>
								</td>
								<td class="main">
									
								</td>
								<td class="operations">
									<div onclick="switcherCourse(this);" class="switcher {if 'active' == $p->getStatus()}true{else}false{/if}"
										data-field="status"
										data-value="{if 'active' == $p->getStatus()}1{else}0{/if}"
										data-id="{$p->getId()}">
									</div>
									<a href="/{ADMIN}/{$module}/edit/{$p->getId()}" title="Редактировать"><i class="fa fa-edit text-info"></i></a>
									<i class="fa fa-times text-danger pointer" title="Удалить" onclick="deleteCourse({$p->getId()});"></i>
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

{include file="../_units/footer.tpl"}

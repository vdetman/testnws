{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a onclick="addNew();" class="btn btn-info btn-xs pull-right"><i class="fa fa-plus"></i> Добавить</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
				{if $snipets}
					<table class="table table-bordered table-hover" id="snipets_listing">
						<thead>
							<tr>
								<th>Описание</th>
								<th>HTML</th>
								<th>Действия</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$snipets item=$s}
							<tr id="item_{$s->getIdent()}">
								<td class="main_data">
									<span class="ident"><a href="/{ADMIN}/{$module}/edit/{$s->getIdent()}">{$s->getIdent()}</a>{if 'required' == $s->getType()}<span class="required">(системный)</span>{/if}</span>
									<span class="description">{$s->getDescription()}</span>
									<span class="cont">Контент: {if $s->getLength()}{$s->getLength()} {Helper\Text::getEnding($s->getLength(), 'символ', 'символа', 'символов')}{else}пусто{/if}</span>
								</td>
								<td class="mode">
									<div onclick="switcher(this);" class="switcher {if 'visual' == $s->getMode()}true{else}false{/if}"
										data-field="Mode"
										data-value="{if 'visual' == $s->getMode()}1{else}0{/if}"
										data-id="{$s->getIdent()}">
									</div>
								</td>
								<td class="operations">
									<div onclick="switcher(this);" class="switcher {if 'active' == $s->getStatus()}true{else}false{/if}"
										data-field="Status"
										data-value="{if 'active' == $s->getStatus()}1{else}0{/if}"
										data-id="{$s->getIdent()}">
									</div>
									<a href="/{ADMIN}/{$module}/edit/{$s->getIdent()}" title="Редактировать"><i class="fa fa-edit btn-edit"></i></a>
									<i class="fa fa-times btn-drop" title="Удалить" onclick="deleteItem('{$s->getIdent()}');"></i>
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

{include file="../_units/footer.tpl"}
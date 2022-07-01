{include file="../../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button class="btn btn-info btn-xs pull-right" onclick="createLetter();"><i class="fa fa-plus"></i> Добавить</button>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					{if $letters}
					<div class="table-responsive">
						<table class="table table-bordered table-hover" id="letters_listing">
							<thead>
								<tr>
									<th>Информация</th>
									<th>Действия</th>
								</tr>
							</thead>
							<tbody>
							{foreach from=$letters item=$l}
								<tr id="item_{$l->getId()}">
									<td class="params">
										<span class="ident"><a href="/{ADMIN}/{$module}/editLetter/{$l->getId()}" title="Редактировать">{$l->getIdent()}</a></span>
										{if $l->getIsSystem()}
											<span class="is_system">(системный)</span>
										{/if}
										{foreach from=$languages key=$iso item=$lang}
											<button class="btn btn-xs {if $l->getData($iso)->getFilled()}btn-info{else}btn-danger{/if}" onclick="showLetterBody({$l->getId()}, '{$iso}');"><i class="fa fa-eye"></i> {$lang}</button>
										{/foreach}
										<span class="descr">{$l->getDescription()}</span>
										<span class="used">
											{if $l->getIsUsed()}
												<span class="text-danger">Используется в {$l->getIsUsed()} {Helper\Text::getEnding($l->getIsUsed(), 'уведомлении', 'уведомлениях', 'уведомлениях')}</span>
											{else}
												Не используется в уведомлениях
											{/if}
										</span>
									</td>
									<td class="operations">
										<i class="fa fa-times btn-drop" title="Удалить" onclick="deleteLetter('{$l->getId()}');"></i>
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
	<div id="modalContainer" class="modal fade" aria-hidden="true" style="display: none;"></div>

{include file="../../_units/footer.tpl"}
<div class="row">
	<div class="col-lg-12">
		<div class="row m-b-10">
			<div class="col-lg-12">
				<button onclick="createTelegramTemplate();" class="btn btn-xs btn-info pull-right"><i class="fa fa-plus"></i> Добавить новый шаблон</button>
			</div>
		</div>
		{if $templates}
			<div class="table-responsive">
				<table class="table table-bordered table-hover" id="telegram_templates_listing">
					<thead>
						<tr>
							<th>Информация</th>
							<th>Действия</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$templates item=$t}
						<tr id="item_{$t->getId()}">
							<td class="params">
								<span class="ident"><a href="javascript:void(0);" onclick="editTelegramTemplate({$t->getId()});" title="Редактировать">{$t->getLabel()}</a></span>
								{if $t->getRequired()}
									<span class="is_system">(системный)</span>
								{/if}
								<span class="descr">{$t->getDescription()}</span>
								<span class="used">
									{if $t->getIsUsed()}
										<span class="text-danger">Используется в {$t->getIsUsed()} {Helper\Text::getEnding($t->getIsUsed(), 'уведомлении', 'уведомлениях', 'уведомлениях')}</span>
									{else}
										Не используется в уведомлениях
									{/if}
								</span>
							</td>
							<td class="operations">
								<i class="fa fa-times btn-drop" title="Удалить" onclick="deleteTelegramTemplate({$t->getId()});"></i>
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
<div id="modalContainer" class="modal fade" aria-hidden="true" style="display: none;"></div>
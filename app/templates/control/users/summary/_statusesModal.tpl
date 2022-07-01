<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title">
				<button onclick="addNewStatusModal();" class="btn btn-success btn-xs pull-right"><i class="fa fa-plus"></i> новый статус</button>
				Управление статусами
			</h4>
		</div>
		<div class="modal-body">
			<form id="addNewStatusForm" onsubmit="return false;">
				<div class="form-group">
					{foreach from=$statuses item=$s}
						<div class="row m-b-5">
							<div class="col-lg-7 p-5 bg bg-{$s->getStyle()}">{$s->getName()}</div>
							<div class="col-lg-4 p-5">
								{if isset($statusesUsage[$s->getId()])}
									{$statusesUsage[$s->getId()]} {Helper\Text::getEnding($statusesUsage[$s->getId()], 'запись', 'записи', 'записей')}
								{else}<i><del>не используется</del></i>{/if}
							</div>
							<div class="col-lg-1 p-5 text-right">
								{if !isset($statusesUsage[$s->getId()])}
									<i class="fa fa-times pointer text-danger" onclick="deleteStatus({$s->getId()});"></i>
								{/if}
							</div>
						</div>
					{/foreach}
				</div>
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
		</div>
	</div>
</div>
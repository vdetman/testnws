<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title">
				Изменение записи
				<br>
				#{$summary->getUserId()} | {$summary->getUser()->getEmail()} | {$summary->getUser()->getFirstName()} {$summary->getUser()->getLastName()}
			</h4>
		</div>
		<div class="modal-body">
			<form id="editSummaryForm" onsubmit="return false;">
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									<label>Комментарий</label>
									<textarea class="form-control" name="comment">{$summary->getComment()}</textarea>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									<label>Статус</label>
									<select class="form-control" name="status_id">
										<option value="0">без статуса</option>
										{foreach from=$statuses item=$s}
											<option value="{$s->getId()}" {if {$s->getId()} == $summary->getStatusId()}selected="selected"{/if}>{$s->getName()}</option>
										{/foreach}
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									<label>Менеджер</label>
									<select class="form-control" name="manager_id">
										<option value="0">без менеджера</option>
										{foreach from=$managers item=$m}
											<option value="{$m->getId()}" {if {$m->getId()} == $summary->getManagerId()}selected="selected"{/if}>{$m->getName()} ({$m->getEmail()})</option>
										{/foreach}
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<input name="user_id" type="hidden" value="{$summary->getUserId()}" />
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			<button type="button" class="btn btn-info" onclick="editSummarySave({$summary->getUserId()});">Сохранить</button>
		</div>
	</div>
</div>
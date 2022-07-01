<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title">
				Смена менеджера операции
				#{$o->getId()} | {$o->getCode()} | {$o->getAmount()} <i class="fa fa-ruble"></i>
			</h4>
		</div>
		<div class="modal-body">
			<form class="form-horizontal">
				<div class="form-group">
					<label class="col-sm-2 control-label">Менеджер</label>
					<div class="col-sm-10">
						<select class="form-control" id="NewManagerId">
							{if $managers}
								{foreach from=$managers item=$m}
									<option value="{$m->getId()}" {if {$m->getId()} == $o->getManagerId()}selected="selected"{/if}>{$m->getName()} ({$m->getEmail()})</option>
								{/foreach}
							{/if}
						</select>
					</div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			<button type="button" class="btn btn-info" onclick="changeManagerSave({$o->getId()});">Сохранить</button>
		</div>
	</div>
</div>
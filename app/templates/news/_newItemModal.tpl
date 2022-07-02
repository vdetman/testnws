<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title">Новая новость ;-)</h4>
		</div>
		<div class="modal-body">
			<form class="form-horizontal" id="newItemFrom" onsubmit="return false;">
				<div class="form-group">
					<label class="col-sm-2 control-label">Рубрика</label>
					<div class="col-sm-12">
						<select class="form-control" multiple="" name="rubrics[]">
							{if $managers}
								{foreach from=$managers item=$m}
									<option value="{$m->getId()}" {if {$m->getId()} == $o->getManagerId()}selected="selected"{/if}>{$m->getName()} ({$m->getEmail()})</option>
								{/foreach}
							{/if}
							{function name=rubrics level=level}
								{foreach from=$data item=$item}
									<option value="{$item->getRubric()->getId()}" style="padding-left: {$level * 12 + 16}px;">
										{if $level}&#8627;{/if}
										{$item->getRubric()->getName()}
									</option>
									{if $item->getChilds()}
										{call name=rubrics data=$item->getChilds() level=$level+1}
									{/if}
								{/foreach}
							{/function}
							{call name=rubrics data=$rubricTree->getChilds() level=0}
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Название</label>
					<div class="col-sm-12">
						<input class="form-control" name="news[header]" type="text">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Превьюха</label>
					<div class="col-sm-12">
						<textarea class="form-control" name="news[preview]"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Контент</label>
					<div class="col-sm-12">
						<textarea class="form-control" name="news[content]"></textarea>
					</div>
				</div>
			</form>
			<div class="mt-2" id="newItemResult">
        <div style="display: none;" class="alert alert-success success"></div>
        <div style="display: none;" class="alert alert-danger error"></div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-info" onclick="newItemSave();">Fire!</button>
		</div>
	</div>
</div>
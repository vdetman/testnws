<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h4 class="modal-title">Заполните параметры</h4>
		</div>
		<div class="modal-body">
			<form id="paramForm" onsubmit="return false;">
				{foreach from=$params item='param'}
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{$param.label}</label>
								{if 'input'==$param.type}
									<input type="text" class="form-control" name="{$param.name}" placeholder="{$param.placeholder}">
								{elseif 'textarea'==$param.type}
									<textarea class="form-control autogrow" name="{$param.name}" placeholder="{$param.placeholder}" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 104px;"></textarea>
								{elseif 'select'==$param.type}
									<select class="form-control" name="{$param.name}">
										{foreach from=$param.items item=$i}
										<option value="{$i.value}" {if $i.selected}selected="selected"{/if}>{$i.option}</option>
										{/foreach}
									</select>
								{/if}
								{if $param.help}
									<span class="help-block"><small>{$param.help}</small></span>
								{/if}
							</div>
						</div>
					</div>
				{/foreach}
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-white" data-dismiss="modal">Закрыть</button>
			<button type="button" class="btn btn-info" onclick="telegramRequest('{$method}');">Отправить</button>
		</div>
	</div>
</div>
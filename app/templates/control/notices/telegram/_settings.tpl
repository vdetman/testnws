<div class="row">
	<div class="col-lg-12">
		<div class="row m-b-10">
			<div class="col-lg-12">
				<button onclick="$('#settingForm').submit();" class="btn btn-xs btn-success pull-left"><i class="fa fa-save"></i> Сохранить</button>
				<button onclick="createTelegramProperty();" class="btn btn-xs btn-info pull-right"><i class="fa fa-plus"></i> Добавить новое свойство</button>
			</div>
		</div>
		<form action="" method="post" id="settingForm">
			{foreach from=$settings item='s'}
			<div class="form-group" id="item_{$s->getId()}">
				<div class="row">
					<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-addon">{$s->getProperty()}</span>
							<input type="text" name="settings[{$s->getId()}]" value="{$s->getValue()}" class="form-control" />
							{if !$s->getRequired()}
								<span class="input-group-btn"><button type="button" onclick="deleteTelegramProperty({$s->getId()});" class="btn btn-danger"><i class="fa fa-times"></i></button></span>
							{/if}
						</div>
					</div>
				</div>
			</div>
			{/foreach}
			<input type="hidden" name="save" value="1" />
		</form>
	</div>
</div>
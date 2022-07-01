<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h4 class="modal-title">Заполните параметры</h4>
		</div>
		<div class="modal-body">
			<form action="" method="post" id="mainForm">
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-3">
									<label>Идентификатор <span style="color:red;">*</span></label>
									<input readonly="" class="form-control" value="{$template->getLabel()}" />
								</div>
								<div class="col-lg-9">
									<label>Описание <span style="color:red;">*</span></label>
									<input name="field[Description]" class="form-control" value="{$template->getDescription()}" />
								</div>
							</div>
						</div>
						{include file="./_editTemplateData.tpl" template=$template languages=$languages}
					</div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-white" data-dismiss="modal">Закрыть</button>
			<button type="button" class="btn btn-info" onclick="saveTelegramTemplate({$template->getId()});">Сохранить</button>
		</div>
	</div>
</div>
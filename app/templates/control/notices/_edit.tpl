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
									<label>Получатель <span style="color:red;">*</span></label>
									<input name="field[Recipient]" class="form-control" value="{$notice->getRecipient()}" />
								</div>
								<div class="col-lg-9">
									<label>Тема письма <span style="color:red;">*</span> <small>(только для Email)</small></label>
									<input name="field[Subject]" class="form-control" value="{$notice->getSubject()}" />
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									<label>Сообщение <span style="color:red;">*</span></label>
									<textarea class="form-control" name="field[Message]">{$notice->getMessage()}</textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-white" data-dismiss="modal">Закрыть</button>
			<button type="button" class="btn btn-info" onclick="saveNotice({$notice->getId()});">Сохранить</button>
		</div>
	</div>
</div>
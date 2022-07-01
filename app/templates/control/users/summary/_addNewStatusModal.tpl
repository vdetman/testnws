<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title">
				<button onclick="statusesModal();" class="btn btn-success btn-xs pull-right"><i class="fa fa-reply"></i> вернуться к списку</button>
				Создание нового статуса
			</h4>
		</div>
		<div class="modal-body">
			<form id="addNewStatusForm" onsubmit="return false;">
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									<label>Название <span style="color:red;">*</span></label>
									<input name="statusName" class="form-control" value="" />
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									<label>Стиль <span style="color:red;">*</span></label>
									{if $styles}
										{foreach from=$styles item=$s name=foo}
											<label class="bg bg-{$s} p-5 m-b-5 pointer">
												<input name="statusStyle" type="radio" {if $smarty.foreach.foo.index == 0}checked=""{/if} class="" value="{$s|lower}" />
												{$s|upper}
											</label>
										{/foreach}
									{/if}
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			<button type="button" class="btn btn-info" onclick="addNewStatusConfirm();">Создать</button>
		</div>
	</div>
</div>
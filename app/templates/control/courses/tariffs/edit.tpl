{include file="../../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#mainForm').submit();" class="btn btn-success btn-xs pull-left"><i class="fa fa-save"></i> Сохранить</button>
					<a href="/{ADMIN}/{$module}/tariffs/{$course->getId()}" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку уроков курса: {$course->getName()}</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="mainForm">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-6">
											<label>Название</label>
											<input name="field[name]" class="form-control" value="{$tariff->getName()}" />
										</div>
										<div class="col-lg-3">
											<label>Цена</label>
											<input name="field[price]" class="form-control" value="{$tariff->getPrice()}" />
										</div>
										<div class="col-lg-3">
											<label>Старая цена</label>
											<input name="field[old_price]" class="form-control" value="{$tariff->getOldPrice()}" />
										</div>
									</div>
								</div>
										
								<div class="form-group">
									<div class="row">
										<div class="col-lg-12">
											<label>Описание</label>
											<textarea class="form-control" rows="15" name="field[description]">{$tariff->getDescription()}</textarea>
										</div>
									</div>
								</div>
								<input type="hidden" name="save" value="1" />
								<button type="submit" class="btn btn-success btn-xs"><i class="fa fa-save"></i> Сохранить</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

{include file="../../_units/footer.tpl"}

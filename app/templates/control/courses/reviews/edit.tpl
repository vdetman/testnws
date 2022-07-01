{include file="../../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#mainForm').submit();" class="btn btn-success btn-xs pull-left"><i class="fa fa-save"></i> Сохранить</button>
					<a href="/{ADMIN}/{$module}/reviews/{$course->getId()}" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку уроков курса: {$course->getName()}</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="mainForm">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-6">
											<label>Имя</label>
											<input name="field[name]" class="form-control" value="{$review->getName()}" />
										</div>
										<div class="col-lg-3">
											<label>Компания</label>
											<input name="field[company]" class="form-control" value="{$review->getCompany()}" />
										</div>
										<div class="col-lg-3">
											<label>Видео</label>
											<input name="field[video]" class="form-control" value="{$review->getVideo()}" />
										</div>
									</div>
								</div>
										
								<div class="form-group">
									<div class="row">
										<div class="col-lg-12">
											<label>Описание</label>
											<textarea class="form-control" rows="15" name="field[content]">{$review->getContent()}</textarea>
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

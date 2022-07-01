{include file="../../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#mainForm').submit();" class="btn btn-success btn-xs pull-left"><i class="fa fa-save"></i> Сохранить</button>
					<a href="/{ADMIN}/{$module}/lessons/{$course->getId()}" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку уроков курса: {$course->getName()}</a>
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
											<input name="field[name]" class="form-control" value="{$lesson->getName()}" />
										</div>
										<div class="col-lg-3">
											<label>Видео</label>
											<input name="field[video]" class="form-control" value="{$lesson->getVideo()}" />
										</div>
										<div class="col-lg-3">
											<label>Длительность</label>
											<input name="field[duration]" class="form-control" value="{$lesson->getDuration()}" />
										</div>
									</div>
								</div>
										
								<div class="form-group">
									<div class="row">
										<div class="col-lg-6">
											<label>Картинки</label>
											{include file="./_photo.tpl" lesson=$lesson}
										</div>
										<div class="col-lg-6">
											<label>Файлы</label>
											{include file="./_files.tpl" lesson=$lesson}
										</div>
									</div>
								</div>
										
								<div class="form-group">
									<div class="row">
										<div class="col-lg-12">
											<label>Описание</label>
											<textarea class="form-control" rows="15" name="field[description]">{$lesson->getDescription()}</textarea>
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
	<script type="text/javascript">
		$(document).ready(function(){ ckeditorInit('.textarea-editor', '{ADMIN}'); });
	</script>

{include file="../../_units/footer.tpl"}

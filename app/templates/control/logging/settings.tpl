{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<form action="" method="post">
				<div class="panel panel-default">
					<div class="panel-heading">
						Логирование посещений
						<button onclick="clearLogs('general'); return false;" class="btn btn-danger btn-xs m-l-10" style="float: right;">Очистить логи посещений</button>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4">
								<div class="form-group">
									<label>Активно</label>
									<select class="form-control" name="field[enable]">
										<option value="true" {if $settingsList.enable}selected="selected"{/if}>да</option>
										<option value="false" {if !$settingsList.enable}selected="selected"{/if}>нет</option>
									</select>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="form-group">
									<label>Логировать AJAX</label>
									<select class="form-control" name="field[ajax]">
										<option value="true" {if $settingsList.enableAjax}selected="selected"{/if}>да</option>
										<option value="false" {if !$settingsList.enableAjax}selected="selected"{/if}>нет</option>
									</select>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="form-group">
									<label>Логировать роботов</label>
									<select class="form-control" name="field[robots]">
										<option value="true" {if $settingsList.enableRobots}selected="selected"{/if}>да</option>
										<option value="false" {if !$settingsList.enableRobots}selected="selected"{/if}>нет</option>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Список исключений URL запроса</label>
							<textarea rows="15" name="field[exceptions]" class="form-control">{$settingsList.exceptions}</textarea>
							<p class="help-block">Маска (регулярное выражение) URL, исключенного из логирования. Одна маска на одной строке</p>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<input type="submit" name="submit" class="btn btn-success" value="Сохранить" />
							</div>
						</div>
						{if $result}
							<div class="alert {if $result.status}alert-success{else}alert-danger{/if} m-t-15">{$result.descr}</div>
						{/if}
					</div>
				</div>
			</form>
		</div>
	</div>

{include file="../_units/footer.tpl"}
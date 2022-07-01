{include file="../../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#mainForm').submit();" class="btn btn-success btn-xs pull-left"><i class="fa fa-save"></i> Сохранить</button>
					<a href="/{ADMIN}/{$module}/spheres" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="mainForm">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-10">
											<label>Имя</label>
											<input name="field[Name]" class="form-control" value="{$sphere->getName()}" />
										</div>
										<div class="col-lg-2">
											<label>Статус</label>
											<select class="form-control" name="field[Status]">
												<option value="active" {if 'active' == $sphere->getStatus()}selected="selected"{/if}>Активен</option>
												<option value="hidden" {if 'hidden' == $sphere->getStatus()}selected="selected"{/if}>Неактивен</option>
											</select>
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
	<script type="text/javascript">$(document).ready(function(){ ckeditorInit('#Text', '{ADMIN}'); });</script>

{include file="../../_units/footer.tpl"}

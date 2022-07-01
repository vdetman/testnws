{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#mainForm').submit();" class="btn btn-success btn-xs pull-left"><i class="fa fa-save"></i> Сохранить</button>
					<a href="/{ADMIN}/snipets" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="mainForm">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Идентификатор</label>
											<input class="form-control" readonly="true" value="{$snipet->getIdent()}" />
										</div>
										<div class="col-lg-9">
											<label>Описание</label>
											<input name="field[Description]" class="form-control" value="{$snipet->getDescription()}" />
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-12">
											<label>Контент</label>
											<textarea class="form-control" rows="15" name="field[Content]" id="{if 'visual' == $snipet->getMode()}Content{/if}">{$snipet->getContent()}</textarea>
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

	{if 'visual' == $snipet->getMode()}
		<script type="text/javascript">$(document).ready(function(){ ckeditorInit('#Content', '{ADMIN}'); });</script>
	{/if}

{include file="../_units/footer.tpl"}
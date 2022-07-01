{include file="../../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a href="/{ADMIN}/{$module}/letters" class="btn btn-primary btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
					<button onclick="$('#mainForm').submit();" class="btn btn-purple btn-xs pull-left"><i class="fa fa-save"></i> Сохранить</button>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="mainForm">
						<div class="row">
							<div class="col-lg-12">
								<div class="row m-b-10">
									<div class="col-lg-12">
										{if $result->getMessage() || $result->getError()}
											<div class="alert {if $result->success()}alert-success{else}alert-danger{/if} p-5 inline m-b-0">
												{if $result->getError()}{$result->getError()}{else}{$result->getMessage()}{/if}
											</div>
										{/if}
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Идентификатор <span style="color:red;">*</span></label>
											<input readonly="" class="form-control" value="{$letter->getIdent()}" />
										</div>
										<div class="col-lg-9">
											<label>Описание <span style="color:red;">*</span></label>
											<input name="field[Description]" class="form-control" value="{if isset($post)}{$post.Description}{else}{$letter->getDescription()}{/if}" />
										</div>
									</div>
								</div>

								{if !isset($data)}{assign var='data' value=false}{/if}
								{include file="./_data.tpl" letter=$letter data=$data languages=$languages}
								
								<input type="hidden" name="save" value="1" />
								<div class="row">
									<div class="col-lg-12">
										<button type="submit" class="btn btn-purple">Сохранить</button>
										{if $result->getMessage() || $result->getError()}
											<div class="alert {if $result->success()}alert-success{else}alert-danger{/if} p-5 inline m-b-0">
												{if $result->getError()}{$result->getError()}{else}{$result->getMessage()}{/if}
											</div>
										{/if}
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div id="modalContainer" class="modal fade" aria-hidden="true" style="display: none;"></div>
	
{include file="../../_units/footer.tpl"}
{include file="../_units/header.tpl"}

	<div class="row" id="operation_detail">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#mainForm').submit();" class="btn btn-success btn-xs pull-left m-r-10"><i class="fa fa-save"></i> Сохранить</button>
					<a href="/{ADMIN}/{$module}{$backQuery}" class="btn btn-primary btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="mainForm">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-10">
											<label>Имя <span style="color:red;">*</span></label>
											<input name="field[name]" class="form-control" value="{if isset($partner) && $partner.Name}{$partner.Name}{/if}" />
										</div>
									</div>
								</div>
								<button type="submit" class="btn btn-success btn-xs pull-left m-r-10"><i class="fa fa-save"></i> Сохранить</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}

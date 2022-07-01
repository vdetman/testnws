{include file="../../_units/header.tpl"}

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
										<div class="col-lg-2">
											<label>Язык <span style="color:red;">*</span></label>
											<select class="form-control" name="field[Language]">
												<option value="ru" {if isset($post) && 'ru' == $post.Language}selected="selected"{/if}>RU</option>
											</select>
										</div>
										<div class="col-lg-10">
											<label>Описание <span style="color:red;">*</span></label>
											<input name="field[Name]" class="form-control" value="{if isset($post) && $post.Name}{$post.Name}{/if}" />
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

{include file="../../_units/footer.tpl"}
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
										<div class="col-lg-2">
											<label>Тип операции <span style="color:red;">*</span></label>
											<select class="form-control" name="field[TypeId]">
												<option value=""></option>
												<option value="5" {if isset($post) && 5 == $post.TypeId}selected="selected"{/if}>Бонус</option>
												<option value="2" {if isset($post) && 2 == $post.TypeId}selected="selected"{/if}>Возврат средств</option>
												<option value="7" {if isset($post) && 7 == $post.TypeId}selected="selected"{/if}>Услуги</option>
												<option value="8" {if isset($post) && 8 == $post.TypeId}selected="selected"{/if}>Вывод партнерской прибыли</option>
											</select>
										</div>
										<div class="col-lg-2">
											<label>Сумма <span style="color:red;">*</span></label>
											<input name="field[Amount]" class="form-control" value="{if isset($post) && $post.Amount}{$post.Amount}{/if}" />
										</div>
										<div class="col-lg-3">
											<label>Пользователь <span style="color:red;">*</span></label>
											<input class="form-control" name="field[User]" id="findUser" value="{if isset($post) && $post.User}{$post.User}{/if}" />
											<input type="hidden" id="UserId" name="field[UserId]" value="{if isset($post) && $post.UserId}{$post.UserId}{/if}" />
											<div id="ac_users"></div>
										</div>
										<div class="col-lg-5">
											<label>Комментарий</label>
											<input name="field[Description]" class="form-control" value="{if isset($post) && $post.Description}{$post.Description}{/if}" />
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
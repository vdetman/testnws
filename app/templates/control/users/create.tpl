{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a href="/{ADMIN}/users/{$backQuery}" class="btn btn-primary btn-xs" style="float: right;">Вернуться к списку пользователей</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<div class="alert alert-info">Заполните текущие поля. После этого Вы будете перенаправлены на страницу детальной настройки</div>
					<form action="" method="post">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>E-mail <span class="text-danger">*</span></label>
											<input name="field[Email]" class="form-control" value="{if isset($post)}{$post.Email}{/if}" />
										</div>
										<div class="col-lg-3">
											<label>Логин <span class="text-danger">*</span></label>
											<input name="field[Username]" class="form-control" value="{if isset($post)}{$post.Username}{/if}" />
										</div>
										<div class="col-lg-3">
											<label>Имя <span class="text-danger">*</span></label>
											<input name="field[FirstName]" class="form-control" value="{if isset($post)}{$post.FirstName}{/if}" />
										</div>
										<div class="col-lg-3">
											<label>Роль <span class="text-danger">*</span></label>
											<select class="form-control" name="field[Role]">
												<option value="">не указано</option>
												{foreach from=$roles key=$r item=$d}
												<option value="{$r}" {if isset($post) && $post.Role == $r}selected="selected"{/if}>{$d}</option>
												{/foreach}
											</select>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-4">
											<label>Телефон</label>
											<input name="field[Phone]" class="form-control" value="{if isset($post)}{$post.Phone}{/if}" />
										</div>
										<div class="col-lg-4">
											<label>Фамилия</label>
											<input name="field[LastName]" class="form-control" value="{if isset($post)}{$post.LastName}{/if}" />
										</div>
										<div class="col-lg-4">
											<label>Отчество</label>
											<input name="field[SecondName]" class="form-control" value="{if isset($post)}{$post.SecondName}{/if}" />
										</div>
									</div>
								</div>
								{if $result}
									<div class="alert {if $result.status}alert-success{else}alert-danger{/if} m-t-15">{$result.descr}</div>
								{/if}
								<div class="row">
									<div class="col-lg-12">
										<input type="submit" name="submit" class="btn btn-success" value="Сохранить" />
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /.col-lg-12 -->
	</div>

{include file="../_units/footer.tpl"}
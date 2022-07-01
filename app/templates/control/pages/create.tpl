{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a href="/{ADMIN}/{$module}" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<div class="alert alert-info">Заполните текущие поля. После этого Вы будете перенаправлены на страницу детальной настройки</div>
					<form action="" method="post">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-6">
											<label>Заголовок <span class="text-danger">*</span></label>
											<input name="field[Name]" class="form-control" value="{if isset($post)}{$post.Name}{/if}" />
										</div>
										<div class="col-lg-6">
											<label>Alias <span class="text-danger">*</span></label>
											<input name="field[Alias]" class="form-control" value="{if isset($post)}{$post.Alias}{else}{$alias}{/if}" />
										</div>
									</div>
								</div>
								{if $result->getMessage() || $result->getError()}
									<div class="alert {if $result->success()}alert-success{else}alert-danger{/if}">
										{if $result->getError()}{$result->getError()}{else}{$result->getMessage()}{/if}
									</div>
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
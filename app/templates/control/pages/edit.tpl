{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="pull-left">
						<button onclick="$('#_form').submit();" class="btn btn-success btn-xs"><i class="fa fa-save"></i> Сохранить</button>
					</div>
					<a href="/{ADMIN}/{$module}" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="_form">
						{if $result->getMessage() || $result->getError()}
							<div class="alert {if $result->success()}alert-success{else}alert-danger{/if}">
								{if $result->getError()}{$result->getError()}{else}{$result->getMessage()}{/if}
							</div>
						{/if}
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-7">
											<label>Название <span style="color:red;">*</span></label>
											<input name="field[Name]" class="form-control" value="{if isset($post)}{$post.Name}{else}{$page->getName()}{/if}" />
										</div>
										<div class="col-lg-3">
											<label>Алиас <span style="color:red;">*</span></label>
											<input name="field[Alias]" class="form-control" value="{if isset($post)}{$post.Alias}{else}{$page->getAlias()}{/if}" />
										</div>
										<div class="col-lg-2">
											<label>Активность</label>
											<select class="form-control" name="field[Status]">
												<option value="active" {if isset($post) && 'active' == $post.Status || !isset($post) && 'active' == $page->getStatus()}selected="selected"{/if}>Да</option>
												<option value="hidden" {if isset($post) && 'hidden' == $post.Status || !isset($post) && 'hidden' == $page->getStatus()}selected="selected"{/if}>Нет</option>
											</select>
										</div>
									</div>
								</div>

								{if !isset($data)}{assign var='data' value=false}{/if}
								{include file="./_data.tpl" page=$page data=$data languages=$languages}
								
								<input type="hidden" name="save" value="1" />
								{if $result->getMessage() || $result->getError()}
									<div class="alert {if $result->success()}alert-success{else}alert-danger{/if}">
										{if $result->getError()}{$result->getError()}{else}{$result->getMessage()}{/if}
									</div>
								{/if}
								<div class="row">
									<div class="col-lg-12">
										<button type="submit" class="btn btn-success btn-xs"><i class="fa fa-save"></i> Сохранить</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}
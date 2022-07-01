{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#mainForm').submit();" class="btn btn-success btn-xs pull-left m-r-10"><i class="fa fa-save"></i> Сохранить</button>
					<button onclick="checkProxy({$proxy->getId()});" class="btn btn-default btn-xs pull-left"><i class="fa fa-refresh"></i> Проверить</button>
					<a href="/{ADMIN}/{$module}{$backQuery}" class="btn btn-primary btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="mainForm">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-6">
											<label>Название <span style="color:red;">*</span></label>
											<input name="field[Name]" class="form-control" value="{if isset($post)}{$post.Name}{else}{$proxy->getName()}{/if}" />
										</div>
										<div class="col-lg-3">
											<label>Истекает</label>
											<input name="field[Expires]" autocomplete="off" class="form-control datepicker" value="{if isset($post)}{$post.Expires}{elseif $proxy->getExpires()}{$proxy->getExpires()->format('d.m.Y')}{/if}" />
										</div>
										<div class="col-lg-3">
											<label>Статус</label>
											<select class="form-control" name="field[Status]">
												<option value="active" {if isset($post) && 'active' == $post.Status || !isset($post) && 'active' == $proxy->getStatus()}selected="selected"{/if}>Активен</option>
												<option value="expired" {if isset($post) && 'expired' == $post.Status || !isset($post) && 'expired' == $proxy->getStatus()}selected="selected"{/if}>Истекший</option>
												<option value="disabled" {if isset($post) && 'disabled' == $post.Status || !isset($post) && 'disabled' == $proxy->getStatus()}selected="selected"{/if}>Заблокирован</option>
											</select>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-4">
											<label>IP <span style="color:red;">*</span></label>
											<input name="field[Ip]" class="form-control" value="{if isset($post)}{$post.Ip}{else}{$proxy->getIp()}{/if}" />
										</div>
										<div class="col-lg-2">
											<label>Порт <span style="color:red;">*</span></label>
											<input name="field[Port]" class="form-control" value="{if isset($post)}{$post.Port}{else}{$proxy->getPort()}{/if}" />
										</div>
										<div class="col-lg-3">
											<label>Логин</label>
											<input name="field[Login]" class="form-control" value="{if isset($post)}{$post.Login}{else}{$proxy->getLogin()}{/if}" />
										</div>
										<div class="col-lg-3">
											<label>Пароль</label>
											<input name="field[Password]" class="form-control" value="{if isset($post)}{$post.Password}{else}{$proxy->getPassword()}{/if}" />
										</div>
									</div>
								</div>
								<input type="hidden" name="save" value="1" />
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
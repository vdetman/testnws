{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-4">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Смена пароля</h3>
				</div>
				<div class="panel-body">
					<form role="form" action="" method="post">
						<div class="form-group">
							<label for="currentPassword">Текущий пароль</label>
							<input type="password" name="password[current]" class="form-control" id="currentPassword"  />
						</div>
						<div class="form-group">
							<label for="newPassword">Новый пароль</label>
							<input type="password" name="password[new]" class="form-control" id="newPassword" />
						</div>
						<div class="form-group">
							<label for="confirmPassword">Новый пароль еще раз</label>
							<input type="password" name="password[confirm]" class="form-control" id="confirmPassword" />
						</div>
						{if $error}
							<div class="alert alert-danger">{$error}</div>
						{/if}
						{if $success}
							<div class="alert alert-success">{$success}</div>
						{/if}
						<input type="hidden" name="change_password" value="1" />
						<button type="submit" class="btn btn-purple">Сохранить</button>
					</form>
				</div><!-- panel-body -->
			</div>
		</div>
		<div class="col-lg-8">
			<div class="panel panel-color panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">Двухфакторная аутентификация</h3>
				</div>
				<div class="panel-body">
					<div class="panel-body">
						{if isset($tfa_list) && $tfa_list}
							<div class="form-group">
								<select class="form-control" id="setTfaType" onchange="showTfaPanel($(this).val(), 'enable');">
									<option value="disable" {if 'disable' == $currentUser->getTfaType()}selected="true"{/if}>Отключена</option>
									<option value="sms" {if 'sms' == $currentUser->getTfaType()}selected="true"{/if}>СМС</option>
									<option value="ga" {if 'ga' == $currentUser->getTfaType()}selected="true"{/if}>Google Authenticator</option>
								</select>
							</div>
						{else}
							<h4>Активирована двухфакторная аутентификация {$tfa_type_description}</h4>
							<button type="submit" class="btn btn-danger" id="tfa_disable_btn" onclick="showTfaPanel('{$tfa_type}', 'disable');">Отключить</button>
						{/if}
						<div id="tfa_panel"></div>
					</div>
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}
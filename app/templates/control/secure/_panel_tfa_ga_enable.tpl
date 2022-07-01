<div class="alert alert-info">
Для активации двухэтапной аутентификации отсканируйте штрих-код с помощью приложения
<a href="https://support.google.com/accounts/answer/1066447?hl=ru" target="_blank">Google Authenticator</a>,
в поле "Google Authenticator code" введите сгенерированный код. Убедитесь, что разница во времени на сервере и
на телефоне составляет менее, чем 1 минуту. Если штрих-код не распознается, то для настройки аккаунта в
приложении Google Authenticator воспользуйтесь текстовыми данными, расположенными ниже.
</div>
<img src="/{ADMIN}/secure/tfa_ga_show_qr" />
<div class="form-group">
	<label for="username">Аккаунт</label>
	<input type="text" readonly="true" value="{$username}" class="form-control" id="username" />
</div>
<div class="form-group">
	<label for="secret">Ключ</label>
	<input type="text" readonly="true" value="{$secret}" class="form-control" id="secret" />
</div>
<div class="input-group m-t-10">
	<input type="text" autocomplete="off" id="tfa_ga_code" name="tfa_ga_code" class="form-control" placeholder="Google Authenticator code" />
	<span class="input-group-btn">
		<button type="button" onclick="confirmGAtfa('{$action}', '{$hashAction}');" id="tfa_ga_confirm" class="btn btn-effect-ripple btn-primary">Подтвердить</button>
	</span>
</div>
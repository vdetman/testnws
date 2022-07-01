<div class="input-group m-t-10">
	<input type="text" autocomplete="off" id="tfa_ga_code" name="tfa_ga_code" class="form-control" placeholder="Google Authenticator code" />
	<span class="input-group-btn">
		<button type="button" onclick="confirmGAtfa('{$action}', '{$hashAction}');" id="tfa_ga_confirm" class="btn btn-effect-ripple btn-primary">Подтвердить</button>
	</span>
</div>
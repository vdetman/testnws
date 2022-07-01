<div class="input-group m-t-10">
	<span class="input-group-btn">
		<button type="button" id="tfa_sms_send_btn" onclick="sendSMStfa('{$phone}', '{$hash}');" class="btn btn-effect-ripple btn-primary">Отправить СМС</button>
	</span>
	<input type="text" autocomplete="off" id="tfa_sms_code" disabled="true" name="tfa_sms_code" class="form-control" placeholder="SMS code" />
	<span class="input-group-btn">
		<button type="button" disabled="true" onclick="confirmSMStfa('{$action}', '{$hashAction}');" id="tfa_sms_confirm" class="btn btn-effect-ripple btn-primary">Подтвердить</button>
	</span>
</div>
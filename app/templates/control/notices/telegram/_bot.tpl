<div class="row">
	<div class="col-lg-12">
		<div class="form-group">
			<div class="row">
				<div class="col-md-12">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-key"></i> Token</span>
						<input type="text" id="BotToken" value="{$settings['BOT_TOKEN']->getValue()}" class="form-control" placeholder="1234567890:AABBCC1234567890DDEEFF1234567890GGH" />
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-md-12">
					<button class="btn btn-xs btn-success m-r-5" onclick="telegramRequest('getMe');"><i class="fa fa-android"></i> getMe</button>
					<button class="btn btn-xs btn-success m-r-5" onclick="telegramRequest('getUpdates');"><i class="fa fa-info"></i> getUpdates</button>
					<button class="btn btn-xs btn-success m-r-5" onclick="telegramRequest('getWebhookInfo');"><i class="fa fa-info-circle"></i> getWebhookInfo</button>
					<button class="btn btn-xs btn-warning m-r-5" onclick="telegramRequest('setWebhook', true);"><i class="fa fa-link"></i> setWebhook</button>
					<button class="btn btn-xs btn-danger m-r-5" onclick="telegramRequest('deleteWebhook', false, true);"><i class="fa fa-unlink"></i> deleteWebhook</button>
					<button class="btn btn-xs btn-info" onclick="telegramRequest('sendMessage', true);"><i class="fa fa-paper-plane-o"></i> sendMessage</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="modalContainer" class="modal fade" aria-hidden="true" style="display: none;"></div>
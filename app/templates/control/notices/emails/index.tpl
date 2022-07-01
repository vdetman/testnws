{include file="../../_units/header.tpl"}

	<div class="row m-b-15">
		<div class="col-lg-12">
			<a class="btn btn-success btn-xs" onclick="createEmail();">Добавить новый</a>
		</div>
	</div>

	{if $accounts}
	<div class="row" id="email_listing">
		{foreach from=$accounts item=$e}
			<div class="col-lg-{$lg}">
				<div class="panel panel-default">
					<div class="panel-heading">
						{$e->getIdent()}
						{if $e->getIsDefault()}
							<small>(по умолчанию)</small>
						{/if}
						<span class="used">
							{if $e->getUsed()}
								<span class="text-danger">Используется в {$e->getUsed()} {Helper\Text::getEnding($e->getUsed(), 'уведомлении', 'уведомлениях', 'уведомлениях')}</span>
							{else}
								Не используется в уведомлениях
							{/if}
						</span>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label for="secret">Email отправителя</label>
							<input type="text" class="form-control detected" id="em_email_{$e->getId()}" data-id="{$e->getId()}" value="{$e->getEmail()}" />
						</div>
						<div class="form-group">
							<label for="secret">Имя отправителя</label>
							<input type="text" class="form-control detected" id="em_name_{$e->getId()}" data-id="{$e->getId()}" value="{$e->getName()}" />
						</div>
						<div class="form-group">
							<label for="secret">Логин</label>
							<input type="text" class="form-control detected" id="em_user_{$e->getId()}" data-id="{$e->getId()}" value="{$e->getUser()}" />
						</div>
						<div class="form-group">
							<label for="secret">Пароль</label>
							<input type="text" class="form-control detected" id="em_pass_{$e->getId()}" data-id="{$e->getId()}" value="{$e->getPassword()}" />
						</div>
						<div class="form-group">
							<label for="secret">Сервер</label>
							<input type="text" class="form-control detected" id="em_host_{$e->getId()}" data-id="{$e->getId()}" value="{$e->getHost()}" />
						</div>
						<div class="form-group">
							<label for="secret">Порт</label>
							<input type="text" class="form-control detected" id="em_port_{$e->getId()}" data-id="{$e->getId()}" value="{$e->getPort()}" />
						</div>
						<div class="btn-group btn-group-justified">
							<a class="btn btn-success" id="btn-save-{$e->getId()}" onclick="updateEmail('{$e->getId()}');" title="Сохранить"><i class="fa fa-save"></i></a>
							<a class="btn btn-primary" onclick="setDefaultEmail('{$e->getId()}');" title="Сделать аккаунтом по умолчанию"><i class="fa fa-check"></i></a>
							<a class="btn btn-warning" onclick="checkEmail('{$e->getId()}');" title="Проверить настройки SMTP"><i class="fa fa-exclamation-circle"></i></a>
							<a class="btn btn-danger" onclick="deleteEmail('{$e->getId()}');" title="Удалить"><i class="fa fa-times"></i></a>
						</div>
					</div>
				</div>
			</div>
		{/foreach}
	</div>
	{/if}
	<div id="ajax_result"></div>

{include file="../../_units/footer.tpl"}
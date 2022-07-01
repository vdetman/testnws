{include file="../../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-8">
			<div class="form-group">
				<label>Для детальной настройки выберите событие</label>
				<select class="form-control" id="EventId">
					<option value="">выберите событие из списка</option>
					{foreach from=$groups item=$g}
						{if $g->getEvents()}
						<optgroup label="{$g->getName()}">
							{foreach from=$g->getEvents() item=$e}
							<option {if $event && $event->getId() == $e->getId()}selected="selected"{/if} value="{$e->getId()}">{$e->getDescription()} ({$e->getIdent()})</option>
							{/foreach}
						</optgroup>
						{/if}
					{/foreach}
				</select>
			</div>
		</div>
		<div class="col-lg-4">
			<a class="btn btn-success btn-xs m-l-10 pull-right"onclick="createNewEvent();">Добавить событие</a>
			<a class="btn btn-info btn-xs pull-right" onclick="createEventGroup();">Добавить группу</a>
		</div>
	</div>
	{if $event}
		<div class="row">
			<div class="col-lg-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div onclick="switcherEvent(this);" class="switcher {if $event->getData()->getUserUseEmail()}true{else}false{/if} pull-right"
							data-field="UserUseEmail"
							data-value="{if $event->getData()->getUserUseEmail()}1{else}0{/if}"
							data-id="{$event->getId()}">
						</div>
						E-mail пользователю
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label>Шаблон письма</label>
							<select class="form-control select_autosave" data-eid="{$event->getId()}"  data-field="UserLetterId">
								<option value="">не указан</option>
								{foreach from=$letters item=$l}
								<option value="{$l->getId()}" {if $event->getData()->getUserLetterId() == $l->getId()}selected="selected"{/if}>{$l->getIdent()} ({$l->getDescription()})</option>
								{/foreach}
							</select>
						</div>
						<div class="form-group">
							<label>E-mail отправитель</label>
							<select class="form-control select_autosave" data-eid="{$event->getId()}"  data-field="UserAccountId">
								<option value="">По умолчанию {$defaultAccount}</option>
								{foreach from=$accounts item=$a}
								<option value="{$a->getId()}" {if $event->getData()->getUserAccountId() == $a->getId()}selected="selected"{/if}>{$a->getIdent()} ({$a->getEmail()})</option>
								{/foreach}
							</select>
						</div>
						<div class="form-group">
							<label>E-mail получатель</label>
							<p class="form-control-static">* E-mail пользователя</p>
						</div>
					</div>
					<hr class="m-t-0" />
					<div class="panel-heading">
						<div onclick="switcherEvent(this);" class="switcher {if $event->getData()->getUserUseTelegram()}true{else}false{/if} pull-right"
							data-field="UserUseTelegram"
							data-value="{if $event->getData()->getUserUseTelegram()}1{else}0{/if}"
							data-id="{$event->getId()}">
						</div>
						Telegram пользователю
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label>Шаблон сообщения</label>
							<select class="form-control select_autosave" data-eid="{$event->getId()}"  data-field="UserTelegramTemplateId">
								<option value="">не указан</option>
								{foreach from=$templates item=$t}
								<option value="{$t->getId()}" {if $event->getData()->getUserTelegramTemplateId() == $t->getId()}selected="selected"{/if}>{$t->getLabel()} ({$t->getDescription()})</option>
								{/foreach}
							</select>
						</div>

						<div class="form-group">
							<label>Telegram получатель</label>
							<p class="form-control-static">* Telegram ChatId пользователя</p>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div onclick="switcherEvent(this);" class="switcher {if $event->getData()->getAdminUseEmail()}true{else}false{/if} pull-right"
							data-field="AdminUseEmail"
							data-value="{if $event->getData()->getAdminUseEmail()}1{else}0{/if}"
							data-id="{$event->getId()}">
						</div>
						E-mail Администратору
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label>Шаблон письма</label>
							<select class="form-control select_autosave" data-eid="{$event->getId()}" data-field="AdminLetterId">
								<option value="">не указан</option>
								{foreach from=$letters item=$l}
								<option value="{$l->getId()}" {if $event->getData()->getAdminLetterId() == $l->getId()}selected="selected"{/if}>{$l->getIdent()} ({$l->getDescription()})</option>
								{/foreach}
							</select>
						</div>
						<div class="form-group">
							<label>E-mail отправитель</label>
							<select class="form-control select_autosave" data-eid="{$event->getId()}" data-field="AdminAccountId">
								<option value="">По умолчанию {$defaultAccount}</option>
								{foreach from=$accounts item=$a}
								<option value="{$a->getId()}" {if $event->getData()->getAdminAccountId() == $a->getId()}selected="selected"{/if}>{$a->getIdent()} ({$a->getEmail()})</option>
								{/foreach}
							</select>
						</div>
						<div class="form-group">
							<label>E-mail получатель <small>(несколько через запятую)</small></label>
							<input class="form-control input_autosave" data-eid="{$event->getId()}" data-field="AdminEmailRecipient" value="{$event->getData()->getAdminEmailRecipient()}" />
						</div>
					</div>
					<hr class="m-t-0" />
					<div class="panel-heading">
						<div onclick="switcherEvent(this);" class="switcher {if $event->getData()->getAdminUseTelegram()}true{else}false{/if} pull-right"
							data-field="AdminUseTelegram"
							data-value="{if $event->getData()->getAdminUseTelegram()}1{else}0{/if}"
							data-id="{$event->getId()}">
						</div>
						Telegram Администратору
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label>Шаблон сообщения</label>
							<select class="form-control select_autosave" data-eid="{$event->getId()}" data-field="AdminTelegramTemplateId">
								<option value="">не указан</option>
								{foreach from=$templates item=$t}
								<option value="{$t->getId()}" {if $event->getData()->getAdminTelegramTemplateId() == $t->getId()}selected="selected"{/if}>{$t->getLabel()} ({$t->getDescription()})</option>
								{/foreach}
							</select>
						</div>
						<div class="form-group">
							<label>Telegram получатель <small>(несколько через запятую)</small></label>
							<input class="form-control input_autosave" data-eid="{$event->getId()}" data-field="AdminTelegramRecipient" value="{$event->getData()->getAdminTelegramRecipient()}" />
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="form-group">
							<label>Группа</label>
							<select class="form-control select_autosave" data-eid="{$event->getId()}" data-field="GroupId">
							{foreach from=$groups item=$g}
								<option {if $event->getGroupId() == $g->getId()}selected="selected"{/if} value="{$g->getId()}">{$g->getName()}</option>
							{/foreach}
							</select>
						</div>
						<div class="form-group">
							<label>Описание</label>
							<input class="form-control input_autosave" data-eid="{$event->getId()}" data-field="Description" value="{$event->getDescription()}" />
						</div>
					</div>
					<div class="panel-body">
						<div class="alert alert-{if $isValid->success()}success{else}danger{/if}" id="validInfo">{if $isValid->success()}{$isValid->getMessage()}{else}{$isValid->getError()}{/if}</div>
						<div class="alert alert-warning">
							{if 'recovery_admin' == $event->getIdent()}
								При восстановлении доступа в Админ-панель, следует использовать настройки для пользователя.<br />
								Но, при этом, можно установить дополнительные уведомления Администратору сайта. Это можно использовать в целях безопасности
								<br /><br />
							{/if}
							Инициализация:<br />
							<strong><em>
							Load::module('Notices');<br />
							$this-&gt;notices()-&gt;init('{$event->getIdent()}');<br />
							$this-&gt;notices()-&gt;exec();</em></strong>
						</div>
						<div class="form-group">
							<label>Комментарий</label>
							<textarea class="form-control input_autosave" rows="6" data-eid="{$event->getId()}" data-field="Comment">{$event->getComment()}</textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="alert alert-info">
			<strong>Внимание!!!</strong> Сохранение изменений происходит <strong>АВТОМАТИЧЕСКИ</strong>.
			Сохранение текстовых полей происходит через <strong>2 секунды</strong> после внесения изменений!
		</div>
	{/if}

{include file="../../_units/footer.tpl"}
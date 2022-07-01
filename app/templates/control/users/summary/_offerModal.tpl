<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title">
				Оффер
				<br>
				#{$summary->getUserId()} | {$summary->getUser()->getEmail()} | {$summary->getUser()->getFirstName()} {$summary->getUser()->getLastName()}
			</h4>
		</div>
		<div class="modal-body" id="offer-{$summary->getUserId()}">{$summary->getUser()->getFirstName()}, отправляю условия, которые согласовала для Вас!<br>
<br>
Сумма пополнения и бонусы:<br>
{foreach from=$lines item=$l}
{$l}<br>
{/foreach}
+ 1 курс в подарок из нашей академии<br>
<br>
Скажите до завтра выбранный вариант, я зафиксирую условия.<br>
С уважением, {$manager}.<br>
+74994503141</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Закрыть</button>
			<button type="button" class="btn btn-xs btn-info" onclick="offerToCB($('#offer-{$summary->getUserId()}').text());"><i class="fa fa-clipboard"></i> Скопировать в буфер</button>
		</div>
	</div>
</div>
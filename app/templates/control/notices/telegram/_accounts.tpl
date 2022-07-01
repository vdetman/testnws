<div class="row">
	<div class="col-lg-12">
		{if $accounts}
			<div class="table-responsive">
				<table class="table table-bordered table-hover" id="accounts-listing">
					<thead>
						<tr>
							<th>Дата / ChatId</th>
							<th>Аккаунт</th>
							<th>Пользователь</th>
							<th>Операции</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$accounts item='a'}
							<tr id="item_{$a->getId()}">
								<td class="main_data">
									<span class="add"><i class="fa fa-calendar"></i> {$a->getCreated()->format('d.m.Y H:i:s')}</span>
									<span class="add"><i class="fa fa-paper-plane-o"></i> {$a->getChatId()}</span>
								</td>
								<td class="account">
									<span class="add">@{$a->getUsername()}</span>
									<span class="add"><i class="fa fa-user"></i> {$a->getFirstName()} {$a->getLastName()}</span>
								</td>
								<td class="user">
									{if $a->getUser()}
										<span class="add"><i class="fa fa-user"></i> #{$a->getUserId()} <a href="/{ADMIN}/users/edit/{$a->getUserId()}" target="_blank">{$a->getUser()->getName()}</a></span>
										<span class="add"><i class="fa fa-envelope-o"></i> {$a->getUser()->getEmail()}</span>
									{/if}
								</td>
								<td class="operations">
									<i class="fa fa-times btn-drop" title="Удалить" onclick="deleteTelegramAccount('{$a->getId()}');"></i>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		{else}
			<h4>Не найдено ни одного элемента</h4>
		{/if}
	</div>
</div>
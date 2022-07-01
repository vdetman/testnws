<div class="row">
	<div class="col-lg-12">
		{if $preparings}
			<div class="table-responsive">
				<table class="table table-bordered table-hover" id="preparings-listing">
					<thead>
						<tr>
							<th>Дата / Код</th>
							<th>Пользователь</th>
							<th>Операции</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$preparings item='p'}
							<tr id="item_{$p->getUserId()}">
								<td class="main_data">
									<span class="add"><i class="fa fa-calendar"></i> {$p->getCreated()->format('d.m.Y H:i:s')}</span>
									<span class="add"><i class="fa fa-key"></i> {$p->getKey()}</span>
								</td>
								<td class="user">
									<span class="add"><i class="fa fa-user"></i> #{$p->getUserId()} <a href="/{ADMIN}/users/edit/{$p->getUserId()}" target="_blank">{$p->getUser()->getEmail()}</a></span>
									<span class="add"><i class="fa fa-envelope-o"></i> {$p->getUser()->getName()}</span>
								</td>
								<td class="operations">
									<i class="fa fa-times btn-drop" title="Удалить" onclick="deleteTelegramPreparing('{$p->getUserId()}');"></i>
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
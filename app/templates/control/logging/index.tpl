{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					{include file="./filter.tpl" filter=$filter}
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
				{if $logs}
					{$pagination}
					<table class="table table-bordered table-hover" id="logging_listing">
						<thead>
							<tr>
								<th>ID</th>
								<th></th>
								<th>URL</th>
								<th>Информация</th>
								<th>Данные</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$logs item=$log}
							<tr id="item_{$log->getId()}">
								<td class="id">
									{$log->getId()}
								</td>
								<td class="type">
									<img
										src="/admin/images/{if 'robot' == $log->getType()}pic_spider.png{else}{if $log->getUser()}pic_user.png{else}pic_human.png{/if}{/if}"
										title="{if 'robot' == $log->getType()}Робот{else}{if $log->getUser()}Пользователь{else}Гость{/if}{/if}"
									/>
								</td>
								<td class="url">
									<span class="created">
										{if $log->getCreated()->format('#Hd')}
											{$log->getCreated()->format('#Hd \в H:i')}
										{else}
											{$log->getCreated()->format('d #Hmg Y \в H:i')}
										{/if}
									</span>
									{if $log->getIsAjax()}
									<span class="ajax">AJAX</span>
									{/if}
									<br />
									{if $log->getComment()}
									<span class="comment"><span>Comment:</span> {$log->getComment()}</span><br />
									{/if}
									<span class="current">{$log->getUrl()}</span>
									{if $log->getRefererUrl()}
									<br />
									<span class="referer"><span>Referer:</span> {$log->getRefererUrl()}</span>
									{/if}
								</td>
								<td class="log_info">
									{if $log->getIp()}
									<span class="ip"><span>IP:</span> {$log->getIp()}</span>
									{/if}
									{if $log->getUser()}
										<br />
										<span class="user_name"><a href="/{ADMIN}/users/edit/{$log->getUser()->getId()}" target="_blank">{$log->getUser()->getName()}</a></span><br />
										<span class="user_email">{$log->getUser()->getEmail()}</span>
									{/if}
								</td>
								<td class="add_data">
									{if $log->getData()}
									<div class="data cont_view">
										<span>DATA</span>
										<div class="view">{$log->getData()}</div>
									</div>
									{/if}
									{if $log->getAgent()}
									<div class="agent cont_view">
										<span>AGENT</span>
										<div class="view">{$log->getAgent()}</div>
									</div>
									{/if}
								</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
					{$pagination}
				{else}
					<h4>Не найдено ни одного элемента</h4>
				{/if}
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}
{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-md-8">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#mainForm').submit();" class="btn btn-success btn-xs pull-left m-r-10"><i class="fa fa-save"></i> Сохранить</button>
					<a href="/{ADMIN}/{$module}{$backQuery}" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="mainForm">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-9">
											<label>Название <span class="text-danger">*</span></label>
											<input type="text" class="form-control" name="field[name]" value="{if isset($post.name)}{$post.name}{else}{$item->getName()}{/if}" />
										</div>
										<div class="col-lg-3">
											<label>Статус</label>
											<select class="form-control" name="field[status]">
												<option value="active" {if isset($post) && 'active' == $post.status || !isset($post) && 'active' == $item->getStatus()}selected="selected"{/if}>Активен</option>
												<option value="deleted" {if isset($post) && 'deleted' == $post.status || !isset($post) && 'deleted' == $item->getStatus()}selected="selected"{/if}>Удален</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-4">
								<div class="form-group">
									<label>Текущий владелец</label>
									<div class="email">
										<a href="/{ADMIN}/users/edit/{$item->getUser()->getId()}" title="Подробнее"><i class="fa fa-envelope"></i> #{$item->getUser()->getId()} {$item->getUser()->getEmail()}</a></div>
									<div class="name"><i class="fa fa-user"></i> {$item->getUser()->getName()}</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="form-group">
									<label>Новый владелец <span style="color:red;">*</span></label>
									<input class="form-control" name="field[user]" id="findUser" value="{if isset($post.user)}{$post.user}{/if}" />
									<input type="hidden" id="UserId" name="field[user_id]" value="{if isset($post.user_id)}{$post.user_id}{/if}" />
									<div id="ac_users"></div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<input type="hidden" name="save" value="1" />
								<button type="submit" class="btn btn-success btn-xs pull-left"><i class="fa fa-save"></i> Сохранить</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">
					Тестовые периоды
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form id="testPeriods" data-project="{$item->getId()}">
						{$periods=$item->getTestPeriods()}
						{foreach from=$modules item='module'}
							{$moduleId=$module->getId()}
							{$value = null}
							{if $periods.$moduleId}
								{$p=$periods.$moduleId}
								{$value = $p->getExpires()->format('Y-m-d')}
							{/if}
							<div class="row form-group">
								<label for="{$module->getLabel()}" class="col-md-4">{$module->getName()}</label>
								<div class="col-md-8">
									<div class="input-group">
										<input id="{$module->getLabel()}" class="form-control test-period" name="testPeriods[{$module->getId()}]" type="text" {if $value}value="{$value}"{/if} autocomplete="off">
									</div>
								</div>
							</div>
						{/foreach}
						<button type="submit" class="btn btn-success btn-xs pull-left"><i class="fa fa-save"></i> Сохранить</button>
					</form>
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}
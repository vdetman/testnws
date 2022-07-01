{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#mainForm').submit();" class="btn btn-success btn-xs pull-left"><i class="fa fa-save"></i> Сохранить</button>
					<a href="/{ADMIN}/{$module}" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="mainForm">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-4">
											<label>Имя</label>
											<input name="field[name]" class="form-control" value="{$partner->getName()}" />
										</div>
										<div class="col-lg-3">
											<label>Линк Сайта</label>
											<input name="field[site_link]" class="form-control" value="{$partner->getSiteLink()}" />
										</div>
										<div class="col-lg-3">
											<label>Имя Сайта</label>
											<input type="text" name="field[site_name]" class="form-control" value="{$partner->getSiteName()}" />
										</div>
										<div class="col-lg-2">
											<label>Статус</label>
											<select class="form-control" name="field[status]">
												<option value="active" {if 'active' == $partner->getStatus()}selected="selected"{/if}>Активен</option>
												<option value="hidden" {if 'hidden' == $partner->getStatus()}selected="selected"{/if}>Неактивен</option>
											</select>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-4">
											<label>Контактное имя</label>
											<input name="field[contact_name]" class="form-control" value="{$partner->getContactName()}" />
										</div>
										<div class="col-lg-4">
											<label>Контактный email</label>
											<input name="field[contact_email]" class="form-control" value="{$partner->getContactEmail()}" />
										</div>
										<div class="col-lg-4">
											<label>Контактный telegram</label>
											<input name="field[contact_telegram]" class="form-control" value="{$partner->getContactTelegram()}" />
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-12">
											<label>Сферы</label>
											<div>
												{foreach from=$spheres item=$s}
													<div class="checkbox-inline">
														<label class="cr-styled">
															<input type="checkbox" name="spheres[{$s->getId()}]" {if isset($selectedSpheres[$s->getId()])}checked="checked"{/if} value="{$s->getId()}" />
															<i class="fa"></i> {$s->getName()}
														</label>
													</div>
												{/foreach}
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Основная картинка</label>
											{include file="./_photo.tpl" partner=$partner}
										</div>
										<div class="col-lg-9">
											<div class="form-group">
												<div class="row">
													<div class="col-lg-6">
														<label>Телефон</label>
														<textarea class="form-control" rows="2" name="field[phone]">{$partner->getPhone()}</textarea>
													</div>
													<div class="col-lg-6">
														<label>Benefit</label>
														<textarea class="form-control" rows="2" name="field[benefit]">{$partner->getBenefit()}</textarea>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-12">
											<label>Description</label>
											<textarea class="form-control" rows="2" name="field[description]" id="Text">{$partner->getDescription()}</textarea>
										</div>
									</div>
								</div>
								<input type="hidden" name="save" value="1" />
								<button type="submit" class="btn btn-success btn-xs"><i class="fa fa-save"></i> Сохранить</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">$(document).ready(function(){ ckeditorInit('#Text', '{ADMIN}'); });</script>

{include file="../_units/footer.tpl"}

{include file="../../_units/header.tpl"}

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
										<div class="col-lg-3">
											<label>Язык</label>
											<input class="form-control" readonly="true" value="{$post->getLanguage()}" />
										</div>
										<div class="col-lg-5">
											<label>Описание</label>
											<input name="field[Name]" class="form-control" value="{$post->getName()}" />
										</div>
										<div class="col-lg-2">
											<label>Robots</label>
											<select class="form-control" name="field[Robots]">
												<option value="" {if !$post->getRobots()}selected="selected"{/if}></option>
												<option value="index, follow" {if 'index, follow' == $post->getRobots()}selected="selected"{/if}>index, follow</option>
												<option value="noindex, nofollow" {if 'noindex, nofollow' == $post->getRobots()}selected="selected"{/if}>noindex, nofollow</option>
											</select>
										</div>
										<div class="col-lg-2">
											<label>Статус</label>
											<select class="form-control" name="field[Status]">
												<option value="active" {if 'active' == $post->getStatus()}selected="selected"{/if}>Активен</option>
												<option value="hidden" {if 'hidden' == $post->getStatus()}selected="selected"{/if}>Неактивен</option>
											</select>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Alias</label>
											<input name="field[Alias]" class="form-control" value="{$post->getAlias()}" />
										</div>
										<div class="col-lg-5">
											<label>Title</label>
											<input name="field[Title]" class="form-control" value="{$post->getTitle()}" />
										</div>
										<div class="col-lg-4">
											<label>Header</label>
											<input name="field[Header]" class="form-control" value="{$post->getHeader()}" />
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Основная картинка</label>
											{include file="./_photo.tpl" post=$post}
										</div>
										<div class="col-lg-9">
											<div class="form-group">
												<div class="row">
													<div class="col-lg-6">
														<label>Keywords</label>
														<textarea class="form-control" rows="2" name="field[Keywords]">{$post->getKeywords()}</textarea>
													</div>
													<div class="col-lg-6">
														<label>Description</label>
														<textarea class="form-control" rows="2" name="field[Description]">{$post->getDescription()}</textarea>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-12">
											<label>Контент</label>
											<textarea class="form-control" rows="15" name="field[Text]" id="Text">{$post->getText()}</textarea>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-9">
											<label>Вступительный текст</label>
											<input name="field[Preview]" class="form-control" value="{$post->getPreview()}" />
										</div>
										<div class="col-lg-3">
											<label>Теги</label>
											<input name="field[Tags]" class="form-control" value="{$post->getTags()}" />
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

{include file="../../_units/footer.tpl"}
{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#mainForm').submit();" class="btn btn-success btn-xs pull-left"><i class="fa fa-save"></i> Сохранить</button>
					<a href="/{ADMIN}/{$module}/tariffs/{$course->getId()}" class="btn btn-info btn-xs pull-left m-l-15"><i class="fa fa-money"></i> Тарифы курса ({count($tariffs)})</a>
					<a href="/{ADMIN}/{$module}/lessons/{$course->getId()}" class="btn btn-primary btn-xs pull-left m-l-15"><i class="fa fa-info"></i> Уроки курса ({count($lessons)})</a>
					<a href="/{ADMIN}/{$module}/reviews/{$course->getId()}" class="btn btn-default btn-xs pull-left m-l-15"><i class="fa fa-comment"></i> Отзывы курса ({count($reviews)})</a>
					<a href="/{ADMIN}/{$module}" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="mainForm">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-6">
											<label>Название</label>
											<input name="field[name]" class="form-control" value="{$course->getName()}" />
										</div>
										<div class="col-lg-3">
											<label>Бесплатный курс</label>
											<select class="form-control" name="field[is_free]">
												<option value="1" {if $course->getIsFree()}selected="selected"{/if}>Да</option>
												<option value="0" {if !$course->getIsFree()}selected="selected"{/if}>Нет</option>
											</select>
										</div>
										<div class="col-lg-3">
											<label>Статус</label>
											<select class="form-control" name="field[status]">
												<option value="active" {if 'active' == $course->getStatus()}selected="selected"{/if}>Активен</option>
												<option value="hidden" {if 'hidden' == $course->getStatus()}selected="selected"{/if}>Скрытый</option>
											</select>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-12">
											<label>Фотографии</label>
											{include file="./_photo.tpl" course=$course}
										</div>
									</div>
								</div>
									
								<div class="form-group">
									<div class="row">
										<div class="col-lg-4">
											<label>promo_title</label>
											<textarea class="form-control" name="field[promo_title]" rows="10">{if isset($post)}{$post.promo_title}{else}{$course->getPromoTitle()}{/if}</textarea>
										</div>
										<div class="col-lg-4">
											<label>promo_description</label>
											<textarea class="form-control" name="field[promo_description]" rows="10">{if isset($post)}{$post.promo_description}{else}{$course->getPromoDescription()}{/if}</textarea>
										</div>
										<div class="col-lg-4">
											<label>description</label>
											<textarea class="form-control" name="field[description]" rows="10">{if isset($post)}{$post.description}{else}{$course->getDescription()}{/if}</textarea>
										</div>
									</div>
								</div>
									
								<div class="form-group">
									<div class="row">
										<div class="col-lg-6">
											<label>html_paid</label>
											<textarea class="form-control" name="field[html_paid]" rows="20" >{if isset($post)}{$post.html_paid}{else}{$course->getHtmlPaid()}{/if}</textarea>
										</div>
										<div class="col-lg-6">
											<label>html_unpaid</label>
											<textarea class="form-control" name="field[html_unpaid]" rows="20" >{if isset($post)}{$post.html_unpaid}{else}{$course->getHtmlUnpaid()}{/if}</textarea>
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
	<script type="text/javascript">
		$(document).ready(function(){ ckeditorInit('.textarea-editor', '{ADMIN}'); });
	</script>

{include file="../_units/footer.tpl"}

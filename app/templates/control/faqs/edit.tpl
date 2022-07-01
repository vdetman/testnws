{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#primaryForm').submit();" class="btn btn-success btn-xs pull-left"><i class="fa fa-save"></i> Сохранить</button>
					<a href="/{ADMIN}/{$module}" class="btn btn-primary btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="primaryForm">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Идентификатор <span style="color:red;">*</span></label>
											<input class="form-control" readonly="" value="{$faq->getIdent()}" />
										</div>
										<div class="col-lg-7">
											<label>Название </label>
											<input name="field[Name]" class="form-control" value="{if isset($post)}{$post.Name}{else}{$faq->getName()}{/if}" />
										</div>
										<div class="col-lg-2">
											<label>Активность</label>
											<select class="form-control" name="field[Status]">
												<option value="active" {if isset($post) && 'active' == $post.Status || !isset($post) && 'active' == $faq->getStatus()}selected="selected"{/if}>Да</option>
												<option value="hidden" {if isset($post) && 'hidden' == $post.Status || !isset($post) && 'hidden' == $faq->getStatus()}selected="selected"{/if}>Нет</option>
											</select>
										</div>
									</div>
								</div>
								<input type="hidden" name="save" value="1" />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
											
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title pull-left">Элементы</h3>
					<button class="btn btn-xs btn-effect-ripple btn-info pull-right" onclick="createItem({$faq->getId()});"><i class="fa fa-plus"></i> Добавить элемент</button>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered table-hover" id="listing">
							<thead>
								<tr>
									<th colspan="2">Контент</th>
									<th>Видео</th>
									<th>Операции</th>
								</tr>
							</thead>
							<tbody id="items">
							{foreach from=$faq->getItems() item=$item}
								{include file="./_item.tpl" item=$item}
							{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}
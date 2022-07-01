{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#mainForm').submit();" class="btn btn-success btn-xs pull-left m-r-10"><i class="fa fa-save"></i> Сохранить</button>
					<a href="/{ADMIN}/finance/settings" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
					<button onclick="deleteItem({$item->getId()});" class="btn btn-danger btn-xs pull-right m-r-10"><i class="fa fa-times"></i> Удалить</button>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="mainForm">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Label</label>
											<input name="field[label]" class="form-control" value="{$item->getLabel()}" />
										</div>
										<div class="col-lg-9">
											<label>Описание</label>
											<input name="field[description]" class="form-control" value="{if isset($post)}{$post.description}{else}{$item->getDescription()}{/if}" />
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-12">
											<label>Значение</label>
											<textarea name="field[value]" class="form-control">{if isset($post)}{$post.value}{else}{$item->getValue()}{/if}</textarea>
										</div>
									</div>
								</div>
								<input type="hidden" name="save" value="1" />
								
								<div class="row">
									<div class="col-lg-12">
										<button type="submit" class="btn btn-success btn-xs pull-left"><i class="fa fa-save"></i> Сохранить</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}

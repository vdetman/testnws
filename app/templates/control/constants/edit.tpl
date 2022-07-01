{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#mainForm').submit();" class="btn btn-success btn-xs pull-left m-r-10"><i class="fa fa-save"></i> Сохранить</button>
					<a href="/{ADMIN}/{$module}{$backQuery}" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
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
											<label>Идентификатор</label>
											<input class="form-control" readonly="true" value="{$item->getName()}" />
										</div>
										<div class="col-lg-9">
											<label>Описание</label>
											<input name="field[Description]" class="form-control" value="{if isset($post)}{$post.Description}{else}{$item->getDescription()}{/if}" />
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Тип данных</label>
											<select class="form-control" name="field[Type]">
												<option value="bool" {if 'bool'==$item->getType()}selected="selected"{/if}>Бинарный</option>
												<option value="string" {if 'string'==$item->getType()}selected="selected"{/if}>Строковый</option>
												<option value="int" {if 'int'==$item->getType()}selected="selected"{/if}>Целочисленный</option>
												<option value="float" {if 'float'==$item->getType()}selected="selected"{/if}>Числовой</option>
											</select>
										</div>
										<div class="col-lg-9">
											<label>Значение</label>
											<textarea name="field[Value]" class="form-control">{if isset($post)}{$post.Value}{else}{$item->getValue()}{/if}</textarea>
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
{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-md-12">
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
										<div class="col-lg-12">
											<label>ApiKey V1</label>
											<input type="text" class="form-control" name="field[api_key]" value="{if isset($post.api_key)}{$post.api_key}{else}{$item->getKeys()->getApiKey()}{/if}" />
										</div>
									</div>
									<div class="row">
										<div class="col-lg-12">
											<label>ApiKey V2</label>
											<input type="text" class="form-control" name="field[api_key_v2]" value="{if isset($post.api_key_v2)}{$post.api_key_v2}{else}{$item->getKeys()->getApiKeyV2()}{/if}" />
										</div>
										{*<div class="col-lg-6">
											<label>Блокировки</label><br/>
											<label class="cr-styled"><input type="checkbox" name="field[Blocked][]" {if in_array('all', $blocked)}checked=""{/if} value="all" /><i class="fa"></i> All</label>
											<label class="cr-styled"><input type="checkbox" name="field[Blocked][]" {if in_array('orders', $blocked)}checked=""{/if} value="orders" /><i class="fa"></i> Orders</label>
											<label class="cr-styled"><input type="checkbox" name="field[Blocked][]" {if in_array('sales', $blocked)}checked=""{/if} value="sales" /><i class="fa"></i> Sales</label>
											<label class="cr-styled"><input type="checkbox" name="field[Blocked][]" {if in_array('stocks', $blocked)}checked=""{/if} value="stocks" /><i class="fa"></i> Stocks</label>
										</div>*}
									</div>
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
	</div>

{include file="../_units/footer.tpl"}
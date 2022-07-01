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
										<div class="col-lg-2">
											<label>ClientId</label>
											<input disabled type="text" class="form-control" name="" value="{if isset($post.ClientId)}{$post.ClientId}{else}{$item->getClientId()}{/if}" />
										</div>
										<div class="col-lg-4">
											<label>ApiKey</label>
											<input type="text" class="form-control" name="field[api_key]" value="{if isset($post.ApiKey)}{$post.ApiKey}{else}{$item->getApiKey()}{/if}" />
										</div>
										<div class="col-lg-6">
											<label>Блокировки</label><br/>
											<label class="cr-styled"><input type="checkbox" name="field[blocked][]" {if in_array('all', $blocked)}checked=""{/if} value="all" /><i class="fa"></i> All</label>
											<label class="cr-styled"><input type="checkbox" name="field[blocked][]" {if in_array('orders', $blocked)}checked=""{/if} value="orders" /><i class="fa"></i> Orders</label>
											<label class="cr-styled"><input type="checkbox" name="field[blocked][]" {if in_array('stocks', $blocked)}checked=""{/if} value="stocks" /><i class="fa"></i> Stocks</label>
											<label class="cr-styled"><input type="checkbox" name="field[blocked][]" {if in_array('products', $blocked)}checked=""{/if} value="products" /><i class="fa"></i> Products</label>
											<label class="cr-styled"><input type="checkbox" name="field[blocked][]" {if in_array('prices', $blocked)}checked=""{/if} value="prices" /><i class="fa"></i> Prices</label>
										</div>
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
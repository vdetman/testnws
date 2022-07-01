{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form role="form" action="" method="post" enctype="multipart/form-data">
						<input type="file" name="file" accept=".xls*" class="pull-left" />
						<input type="submit" class="btn btn-primary btn-xs" value="Загрузить" />
					</form>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					{if $result->getMessage() || $result->getError()}
						<div class="alert {if $result->success()}alert-success{else}alert-danger{/if}">
							{if $result->getError()}{$result->getError()}{else}{$result->getMessage()}{/if}
						</div>
					{/if}
					{if $processImport->getMessage() || $processImport->getError()}
						<div class="alert {if $processImport->success()}alert-success{else}alert-danger{/if}">
							{if $processImport->getError()}{$processImport->getError()}{else}{$processImport->getMessage()}{/if}
						</div>
					{/if}
					{if $result->success()}
						{*<pre>{print_r($DATA, 1)}</pre>*}
						<form role="form2"  action="" method="post">
							<div class="row">
								<div class="col-lg-12">
									<button type="submit" class="btn btn-success btn-xs">Импортировать</button>
								</div>
							</div>
							<table class="table table-bordered table-hover" id="import_listing">
								<tbody>
								{foreach from=$DATA key=$key item=$d name='row'}
									{if 1 == $smarty.foreach.row.iteration}
										<tr>
											<th class="th-checkbox">
												<input type="checkbox" id="th-confirm" checked="" onclick="$('.td-confirm').prop('checked', $(this).is(':checked'));" />
											</th>
											{foreach from=$d item=$h}
											<th>{$h}</th>
											{/foreach}
										</tr>
									{else}
										<tr>
											<th class="td-checkbox">
												<input type="checkbox" class="td-confirm" name="comfirm[{$key}]" checked="" value="1" />
											</th>
											{foreach from=$d item=$c name='cell'}
												<td class="value {if $c.update && $c.value}updated{/if} {if $c.exist}exist{/if}">
													{if $c.oldValue}<div class="old"><del>{$c.oldValue}</del></div>{/if}
													{if $c.value}<div class="new">{$c.value}</div>{/if}
													{if $c.update && $c.value}
														<input type="hidden" name="proxy[{$key}][{$c.field}]" value="{$c.value}" />
													{/if}
												</td>
											{/foreach}
										</tr>
									{/if}
								{/foreach}
								</tbody>
							</table>
							<input type="hidden" name="importSave" value="1" />
							<div class="row">
								<div class="col-lg-12">
									<button type="submit" class="btn btn-success btn-xs">Импортировать</button>
								</div>
							</div>
						</form>
					{/if}
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}
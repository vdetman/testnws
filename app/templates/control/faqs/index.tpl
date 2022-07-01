{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button class="btn btn-xs btn-effect-ripple btn-info pull-right" onclick="createFaq();"><i class="fa fa-plus"></i> Новый блок</button>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered table-hover" id="faqs_listing">
							<thead>
								<tr>
									<th colspan="2">Информация</th>
									<th>Операции</th>
								</tr>
							</thead>
							<tbody>
							{foreach from=$faqs item=$faq}
								{include file="./_faq.tpl" faq=$faq}
							{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}
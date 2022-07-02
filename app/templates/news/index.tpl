{include file='../_units/libraries.tpl'}
{include file='../_units/header.tpl'}

<div class="row" style="width: 100%;">
	<div class="pull-left pt-3 pb-3 bg-white" style="width: 280px;">
		
		<div class="bd-example-snippet bd-code-snippet">
			<div class="bd-example">
				<div class="list-group">
					<div class="list-group-item">
						<input class="form-control form-control-sm autosubmit" id="search" value="{$search}" type="text" placeholder="поиск по тексту">
					</div>
					<div id="rubricsList">
						{include file='./_rubricsList.tpl'}
					</div>
					<div class="p-1">
						<button class="btn btn-xs btn-success" onclick="newItemModal();">+ добавить новость</button>
						{*<button class="btn btn-xs btn-danger" onclick="refreshTree();">refresh</button>*}
					</div>
				</div>
			</div>
		</div>
	</div>
			
	<div class="row pull-right pt-3 pb-3 bg-white" style="width: calc(100% - 280px);">
		<div class="row" id="newsList">
			{foreach from=$newsList item=$news}
				{include file="./_item.tpl" news=$news}
			{/foreach}
		</div>
		<div class="text-center" id="loader" style="display: none;">
			<div class="spinner-border text-primary"><span class="visually-hidden">Loading...</span></div>
		</div>
	</div>
	
</div>

<input type="hidden" id="filter" value='{$filter}' />
<input type="hidden" id="loaded" value='{$loaded}' />
<input type="hidden" id="total" value='{$total}' />

<div id="modalContainer" class="modal fade" aria-hidden="true" style="display: none;"></div>

{include file='../_units/footer.tpl'}
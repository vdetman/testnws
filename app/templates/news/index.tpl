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
					{function name=rubrics level=level}
						{foreach from=$data item=$item}
							<a href="/news?rubric={$item->getRubric()->getId()}" style="padding-left: {$level * 12 + 16}px;" class="list-group-item list-group-item-action {if $currentRubric == $item->getRubric()->getId()}list-group-item-info{/if}">
								{if $level}&#8627;{/if}
								{$item->getRubric()->getName()}
								<small>({$item->getItemsCount()})</small>
							</a>
							{if $item->getChilds()}
								{call name=rubrics data=$item->getChilds() level=$level+1}
							{/if}
						{/foreach}
					{/function}
					{call name=rubrics data=$rubricTree->getChilds() level=0}
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
{include file='../_units/footer.tpl'}
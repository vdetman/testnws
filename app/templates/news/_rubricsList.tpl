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
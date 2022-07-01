<ul class="pagination">
	{strip}
	{foreach from=$items item=$item}
		{if !$item.current}
			<a href="{$item.href}">
				<li class="page_item" title="{$item.title}">{$item.name}</li>
			</a>
		{else}
			<li class="page_item current" title="{$item.title}">{$item.name}</li>
		{/if}
	{/foreach}
	{/strip}
</ul>
<div id="dropzone"></div>
<ul class="row" id="files">
	{foreach from=$lesson->getFiles() item=$f}
		{include file='./_file_item.tpl' f=$f}
	{/foreach}
</ul>
<div class="clearfix"></div>
<input type="hidden" id="ItemId" value="{$lesson->getId()}" />
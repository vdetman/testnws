<ul class="nav nav-tabs">
{foreach from=$languages key=$iso item=$lang name=lang}
	<li class="{if $smarty.foreach.lang.first}active{/if}">
		<a href="#{$template->getId()}{$iso}" data-toggle="tab" aria-expanded="{if $smarty.foreach.lang.first}true{else}false{/if}">
			<span class="visible-xs"><i class="fa fa-home"></i></span>
			<span class="hidden-xs">{$lang}</span>
		</a>
	</li>
{/foreach}
</ul>
<div class="tab-content">
{foreach from=$languages key=$iso item=$lang name=lang}
	{assign var='d' value=$template->getData($iso)}
	<div class="tab-pane {if $smarty.foreach.lang.first}active{/if}" id="{$template->getId()}{$iso}">
		<div class="form-group">
			<div class="row">
				<div class="col-lg-12">
					<label>Тело письма</label>
					<textarea class="form-control" name="data[{$iso}][Message]">{$d->getMessage()}</textarea>
				</div>
			</div>
		</div>
	</div>
{/foreach}
</div>
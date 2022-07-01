<tr id="item_{$item->getId()}">
	<td class="sort_handle"><i class="fa fa-navicon"></i></td>
	<td class="main_data">
		<ul class="nav nav-tabs">
		{foreach from=$languages item=$l name=lang}
			<li class="{if $smarty.foreach.lang.first}active{/if}">
				<a href="#{$item->getId()}{$l->getIso()}" data-toggle="tab" aria-expanded="{if $smarty.foreach.lang.first}true{else}false{/if}">
					<span class="visible-xs"><i class="fa fa-home"></i></span>
					<span class="hidden-xs">{$l->getName()}</span>
				</a>
			</li>
		{/foreach}
		</ul>
		<div class="tab-content">
		{foreach from=$languages item=$l name=lang}
			<div class="tab-pane {if $smarty.foreach.lang.first}active{/if}" id="{$item->getId()}{$l->getIso()}"> 
				<div class="input-group">
					<span class="input-group-addon" title="Вопрос">
						<i class="fa fa-header"></i>
					</span>
					<input type="text" value="{$item->getData($l->getIso())->getQuestion()}" data-id="{$item->getId()}" data-field="Question" data-lang="{$l->getIso()}" class="autosave form-control input-sm" />
				</div>
				<div class="input-group">
					<span class="input-group-addon" title="Код YouTube">
						<i class="fa fa-youtube"></i>
					</span>
					<input type="text" value="{$item->getData($l->getIso())->getVideoCode()}" data-id="{$item->getId()}" data-field="VideoCode" data-lang="{$l->getIso()}" class="autosave form-control input-sm" />
				</div>
				<textarea class="autosave form-control" data-id="{$item->getId()}" data-field="Answer" data-lang="{$l->getIso()}" placeholder="Ответ">{$item->getData($l->getIso())->getAnswer()}</textarea>
			</div>
		{/foreach}
		</div>
	</td>
	<td class="video">
		<div onclick="switcherItem(this);" class="switcher {if $item->getIsVideo()}true{else}false{/if}"
			data-field="IsVideo"
			data-value="{if $item->getIsVideo()}1{else}0{/if}"
			data-id="{$item->getId()}">
		</div>
	</td>
	<td class="operations">
		<div onclick="switcherItem(this);" class="switcher {if 'active' == $item->getStatus()}true{else}false{/if}"
			data-field="Status"
			data-value="{if 'active' == $item->getStatus()}1{else}0{/if}"
			data-id="{$item->getId()}">
		</div>
		<i class="fa fa-times btn-drop" title="Удалить" onclick="deleteItem('{$item->getId()}');"></i>
	</td>
</tr>
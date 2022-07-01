<tr id="item_{$faq->getId()}">
	<td class="sort_handle"><i class="fa fa-navicon"></i></td>
	<td class="main_data">
		<div class="ident"><a href="/{ADMIN}/{$module}/edit/{$faq->getId()}" title="Редактировать">{$faq->getIdent()}</a></div>
		<div class="add">
			<i class="fa fa-list" title="Вопросы"></i>
			{if $faq->getItems()}
				{count($faq->getItems())}
				{Helper\Text::getEnding(count($faq->getItems()), 'вопрос', 'вопроса', 'вопросов')}
			{else}
				пусто
			{/if}
		</div>
		<div class="name">{$faq->getName()}</div>
	</td>
	<td class="operations">
		<div onclick="switcherFaq(this);" class="switcher {if 'active' == $faq->getStatus()}true{else}false{/if}"
			data-field="Status"
			data-value="{if 'active' == $faq->getStatus()}1{else}0{/if}"
			data-id="{$faq->getId()}">
		</div>
		<a href="/{ADMIN}/{$module}/edit/{$faq->getId()}" title="Редактировать"><i class="fa fa-edit btn-edit"></i></a>
		<i class="fa fa-times btn-drop" title="Удалить" onclick="deleteFaq('{$faq->getId()}');"></i>
	</td>
</tr>
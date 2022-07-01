<div id="extension_filter">
	<span class="exf_btn text-primary m-b-5 pointer">расширенный фильтр</span>
	<div id="exf_body">
		<div class="row m-b-10">
			<div class="col-lg-2 range">
				<label>Баланс</label>
				<input name="balance_min" class="form-control min" value="{$filter->get('balance_min')}" placeholder="от" />
				<input name="balance_max" class="form-control max" value="{$filter->get('balance_max')}" placeholder="до" />
				<div class="clearfix"></div>
			</div>
			<div class="col-lg-2 range">
				<label>Хватит на</label>
				<input name="enough_days_min" class="form-control min" value="{$filter->get('enough_days_min')}" placeholder="от" />
				<input name="enough_days_max" class="form-control max" value="{$filter->get('enough_days_max')}" placeholder="до" />
				<div class="clearfix"></div>
			</div>
			<div class="col-lg-2 range">
				<label>Сумма пополнений</label>
				<input name="refills_min" class="form-control min" value="{$filter->get('refills_min')}" placeholder="от" />
				<input name="refills_max" class="form-control max" value="{$filter->get('refills_max')}" placeholder="до" />
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="row">
			{assign var='mods' value=$filter->get('modules')}
			<div class="col-lg-10">
				{if isset($mods[1])}
					{assign var='mW' value=$mods[1]}
				{/if}
				<label>Модули Wildberries</label>
				<label class="cr-styled autosubmit"><input type="checkbox" {if isset($mW) && in_array(MODULE_ANALYTICS_ID, $mW)}checked=""{/if} name="modules[{MARKETPLACE_ID_WILDBERRIES}][]" value="{MODULE_ANALYTICS_ID}"><i class="fa"></i> API</label>
				<label class="cr-styled autosubmit"><input type="checkbox" {if isset($mW) && in_array(MODULE_COMPETITORS_ID, $mW)}checked=""{/if} name="modules[{MARKETPLACE_ID_WILDBERRIES}][]" value="{MODULE_COMPETITORS_ID}"><i class="fa"></i> Конкуренты</label>
				<label class="cr-styled autosubmit"><input type="checkbox" {if isset($mW) && in_array(MODULE_ANALYSIS_ID, $mW)}checked=""{/if} name="modules[{MARKETPLACE_ID_WILDBERRIES}][]" value="{MODULE_ANALYSIS_ID}"><i class="fa"></i> Анализ товара</label>
				<label class="cr-styled autosubmit"><input type="checkbox" {if isset($mW) && in_array(MODULE_POSITIONS_ID, $mW)}checked=""{/if} name="modules[{MARKETPLACE_ID_WILDBERRIES}][]" value="{MODULE_POSITIONS_ID}"><i class="fa"></i> Позиции</label>
				<label class="cr-styled autosubmit"><input type="checkbox" {if isset($mW) && in_array(MODULE_EXTENSION_ID, $mW)}checked=""{/if} name="modules[{MARKETPLACE_ID_WILDBERRIES}][]" value="{MODULE_EXTENSION_ID}"><i class="fa"></i> Расширение</label>
				<label class="cr-styled autosubmit"><input type="checkbox" {if isset($mW) && in_array(MODULE_PRICE_EDITOR_ID, $mW)}checked=""{/if} name="modules[{MARKETPLACE_ID_WILDBERRIES}][]" value="{MODULE_PRICE_EDITOR_ID}"><i class="fa"></i> Редактор цен</label>
				<label class="cr-styled autosubmit"><input type="checkbox" {if isset($mW) && in_array(MODULE_CARD_EDITOR_ID, $mW)}checked=""{/if} name="modules[{MARKETPLACE_ID_WILDBERRIES}][]" value="{MODULE_CARD_EDITOR_ID}"><i class="fa"></i> Редактор карточки товара</label>
				<label class="cr-styled autosubmit"><input type="checkbox" {if isset($mW) && in_array(MODULE_SALES_ANALYSIS_ID, $mW)}checked=""{/if} name="modules[{MARKETPLACE_ID_WILDBERRIES}][]" value="{MODULE_SALES_ANALYSIS_ID}"><i class="fa"></i> Анализ продаж</label>
				<label class="cr-styled autosubmit"><input type="checkbox" {if isset($mW) && in_array(MODULE_ASSISTANT_ID, $mW)}checked=""{/if} name="modules[{MARKETPLACE_ID_WILDBERRIES}][]" value="{MODULE_ASSISTANT_ID}"><i class="fa"></i> Ассистент</label>
				<div class="clearfix"></div>
			</div>
			<div class="col-lg-2">
				{if isset($mods[2])}
					{assign var='mO' value=$mods[2]}
				{/if}
				<label>Модули Ozon</label>
				<label class="cr-styled autosubmit"><input type="checkbox" {if isset($mO) && in_array(MODULE_ANALYTICS_ID, $mO)}checked=""{/if} name="modules[{MARKETPLACE_ID_OZON}][]" value="{MODULE_ANALYTICS_ID}"><i class="fa"></i> API</label>
				<label class="cr-styled autosubmit"><input type="checkbox" {if isset($mO) && in_array(MODULE_POSITIONS_ID, $mO)}checked=""{/if} name="modules[{MARKETPLACE_ID_OZON}][]" value="{MODULE_POSITIONS_ID}"><i class="fa"></i> Позиции</label>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
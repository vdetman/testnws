<form action="" method="get">
	<div class="filter_form col-lg-10">
		<div class="row">
			<div class="col-lg-2">
				<label>Сортировка</label>
				<select class="form-control autosubmit" name="order">
					<option value="id_desc" {if 'id_desc' == $filter->get('order')}selected="selected"{/if}>ID &darr;</option>
					<option value="id_asc" {if 'id_asc' == $filter->get('order')}selected="selected"{/if}>ID &uparrow;</option>
					<option value="balance_desc" {if 'balance_desc' == $filter->get('order')}selected="selected"{/if}>Баланс &darr;</option>
					<option value="balance_asc" {if 'balance_asc' == $filter->get('order')}selected="selected"{/if}>Баланс &uparrow;</option>
					<option value="daily_amount_desc" {if 'daily_amount_desc' == $filter->get('order')}selected="selected"{/if}>Размер суточного списания &darr;</option>
					<option value="daily_amount_asc" {if 'daily_amount_asc' == $filter->get('order')}selected="selected"{/if}>Размер суточного списания &uparrow;</option>
					<option value="enough_days_desc" {if 'enough_days_desc' == $filter->get('order')}selected="selected"{/if}>Хватит на &darr;</option>
					<option value="enough_days_asc" {if 'enough_days_asc' == $filter->get('order')}selected="selected"{/if}>Хватит на &uparrow;</option>
					<option value="refills_desc" {if 'refills_desc' == $filter->get('order')}selected="selected"{/if}>Сумма пополнений &darr;</option>
					<option value="refills_asc" {if 'refills_asc' == $filter->get('order')}selected="selected"{/if}>Сумма пополнений &uparrow;</option>
					<option value="bonuses_desc" {if 'bonuses_desc' == $filter->get('order')}selected="selected"{/if}>Сумма бонусов &darr;</option>
					<option value="bonuses_asc" {if 'bonuses_asc' == $filter->get('order')}selected="selected"{/if}>Сумма бонусов &uparrow;</option>
					<option value="last_refill_desc" {if 'last_refill_desc' == $filter->get('order')}selected="selected"{/if}>Дата последнего пополнения &darr;</option>
					<option value="last_refill_asc" {if 'last_refill_asc' == $filter->get('order')}selected="selected"{/if}>Дата последнего пополнения &uparrow;</option>
					<option value="last_costed_desc" {if 'last_costed_desc' == $filter->get('order')}selected="selected"{/if}>Дата последнего списания &darr;</option>
					<option value="last_costed_asc" {if 'last_costed_asc' == $filter->get('order')}selected="selected"{/if}>Дата последнего списания &uparrow;</option>
				</select>
			</div>
			<div class="col-lg-2">
				<label>Статус</label>
				<select class="form-control autosubmit" name="status_id">
					<option value="all">все</option>
					{foreach from=$filter->get('statuses') item=$s}
					<option value="{$s->getId()}" {if $filter->get('status_id') == $s->getId()}selected="selected"{/if}>{$s->getName()}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-2">
				<label>Менеджер</label>
				<select class="form-control autosubmit" name="manager_id">
					<option value="all">Все</option>
					{if $managers}
						{foreach from=$managers item=$manager}
							<option value="{$manager->getId()}" {if {$manager->getId()} == $filter->get('manager_id')}selected="selected"{/if}>{$manager->getName()}</option>
						{/foreach}
					{/if}
				</select>
			</div>
			<div class="col-lg-4">
				<label>Найти</label>
				<input name="search" class="form-control" value="{$filter->get('search')}" />
			</div>
			<div class="col-lg-2">
				<label>{$filter->getTotal()} {$filter->getUnits()}</label>
				<button type="submit" class="btn btn-info btn-xs">применить</button>
				<button onclick="resetFilter(); return false;" class="btn btn-default btn-xs">сброс</button>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				{include file="./_ext_filter.tpl" filter=$filter}
			</div>
		</div>
	</div>
	
	<input name="tmp" type="hidden" value="no" />
</form>
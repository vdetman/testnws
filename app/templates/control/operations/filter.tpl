<form action="" method="get">
	<div class="filter_form col-lg-9">
		<div class="row m-b-5">
			<div class="col-lg-1">
				<label>Сортировка</label>
				<select class="form-control autosubmit" name="order">
					<option value="id_desc" {if 'id_desc' == $filter->get('order')}selected="selected"{/if}>ID &darr;</option>
					<option value="id_asc" {if 'id_asc' == $filter->get('order')}selected="selected"{/if}>ID &uparrow;</option>
					<option value="amount_desc" {if 'amount_desc' == $filter->get('order')}selected="selected"{/if}>Сумма &darr;</option>
					<option value="amount_asc" {if 'amount_asc' == $filter->get('order')}selected="selected"{/if}>Сумма &uparrow;</option>
				</select>
			</div>
			<div class="col-lg-2">
				<label>Тип</label>
				<select class="form-control autosubmit" name="type_id">
					<option value="all">Все</option>
					<option value="1" {if 1 == $filter->get('type_id')}selected="selected"{/if}>Пополнение счета</option>
					<option value="2" {if 2 == $filter->get('type_id')}selected="selected"{/if}>Возврат средств</option>
					<option value="3" {if 3 == $filter->get('type_id')}selected="selected"{/if}>Списание по тарифу</option>
					<option value="4" {if 4 == $filter->get('type_id')}selected="selected"{/if}>Комиссия сервиса</option>
					<option value="5" {if 5 == $filter->get('type_id')}selected="selected"{/if}>Бонус</option>
					<option value="6" {if 6 == $filter->get('type_id')}selected="selected"{/if}>Бонус партнера</option>
					<option value="7" {if 7 == $filter->get('type_id')}selected="selected"{/if}>Оплата услуги</option>
					<option value="8" {if 8 == $filter->get('type_id')}selected="selected"{/if}>Вывод средств</option>
					<option value="9" {if 9 == $filter->get('type_id')}selected="selected"{/if}>Разовое списание</option>
					<option value="10" {if 10 == $filter->get('type_id')}selected="selected"{/if}>Оплата курса</option>
				</select>
			</div>
			<div class="col-lg-2">
				<label>Статус</label>
				<select class="form-control autosubmit" name="status">
					<option value="all" {if 'all' == $filter->get('status')}selected="selected"{/if}>все</option>
					<option value="new" {if 'new' == $filter->get('status')}selected="selected"{/if}>новая</option>
					<option value="locked" {if 'locked' == $filter->get('status')}selected="selected"{/if}>в ожидании</option>
					<option value="success" {if 'success' == $filter->get('status')}selected="selected"{/if}>оплачена</option>
					<option value="canceled" {if 'canceled' == $filter->get('status')}selected="selected"{/if}>отменена</option>
					<option value="error" {if 'error' == $filter->get('status')}selected="selected"{/if}>ошибка</option>
				</select>
			</div>
			<div class="col-lg-1">
				<label>Тип оплаты</label>
				<select class="form-control autosubmit" name="is_first_refill">
					<option value="all" {if 'all' == $filter->get('is_first_refill')}selected="selected"{/if}>все</option>
					<option value="true" {if 'true' == $filter->get('is_first_refill')}selected="selected"{/if}>Первичная</option>
					<option value="false" {if 'false' == $filter->get('is_first_refill')}selected="selected"{/if}>Повторная</option>
				</select>
			</div>
			<div class="col-lg-2">
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
			<div class="col-lg-2 period">
				<label>Период</label>
				{if $filter->has('since') || $filter->has('until')}
				<span class="erase" onclick="clearPeriod();">очистить</span>
				{/if}
				<br />
				<input name="since" class="form-control min" readonly="" value="{$filter->get('since')}" placeholder="с" />
				<input name="until" class="form-control max" readonly="" value="{$filter->get('until')}" placeholder="по" />
				<div class="clearfix"></div>
			</div>
			<div class="col-lg-2 amount">
				<label>Сумма</label>
				{if $filter->has('amount_min') || $filter->has('amount_max')}
				<span class="erase" onclick="clearAmount();">очистить</span>
				{/if}
				<br />
				<input name="amount_min" class="form-control min only_number" value="{$filter->get('amount_min')}" placeholder="с" />
				<input name="amount_max" class="form-control max only_number" value="{$filter->get('amount_max')}" placeholder="по" />
				<div class="clearfix"></div>
			</div>
			<div class="col-lg-1">
				<label>Способ</label>
				<select class="form-control autosubmit" name="method">
					<option value="all">Все</option>
					<option value="cash" {if 'cash' == $filter->get('method')}selected="selected"{/if}>Наличный</option>
					<option value="cashless" {if 'cashless' == $filter->get('method')}selected="selected"{/if}>Безналичный</option>
					<option value="account" {if 'account' == $filter->get('method')}selected="selected"{/if}>Личный счет</option>
				</select>
			</div>
			<div class="col-lg-1">
				<label>ID</label>
				<input name="operation_id" class="form-control only_number" value="{$filter->get('operation_id')}" />
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
		</div>
	</div>
	<input name="tmp" type="hidden" value="no" />
</form>
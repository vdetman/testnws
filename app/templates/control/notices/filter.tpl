<form action="" method="get">
	<div class="filter_form col-lg-10">
		<div class="row">
			<div class="col-lg-2">
				<label>Сортировка</label>
				<select class="form-control autosubmit" name="Order">
					<option value="id_desc" {if 'id_desc' == $filter->get('Order')}selected="selected"{/if}>ID &darr;</option>
					<option value="id_asc" {if 'id_asc' == $filter->get('Order')}selected="selected"{/if}>ID &uparrow;</option>
				</select>
			</div>
			<div class="col-lg-1">
				<label>Тип</label>
				<select class="form-control autosubmit" name="Type">
					<option value="all">все</option>
					<option value="email" {if 'email' == $filter->get('Type')}selected="selected"{/if}>E-mail</option>
					<option value="telegram" {if 'telegram' == $filter->get('Type')}selected="selected"{/if}>Telegram</option>
					<option value="sms" {if 'sms' == $filter->get('Type')}selected="selected"{/if}>SMS</option>
				</select>
			</div>
			<div class="col-lg-1">
				<label>Статус</label>
				<select class="form-control autosubmit" name="Status">
					<option value="all">все</option>
					<option value="new" {if 'new' == $filter->get('Status')}selected="selected"{/if}>В ожидании</option>
					<option value="process" {if 'process' == $filter->get('Status')}selected="selected"{/if}>В процессе</option>
					<option value="success" {if 'success' == $filter->get('Status')}selected="selected"{/if}>Успешные</option>
					<option value="error" {if 'error' == $filter->get('Status')}selected="selected"{/if}>Ошибка</option>
				</select>
			</div>
			<div class="col-lg-2 period">
				<label>Период</label>
				{if $filter->has('PeriodMin') || $filter->has('PeriodMax')}
				<span class="erase" onclick="clearPeriod();">очистить</span>
				{/if}
				<br />
				<input name="PeriodMin" class="form-control min" readonly="" value="{$filter->get('PeriodMin')}" placeholder="с" />
				<input name="PeriodMax" class="form-control max" readonly="" value="{$filter->get('PeriodMax')}" placeholder="по" />
				<div class="clearfix"></div>
			</div>
			<div class="col-lg-2">
				<label>Найти</label>
				<input name="Search" class="form-control" value="{$filter->get('Search')}" />
			</div>
			<div class="col-lg-2">
				<label>{$filter->getTotal()} {$filter->getUnits()}</label>
				<button type="submit" class="btn btn-info btn-xs">применить</button>
				<button onclick="resetFilter(); return false;" class="btn btn-default btn-xs">сброс</button>
			</div>
		</div>
	</div>
</form>
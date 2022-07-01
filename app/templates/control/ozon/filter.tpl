<form action="" method="get">
	<div class="filter_form col-lg-10">
		<div class="row m-b-5">
			<div class="col-lg-1">
				<label>Сортировка</label>
				<select class="form-control autosubmit" name="order">
					<option value="id_desc" {if 'id_desc' == $filter->get('order')}selected="selected"{/if}>ID &darr;</option>
					<option value="id_asc" {if 'id_asc' == $filter->get('order')}selected="selected"{/if}>ID &uparrow;</option>
				</select>
			</div>
			<div class="col-lg-1">
				<label>ApiKey</label>
				<select class="form-control autosubmit" name="has_api_key">
					<option value="all"></option>
					<option value="1" {if '1' == $filter->get('has_api_key')}selected="selected"{/if}>есть</option>
					<option value="0" {if '0' == $filter->get('has_api_key')}selected="selected"{/if}>нет</option>
				</select>
			</div>
			<div class="col-lg-2">
				<label>Статус проекта</label>
				<select class="form-control autosubmit" name="ProjectStatus">
					<option value="all">все</option>
					<option value="active" {if 'active' == $filter->get('ProjectStatus')}selected="selected"{/if}>активные</option>
					<option value="deleted" {if 'deleted' == $filter->get('ProjectStatus')}selected="selected"{/if}>удаленные</option>
				</select>
			</div>
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
			<div class="col-lg-1">
				<label>AID</label>
				<input name="account_id" class="form-control" value="{$filter->get('account_id')}" />
			</div>
			<div class="col-lg-1">
				<label>PID</label>
				<input name="project_id" class="form-control" value="{$filter->get('project_id')}" />
			</div>
			<div class="col-lg-2">
				<label>Блокировка</label>
				<select class="form-control autosubmit" name="Blocked">
					<option value=""></option>
					<option value="any" {if 'any' == $filter->get('Blocked')}selected="selected"{/if}>Любая</option>
					<option value="absent" {if 'absent' == $filter->get('Blocked')}selected="selected"{/if}>Отсутствуют</option>
					<option value="all" {if 'all' == $filter->get('Blocked')}selected="selected"{/if}>All</option>
					<option value="stocks" {if 'stocks' == $filter->get('Blocked')}selected="selected"{/if}>Stocks</option>
					<option value="orders" {if 'orders' == $filter->get('Blocked')}selected="selected"{/if}>Orders</option>
					<option value="prices" {if 'prices' == $filter->get('Blocked')}selected="selected"{/if}>Prices</option>
					<option value="products" {if 'products' == $filter->get('Blocked')}selected="selected"{/if}>Products</option>
				</select>
			</div>
		</div>
	</div>
	<input name="tmp" type="hidden" value="no" />
</form>
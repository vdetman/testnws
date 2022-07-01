<form action="" method="get">
	<div class="filter_form col-lg-10">
		<div class="row">
			<div class="col-lg-2">
				<label>Сортировка</label>
				<select class="form-control autosubmit" name="order">
					<option value="id_desc" {if 'id_desc' == $filter->get('order')}selected="selected"{/if}>ID &darr;</option>
					<option value="id_asc" {if 'id_asc' == $filter->get('order')}selected="selected"{/if}>ID &uparrow;</option>
				</select>
			</div>
			<div class="col-lg-1">
				<label>Роль</label>
				<select class="form-control autosubmit" name="role_id">
					<option value="all">все</option>
					{foreach from=$filter->get('roles') key=$r item=$d}
					<option value="{$d}" {if $filter->get('role_id') == $d}selected="selected"{/if}>{$r}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-1">
				<label>Статус</label>
				<select class="form-control autosubmit" name="status">
					<option value="all" {if 'all' == $filter->get('status')}selected="selected"{/if}>все</option>
					<option value="active" {if 'active' == $filter->get('status')}selected="selected"{/if}>активные</option>
					<option value="blocked" {if 'blocked' == $filter->get('status')}selected="selected"{/if}>неактивные</option>
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
			<div class="col-lg-3">
				<label>Найти</label>
				<input name="search" class="form-control" value="{$filter->get('search')}" />
			</div>
			<div class="col-lg-2">
				<label>{$filter->getTotal()} {$filter->getUnits()}</label>
				<button type="submit" class="btn btn-info btn-xs">применить</button>
				<button onclick="resetFilter(); return false;" class="btn btn-default btn-xs">сброс</button>
			</div>
		</div>
	</div>
	<input name="tmp" type="hidden" value="no" />
</form>
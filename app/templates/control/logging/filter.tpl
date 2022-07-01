<form action="" method="get">
	<div class="filter_form col-lg-9">
		<div class="row">
			<div class="col-lg-1">
				<label>&nbsp;</label>
				<select class="form-control" name="orderDir">
					<option value="desc" {if 'Desc' == $filter->get('orderDir')}selected="selected"{/if}>&darr; (я-а)</option>
					<option value="asc" {if 'Asc' == $filter->get('orderDir')}selected="selected"{/if}>&uarr; (а-я)</option>
				</select>
			</div>
			<div class="col-lg-1">
				<label>Агент</label>
				<select class="form-control" name="agent">
					<option value="all">все</option>
					<option value="user" {if $filter->get('Type') == 'human' && $filter->get('isUser')}selected="selected"{/if}>Пользователь</option>
					<option value="human" {if $filter->get('Type') == 'human' && $filter->get('isUser') === false}selected="selected"{/if}>Гость</option>
					<option value="robot" {if $filter->get('Type') == 'robot'}selected="selected"{/if}>Робот</option>
					<option value="norobot" {if $filter->get('Type') == 'human' && !$filter->has('isUser')}selected="selected"{/if}>Не робот</option>
				</select>
			</div>
			<div class="col-lg-2 period">
				<label>Период</label>
				{if $filter->has('periodMin') || $filter->has('periodMax')}
				<span class="erase" onclick="clearPeriod();">очистить</span>
				{/if}
				<br />
				<input name="periodMin" class="form-control min" readonly="" value="{$filter->get('periodMin')}" placeholder="с" />
				<input name="periodMax" class="form-control max" readonly="" value="{$filter->get('periodMax')}" placeholder="по" />
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
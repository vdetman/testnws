<form action="" method="get">
	<div class="filter_form col-lg-9">
		<div class="row m-b-5">
			<div class="col-lg-1">
				<label>Язык</label>
				<select class="form-control autosubmit" name="Language">
					<option value="ru" {if 'ru' == $filter->get('Language')}selected="selected"{/if}>RU</option>
				</select>
			</div>
			<div class="col-lg-2">
				<label>Статус</label>
				<select class="form-control autosubmit" name="Status">
					<option value="all" {if 'all' == $filter->get('Status')}selected="selected"{/if}>все</option>
					<option value="active" {if 'active' == $filter->get('Status')}selected="selected"{/if}>активные</option>
					<option value="hidden" {if 'hidden' == $filter->get('Status')}selected="selected"{/if}>скрытые</option>
				</select>
			</div>
			<div class="col-lg-7">
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
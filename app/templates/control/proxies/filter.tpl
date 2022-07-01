<form action="" method="get">
	<div class="filter_form col-lg-8">
		<div class="row">
			<div class="col-lg-2">
				<label>Сортировка</label>
				<select class="form-control autosubmit" name="Order">
					<option value="id_asc" {if 'id_asc' == $filter->get('Order')}selected="selected"{/if}>ID &uparrow;</option>
					<option value="id_desc" {if 'id_desc' == $filter->get('Order')}selected="selected"{/if}>ID &darr;</option>
					<option value="ip_asc" {if 'ip_asc' == $filter->get('Order')}selected="selected"{/if}>Ip &uparrow;</option>
					<option value="ip_desc" {if 'ip_desc' == $filter->get('Order')}selected="selected"{/if}>Ip &darr;</option>
					<option value="ex_asc" {if 'ex_asc' == $filter->get('Order')}selected="selected"{/if}>Срок действия &uparrow;</option>
					<option value="ex_desc" {if 'ex_desc' == $filter->get('Order')}selected="selected"{/if}>Срок действия &darr;</option>
				</select>
			</div>
			<div class="col-lg-2">
				<label>Группа</label>
				<select class="form-control autosubmit" name="Group">
					<option value="all">все</option>
					<option value="php" {if 'php' == $filter->get('Group')}selected="selected"{/if}>PHP</option>
					<option value="ch" {if 'ch' == $filter->get('Group')}selected="selected"{/if}>CH</option>
				</select>
			</div>
			<div class="col-lg-2">
				<label>Статус</label>
				<select class="form-control autosubmit" name="Status">
					<option value="all">все</option>
					<option value="active" {if 'active' == $filter->get('Status')}selected="selected"{/if}>Активные</option>
					<option value="expired" {if 'expired' == $filter->get('Status')}selected="selected"{/if}>Истекшие</option>
					<option value="disabled" {if 'disabled' == $filter->get('Status')}selected="selected"{/if}>Отключенные</option>
				</select>
			</div>
			<div class="col-lg-4">
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
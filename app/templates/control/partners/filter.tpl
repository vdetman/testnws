<form action="" method="get">
	<div class="filter_form col-lg-9">
		<div class="row m-b-5">
			<div class="col-lg-2">
				<label>Статус</label>
				<select class="form-control autosubmit" name="status">
					<option value="all" {if 'all' == $filter->get('status')}selected="selected"{/if}>все</option>
					<option value="active" {if 'active' == $filter->get('status')}selected="selected"{/if}>активные</option>
					<option value="hidden" {if 'hidden' == $filter->get('status')}selected="selected"{/if}>скрытые</option>
				</select>
			</div>
			<div class="col-lg-3">
				<label>Сферы</label>
				<select class="form-control autosubmit" name="sphere_id">
					<option value=""></option>
					{foreach from=$spheres item=$s}
						<option value="{$s->getId()}" {if $s->getId() == $filter->get('sphere_id')}selected="selected"{/if}>{$s->getName()}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-5">
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
<form action="" method="get">
	<div class="filter_form col-lg-9">
		<div class="row">
			<div class="col-lg-2 period">
				<label>Период</label>
				{if $filter->has('Since') || $filter->has('Until')}
				<span class="erase" onclick="clearPeriod();">сбросить</span>
				{/if}
				<br />
				<input name="Since" class="form-control min" readonly="" value="{$filter->get('Since')}" placeholder="с" />
				<input name="Until" class="form-control max" readonly="" value="{$filter->get('Until')}" placeholder="по" />
				<div class="clearfix"></div>
			</div>
			{*<div class="col-lg-2">
				<label>Статус менеджеров</label>
				<select class="form-control autosubmit" name="ResponsibleUserIsActive">
					<option value="all">Все</option>
					<option value="true" {if 'true' == $filter->get('ResponsibleUserIsActive')}selected="selected"{/if}>Активные</option>
					<option value="false" {if 'false' == $filter->get('ResponsibleUserIsActive')}selected="selected"{/if}>Не активные</option>
				</select>
			</div>*}

				<div class="col-lg-2">
					<label>&nbsp;</label>
					<button type="submit" class="btn btn-info btn-xs">применить</button>
					<button onclick="resetFilter(); return false;" class="btn btn-default btn-xs">сброс</button>
				</div>

		</div>
	</div>
</form>
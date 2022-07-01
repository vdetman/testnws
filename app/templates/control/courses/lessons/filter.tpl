<form action="" method="get">
	<div class="filter_form col-lg-9">
		<div class="row m-b-5">
			<div class="col-lg-2">
				<label>Курсы</label>
				<select class="form-control autosubmit" name="course_id">
					<option value="all" {if 'all' == $filter->get('course_id')}selected="selected"{/if}>все</option>
					{foreach from=$courses item=$p}
						<option value="{$p->getId()}" {if $p->getId() == $filter->get('course_id')}selected="selected"{/if}>{$p->getName()}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-2">
				<label>{$filter->getTotal()} {$filter->getUnits()}</label>
				<button type="submit" class="btn btn-info btn-xs">применить</button>
				<button onclick="resetFilter(); return false;" class="btn btn-default btn-xs">сброс</button>
			</div>
		</div>
	</div>
</form>

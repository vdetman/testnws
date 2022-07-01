{include file="../_units/header.tpl"}

<div class="row">
	<div class="col-sm-12">
		<div class="portlet">
			<div class="portlet-heading">
				<h3 class="portlet-title text-dark">Активность пользователей</h3>
				<div class="clearfix"></div>
			</div>
			<div class="portlet-body">
				<div id="usersActivity" style="height: 200px;"></div>
				<script type="text/javascript">
					$.usersActivityData = {$usersActivityData};
				</script>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div class="portlet">
			<div class="portlet-heading">
				<h3 class="portlet-title text-dark">Финансы</h3>
				<div class="clearfix"></div>
			</div>
			<div class="portlet-body">
				<div id="financeActivity" style="height: 200px;"></div>
				<script type="text/javascript">
					$.financeActivityData = {$financeActivityData};
				</script>
			</div>
		</div>
	</div>
</div>

{include file="../_units/footer.tpl"}
{include file="../_units/header.tpl"}
	
	<div class="row m-b-15">
		<div class="col-lg-12">
			<a class="btn btn-{if !$type}success{else}default{/if} btn-rounded btn-xs" href="/{ADMIN}/{$module}/constants">все</a>
			<a class="btn btn-{if 'user'==$type}success{else}default{/if} btn-rounded btn-xs" href="/{ADMIN}/{$module}/constants?type=user">пользовательские</a>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<iframe src="/{ADMIN}/{$module}/constants?view=1{if $type}&type={$type}{/if}" style="width: 100%; height: 1200px;"></iframe>
		</div>
	</div>

{include file="../_units/footer.tpl"}
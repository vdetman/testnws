{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<pre>{print_r($config, 1)}</pre>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-5">
			<pre>{print_r($allKeys, 1)}</pre>
		</div>
		<div class="col-lg-7">
			<pre>{print_r($allTags, 1)}</pre>
		</div>
	</div>

{include file="../_units/footer.tpl"}
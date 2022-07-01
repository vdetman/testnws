<ul class="nav nav-tabs">
{foreach from=$languages key=$iso item=$lang name=lang}
	<li class="{if $smarty.foreach.lang.first}active{/if}">
		<a href="#{$letter->getId()}{$iso}" data-toggle="tab" aria-expanded="{if $smarty.foreach.lang.first}true{else}false{/if}">
			<span class="visible-xs"><i class="fa fa-home"></i></span>
			<span class="hidden-xs">{$lang}</span>
			<span class="btn btn-xs {if $letter->getData($iso)->getFilled()}btn-info{else}btn-danger{/if}" onclick="showLetterBody({$letter->getId()}, '{$iso}');"><i class="fa fa-eye"></i></span>
		</a>
	</li>
{/foreach}
</ul>
<div class="tab-content">
{foreach from=$languages key=$iso item=$lang name=lang}
	{assign var='d' value=$letter->getData($iso)}
	<div class="tab-pane {if $smarty.foreach.lang.first}active{/if}" id="{$letter->getId()}{$iso}"> 
		<div class="form-group">
			<div class="row">
				<div class="col-lg-12">
					<label>Тема письма</label>
					<input name="data[{$iso}][Subject]" class="form-control" value="{if isset($data[$iso])}{$data[$iso].Subject}{else}{$d->getSubject()}{/if}" />
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-lg-12">
					<label>Тело письма</label>
					<textarea class="form-control" name="data[{$iso}][Body]" style="height: 600px;" id="Body-{$iso}">{if isset($data[$iso])}{$data[$iso].Body}{else}{$d->getBody()}{/if}</textarea>
				</div>
			</div>
		</div>
	</div>
	{*<script type="text/javascript">$(document).ready(function(){ ckeditorInit('#Body-{$iso}', '{ADMIN}'); });</script>*}
{/foreach}
</div>
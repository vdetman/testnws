<ul class="nav nav-tabs">
{foreach from=$languages item=$l name=lang}
	<li class="{if $smarty.foreach.lang.first}active{/if}">
		<a href="#{$page->getId()}{$l->getIso()}" data-toggle="tab" aria-expanded="{if $smarty.foreach.lang.first}true{else}false{/if}">
			<span class="visible-xs"><i class="fa fa-home"></i></span>
			<span class="hidden-xs">{$l->getName()}</span>
		</a>
	</li>
{/foreach}
</ul>
<div class="tab-content">
{foreach from=$languages item=$l name=lang}
	{assign var='d' value=$page->getData($l->getIso())}
	<div class="tab-pane {if $smarty.foreach.lang.first}active{/if}" id="{$page->getId()}{$l->getIso()}"> 
		
		
		<div class="form-group">
			<div class="row">
				<div class="col-lg-5">
					<label>Заголовок (Title)</label>
					<input name="data[{$l->getIso()}][Title]" class="form-control" value="{if isset($data[$l->getIso()])}{$data[$l->getIso()].Title}{else}{$d->getTitle()}{/if}" />
				</div>
				<div class="col-lg-5">
					<label>Заголовок (Header)</label>
					<input name="data[{$l->getIso()}][Header]" class="form-control" value="{if isset($data[$l->getIso()])}{$data[$l->getIso()].Header}{else}{$d->getHeader()}{/if}" />
				</div>
				<div class="col-lg-2">
					<label>Robots</label>
					<select class="form-control" name="data[{$l->getIso()}][Robots]">
						{foreach from=$robots item=$r}
						<option value="{$r}" {if isset($data[$l->getIso()]) && $data[$l->getIso()].Robots==$r || !isset($data[$l->getIso()]) && $d->getRobots()==$r}selected="selected"{/if}>{$r}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-lg-12">
					<label>Контент</label>
					<textarea class="form-control" name="data[{$l->getIso()}][Content]" id="Content-{$l->getIso()}">{if isset($data[$l->getIso()])}{$data[$l->getIso()].Content}{else}{$d->getContent()}{/if}</textarea>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-lg-6">
					<label>Description</label>
					<textarea class="form-control" name="data[{$l->getIso()}][Description]" rows="2" >{if isset($data[$l->getIso()])}{$data[$l->getIso()].Description}{else}{$d->getDescription()}{/if}</textarea>
				</div>
				<div class="col-lg-6">
					<label>Keywords</label>
					<textarea class="form-control" name="data[{$l->getIso()}][Keywords]" rows="2" >{if isset($data[$l->getIso()])}{$data[$l->getIso()].Keywords}{else}{$d->getKeywords()}{/if}</textarea>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">$(document).ready(function(){ ckeditorInit('#Content-{$l->getIso()}', '{ADMIN}'); });</script>
{/foreach}
</div>
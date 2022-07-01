{include file="../../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="text-right">
						<a href="/{ADMIN}/{$module}/create" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Создать пост</a>
					</div>
					<div class="clearfix"></div>
					{include file="./filter.tpl" filter=$filter}
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
				{if $posts}
					{$pagination}
					<table class="table table-bordered table-hover" id="post_listing">
						<thead>
							<tr>
								<th>Описание</th>
								<th>Main</th>
								<th>Действия</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$posts item=$p}
							<tr id="item_{$p->getId()}">
								<td class="data">
									<span class="ident"><a href="/{ADMIN}/{$module}/edit/{$p->getId()}">#{$p->getId()} {$p->getName()}</a></span>
									<div>
										{assign var='cw' value=$p->getText()|strip_tags:false|count_characters}
										{if 3000 < $cw}{assign var='cls' value='bg-success'}
										{elseif 2000 < $cw}{assign var='cls' value='bg-primary'}
										{elseif 1000 < $cw}{assign var='cls' value='bg-warning'}
										{else}{assign var='cls' value='bg-danger'}{/if}
										<label class="badge {$cls}" title="Кол-во символов">{$cw}</label>
										<span class="add {if !$p->getPhotoSmall()}empty{/if}"><i class="fa fa-image" title="Картинка"></i></span>
										<span class="add {if !$p->getTitle()}empty{/if}"><i class="fa fa-text-width" title="Title"></i></span>
										<span class="add {if !$p->getHeader()}empty{/if}"><i class="fa fa-header" title="Header"></i></span>
										<span class="add {if !$p->getKeywords()}empty{/if}"><i class="fa fa-align-justify" title="Keywords"></i></span>
										<span class="add {if !$p->getDescription()}empty{/if}"><i class="fa fa-align-justify" title="Description"></i></span>
										<span class="add {if !$p->getRobots()}empty{/if}"><i class="fa fa-android" title="Robots"></i></span>
									</div>
								</td>
								<td class="main">
									<div onclick="switcher(this);" class="switcher {if 'active' == $p->getMain()}true{else}false{/if}"
										data-field="Main"
										data-value="{if 'active' == $p->getMain()}1{else}0{/if}"
										data-id="{$p->getId()}">
									</div>
								</td>
								<td class="operations">
									<div onclick="switcher(this);" class="switcher {if 'active' == $p->getStatus()}true{else}false{/if}"
										data-field="Status"
										data-value="{if 'active' == $p->getStatus()}1{else}0{/if}"
										data-id="{$p->getId()}">
									</div>
									<a href="/{ADMIN}/{$module}/edit/{$p->getId()}" title="Редактировать"><i class="fa fa-edit text-info"></i></a>
									<i class="fa fa-times text-danger pointer" title="Удалить" onclick="deletePost({$p->getId()});"></i>
								</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
					{$pagination}
				{else}
					<h4>Не найдено ни одного элемента</h4>
				{/if}
				</div>
			</div>
		</div>
	</div>

{include file="../../_units/footer.tpl"}
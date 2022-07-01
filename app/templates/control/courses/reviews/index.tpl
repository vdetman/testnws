{include file="../../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a href="/{ADMIN}/{$module}/reviewCreate/{$course->getId()}" class="btn btn-success btn-xs pull-left"><i class="fa fa-plus"></i> Создать отзыв</a>
					<a href="/{ADMIN}/{$module}/edit/{$course->getId()}" class="btn btn-info btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к редактированию курса: {$course->getName()}</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
				{if $reviews}
					<table class="table table-bordered table-hover" id="reviews_listing">
						<thead>
							<tr>
								<th>Имя</th>
								<th>Действия</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$reviews item=$p}
							<tr id="item_{$p->getId()}">
								<td class="data">
									<span class="ident"><a href="/{ADMIN}/{$module}/reviewEdit/{$p->getId()}">#{$p->getId()} {$p->getName()}</a></span>
								</td>
								<td class="operations">
									<a href="/{ADMIN}/{$module}/reviewEdit/{$p->getId()}" title="Редактировать"><i class="fa fa-edit text-info"></i></a>
									<i class="fa fa-times text-danger pointer" title="Удалить" onclick="deleteReview({$p->getId()});"></i>
								</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				{else}
					<h4>Не найдено ни одного элемента</h4>
				{/if}
				</div>
			</div>
		</div>
	</div>

{include file="../../_units/footer.tpl"}

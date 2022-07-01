<div class="card col-12 mb-2 p-1">
	<h6 class="mt-2">{$news->getHeader()}</h6>
	<p class="text-rihgt"><em>{$news->getCreated()->format('d #Hmg Y \в H:i')}</em></p>
	<div>{$news->getContent()}</div>
</div>
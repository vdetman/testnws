<li id="file_{$f->getId()}">
	<div class="options pull-left">
		<i title="Переместить" class="fa fa-navicon handler"></i>
		<i title="Удалить" class="fa fa-times btn-drop" onclick="deleteFile({$f->getId()});"></i>
	</div>
	<div class="pull-left m-l-15"><i>{$f->getName()}</i></div>
	<div class="clearfix"></div>
</li>
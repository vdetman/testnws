<div class="row">
	<div class="col-lg-4">
		<div class="img_cont" id="upl-photo_paid" style="background-image: url('{if $lesson->getPhotoPaid()}{$lesson->getPhotoPaid()}{else}{PARTNER_DEFAULT_PHOTO}{/if}');"></div>
		<button id="upl-btn-photo_paid" data-id="{$lesson->getId()}" class="btn btn-xs btn-effect-ripple btn-info" data-field="photo_paid" onclick="return false;">
			Загрузить картинку "Paid"
			<i class="fa fa-spinner fa-spin hidden" id="upl-loader-photo_paid"></i>
		</button>
		<button id="upl-delete-photo_paid" onclick="deletePhoto({$lesson->getId()}, 'photo_paid'); return false;" class="btn btn-xs btn-effect-ripple btn-danger {if !$lesson->getPhotoPaid()}hidden{/if}"><i class="fa fa-times"></i></button>
	</div>
	<div class="col-lg-4">
		<div class="img_cont" id="upl-photo_unpaid" style="background-image: url('{if $lesson->getPhotoUnpaid()}{$lesson->getPhotoUnpaid()}{else}{PARTNER_DEFAULT_PHOTO}{/if}');"></div>
		<button id="upl-btn-photo_unpaid" data-id="{$lesson->getId()}" class="btn btn-xs btn-effect-ripple btn-info" data-field="photo_unpaid" onclick="return false;">
			Загрузить картинку "Unpaid"
			<i class="fa fa-spinner fa-spin hidden" id="upl-loader-photo_unpaid"></i>
		</button>
		<button id="upl-delete-photo_unpaid" onclick="deletePhoto({$lesson->getId()}, 'photo_unpaid'); return false;" class="btn btn-xs btn-effect-ripple btn-danger {if !$lesson->getPhotoUnpaid()}hidden{/if}"><i class="fa fa-times"></i></button>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	var field = 'photo_paid';
	if (typeof(AjaxUpload) !== 'undefined' && $('#upl-btn-' + field).length > 0) {
		var id = $('#upl-btn-' + field).data('id');
		new AjaxUpload($('#upl-btn-' + field), {
			action: '/' + $.admin + '/courses/uploadLessonPhoto',
			name: 'file',
			accept: 'image/*',
			responseType : 'json',
			data: { id : id, field: field },
			onSubmit: function(file, ext){
				if (! (ext && /^(jpg|png|jpeg|bmp)$/.test(ext))){
					notify('Допустимые файлы: jpg, png, bmp', 0);
					return false;
				}
				$('#upl-loader-' + field).removeClass('hidden');
			},
			onComplete: function(file, response){
				$('#upl-loader-' + field).addClass('hidden');
				if(response.status) {
					$('#upl-' + field).css('background-image', "url('" + response.src + "')");
					if(response.del) { $('#upl-delete-' + field).removeClass('hidden'); } else { $('#upl-delete-' + field).addClass('hidden'); }
				}
			}
		});
	}
	var field = 'photo_unpaid';
	if (typeof(AjaxUpload) !== 'undefined' && $('#upl-btn-' + field).length > 0) {
		var id = $('#upl-btn-' + field).data('id');
		new AjaxUpload($('#upl-btn-' + field), {
			action: '/' + $.admin + '/courses/uploadLessonPhoto',
			name: 'file',
			accept: 'image/*',
			responseType : 'json',
			data: { id : id, field: field },
			onSubmit: function(file, ext){
				if (! (ext && /^(jpg|png|jpeg|bmp)$/.test(ext))){
					notify('Допустимые файлы: jpg, png, bmp', 0);
					return false;
				}
				$('#upl-loader-' + field).removeClass('hidden');
			},
			onComplete: function(file, response){
				$('#upl-loader-' + field).addClass('hidden');
				if(response.status) {
					$('#upl-' + field).css('background-image', "url('" + response.src + "')");
					if(response.del) { $('#upl-delete-' + field).removeClass('hidden'); } else { $('#upl-delete-' + field).addClass('hidden'); }
				}
			}
		});
	}
});

function deletePhoto(itemId, field){
	if(confirm('Действительно удалить?')){
		$.post('/' + $.admin + '/courses/deleteLessonPhoto', { id : itemId, field : field },
		function(response){
			if(response.status) {
				$('#upl-' + field).css('background-image', "url('" + response.src + "')");
				if(response.del) { 
					$('#upl-delete-' + field).removeClass('hidden'); 
				} else { 
					$('#upl-delete-' + field).addClass('hidden'); 
				}
			}
		},'json').fail(function( response ) {
			notify('Ajax error', 0);
			console.log(response);
		});
	}
	return false;
}
</script>

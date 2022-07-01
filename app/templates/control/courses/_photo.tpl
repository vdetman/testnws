<div class="row">
	<div class="col-lg-4">
		<div class="img_cont" id="upl-photo" style="background-image: url('{if $course->getPhoto()}{$course->getPhoto()}{else}{PARTNER_DEFAULT_PHOTO}{/if}');"></div>
		<button id="upl-btn-photo" data-id="{$course->getId()}" class="btn btn-xs btn-effect-ripple btn-info" onclick="return false;">
			Загрузить основную картинку
			<i class="fa fa-spinner fa-spin hidden" id="upl-loader-photo"></i>
		</button>
		<button id="upl-delete-photo" onclick="deletePhoto({$course->getId()}, 'photo'); return false;" class="btn btn-xs btn-effect-ripple btn-danger {if !$course->getPhoto()}hidden{/if}"><i class="fa fa-times"></i></button>
	</div>
	<div class="col-lg-4">
		<div class="img_cont" id="upl-photo_header" style="background-image: url('{if $course->getPhotoHeader()}{$course->getPhotoHeader()}{else}{PARTNER_DEFAULT_PHOTO}{/if}');"></div>
		<button id="upl-btn-photo_header" data-id="{$course->getId()}" class="btn btn-xs btn-effect-ripple btn-info" data-field="photo_header" onclick="return false;">
			Загрузить картинку для "Header"
			<i class="fa fa-spinner fa-spin hidden" id="upl-loader-photo_header"></i>
		</button>
		<button id="upl-delete-photo_header" onclick="deletePhoto({$course->getId()}, 'photo_header'); return false;" class="btn btn-xs btn-effect-ripple btn-danger {if !$course->getPhotoHeader()}hidden{/if}"><i class="fa fa-times"></i></button>
	</div>
	<div class="col-lg-4">
		<div class="img_cont" id="upl-photo_footer" style="background-image: url('{if $course->getPhotoFooter()}{$course->getPhotoFooter()}{else}{PARTNER_DEFAULT_PHOTO}{/if}');"></div>
		<button id="upl-btn-photo_footer" data-id="{$course->getId()}" class="btn btn-xs btn-effect-ripple btn-info" data-field="photo_footer" onclick="return false;">
			Загрузить картинку для "Footer"
			<i class="fa fa-spinner fa-spin hidden" id="upl-loader-photo_footer"></i>
		</button>
		<button id="upl-delete-photo_footer" onclick="deletePhoto({$course->getId()}, 'photo_footer'); return false;" class="btn btn-xs btn-effect-ripple btn-danger {if !$course->getPhotoFooter()}hidden{/if}"><i class="fa fa-times"></i></button>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	var field = 'photo';
	if (typeof(AjaxUpload) !== 'undefined' && $('#upl-btn-' + field).length > 0) {
		var id = $('#upl-btn-' + field).data('id');
		new AjaxUpload($('#upl-btn-' + field), {
			action: '/' + $.admin + '/courses/uploadCoursePhoto',
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
	var field = 'photo_header';
	if (typeof(AjaxUpload) !== 'undefined' && $('#upl-btn-' + field).length > 0) {
		var id = $('#upl-btn-' + field).data('id');
		new AjaxUpload($('#upl-btn-' + field), {
			action: '/' + $.admin + '/courses/uploadCoursePhoto',
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
	var field = 'photo_footer';
	if (typeof(AjaxUpload) !== 'undefined' && $('#upl-btn-' + field).length > 0) {
		var id = $('#upl-btn-' + field).data('id');
		new AjaxUpload($('#upl-btn-' + field), {
			action: '/' + $.admin + '/courses/uploadCoursePhoto',
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
		$.post('/' + $.admin + '/courses/deleteCoursePhoto', { id : itemId, field : field },
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

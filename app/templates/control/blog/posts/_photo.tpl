<div class="row">
	<div class="col-lg-12">
		<img id="upl-photo" src="{if $post->getPhotoSmall()}{$post->getPhotoSmall()}{else}{BLOG_DEFAULT_PHOTO}{/if}" alt="" class="m-b-10" style="height: 150px; display: block;" />
		<button id="upl-btn" data-id="{$post->getId()}" class="btn btn-xs btn-effect-ripple btn-info" onclick="return false;">
			Загрузить картинку
			<i class="fa fa-spinner fa-spin hidden" id="upl-loader"></i>
		</button>
		<button id="upl-delete" onclick="deletePhoto({$post->getId()}); return false;" class="btn btn-xs btn-effect-ripple btn-danger {if !$post->getPhotoSmall()}hidden{/if}"><i class="fa fa-times"></i></button>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	if (typeof(AjaxUpload) !== 'undefined' && $('#upl-btn').length > 0) {
		new AjaxUpload($('#upl-btn'), {
			action: '/' + $.admin + '/blog/uploadPhoto',
			name: 'file',
			accept: 'image/*',
			responseType : 'json',
			data: { id : $('#upl-btn').data('id') },
			onSubmit: function(file, ext){
				if (! (ext && /^(jpg|png|jpeg|bmp)$/.test(ext))){
					notify('Допустимые файлы: jpg, png, bmp', 0);
					return false;
				}
				$('#upl-loader').show();
			},
			onComplete: function(file, response){
				$('#upl-loader').hide();
				if(response.status) {
					$('#upl-photo').attr('src', response.src);
					if(response.del) { $('#upl-delete').removeClass('hidden'); } else { $('#upl-delete').addClass('hidden'); }
				}
			}
		});
	}
});

function deletePhoto(itemId){
	if(confirm('Действительно удалить?')){
		$.post('/' + $.admin + '/blog/deletePhoto', { id : itemId },
		function(response){
			if(response.status) {
				$('#upl-photo').attr('src', response.src);
				if(response.del) { $('#upl-delete').removeClass('hidden'); } else { $('#upl-delete').addClass('hidden'); }
			}
		},'json').fail(function( response ) {
			notify('Ajax error', 0);
			console.log(response);
		});
	}
	return false;
}
</script>
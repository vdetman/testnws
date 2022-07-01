{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<form role="form" action="" method="post" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Логин <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="profile[Username]" value="{if isset($post.Username)}{$post.Username}{else}{$currentUser->getUsername()}{/if}" />
								</div>
								<div class="form-group">
									<label>E-mail <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="profile[Email]" value="{if isset($post.Email)}{$post.Email}{else}{$currentUser->getEmail()}{/if}" />
								</div>
								<div class="form-group">
									<label>Телефон</label>
									<input type="text" class="form-control" name="profile[Phone]" value="{if isset($post.Phone)}{$post.Phone}{else}{$currentUser->getPhone()}{/if}" />
								</div>
								<div class="form-group">
									<label>Фамилия</label>
									<input type="text" class="form-control" name="profile[LastName]" value="{if isset($post.LastName)}{$post.LastName}{else}{$currentUser->getLastName()}{/if}" />
								</div>
								<div class="form-group">
									<label>Имя <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="profile[FirstName]" value="{if isset($post.FirstName)}{$post.FirstName}{else}{$currentUser->getFirstName()}{/if}" />
								</div>
								<div class="form-group">
									<label>Отчество</label>
									<input type="text" class="form-control" name="profile[SecondName]" value="{if isset($post.SecondName)}{$post.SecondName}{else}{$currentUser->getSecondName()}{/if}" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Аватар</label>
									<input type="file" accept="image/*" class="form-control" name="avatar" />
									<span class="help-block"><small>4Mb max, jpg/jpeg/png</small></span>
								</div>
								{if $currentUser->getAvatarExist()}
								<div class="form-group" id="avatar">
									<img src="{$currentUser->getAvatar()}" /><br />
									<a class="btn btn-icon btn-xs btn-danger m-t-5" onclick="deleteAvatar('{$currentUser->getId()}'); return false;"><i class="fa fa-remove"></i> Удалить</a>
								</div>
								{/if}
							</div>
						</div>
						<input type="hidden" name="save" value="1" />
						<button type="submit" class="btn btn-purple">Сохранить</button>
						{if $result}
							<div class="alert {if $result.status}alert-success{else}alert-danger{/if} m-t-15">{$result.descr}</div>
						{/if}
					</form>
				</div><!-- panel-body -->
			</div>
			<!-- panel -->
		</div>
	</div>

{include file="../_units/footer.tpl"}
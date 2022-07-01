{include file="../_units/header.tpl"}

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<button onclick="$('#mainForm').submit();" class="btn btn-success btn-xs pull-left m-r-10"><i class="fa fa-save"></i> Сохранить</button>
					<a href="/{ADMIN}/users/{$backQuery}" class="btn btn-primary btn-xs pull-right"><i class="fa fa-reply"></i> Вернуться к списку</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form action="" method="post" id="mainForm">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label>E-mail <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="profile[email]" value="{if isset($post.Email)}{$post.Email}{else}{$user->getEmail()}{/if}" />
								</div>
								<div class="form-group">
									<label>Телефон</label>
									<input type="text" class="form-control" name="profile[phone]" value="{if isset($post.Phone)}{$post.Phone}{else}{$user->getPhone()}{/if}" />
								</div>
								<div class="form-group">
									<label>Фамилия</label>
									<input type="text" class="form-control" name="profile[last_name]" value="{if isset($post.last_name)}{$post.last_name}{else}{$user->getLastName()}{/if}" />
								</div>
								<div class="form-group">
									<label>Имя <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="profile[first_name]" value="{if isset($post.first_name)}{$post.first_name}{else}{$user->getFirstName()}{/if}" />
								</div>
								<div class="form-group">
									<label>Отчество</label>
									<input type="text" class="form-control" name="profile[second_name]" value="{if isset($post.second_name)}{$post.second_name}{else}{$user->getSecondName()}{/if}" />
								</div>
							</div>
						</div>
						<input type="hidden" name="save" value="1" />
						<div class="row">
							<div class="col-lg-12">
								<button type="submit" class="btn btn-success btn-xs pull-left"><i class="fa fa-save"></i> Сохранить</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

{include file="../_units/footer.tpl"}
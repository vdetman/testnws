{include file="./auth-header.tpl"}

	<div class="panel panel-color panel-primary">

		<div class="panel-heading">
			<h3 class="text-center m-t-10">Вход</h3>
		</div>

		<form class="form-horizontal m-t-40" id="form_login" action="" method="post" onsubmit="return checkForm();">
			<div class="form-group">
				<div class="col-xs-12">
					<input name="email" class="form-control auto_erase_status" type="text" placeholder="E-mail" value="{if isset($post.email)}{$post.email}{/if}" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-12">
					<input name="password" class="form-control auto_erase_status" type="password" placeholder="Пароль" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-12">
					<label class="cr-styled">
						<input type="checkbox" name="remember" {if isset($post.remember) && $post.remember}checked="true"{/if} />
						<i class="fa"></i>
						запомнить
					</label>
				</div>
			</div>
			<div class="form-group text-right">
				<div class="col-xs-12">
					<button class="btn btn-primary w-md" type="submit">войти</button>
				</div>
			</div>
			<input type="hidden" name="primary" value="1" />

			{if $error}
				<div class="alert alert-danger">{$error}</div>
			{/if}

			<div class="form-group m-t-30">
				<div class="col-sm-7">
					<a href="/{ADMIN}/recovery"><i class="fa fa-lock m-r-5"></i> Забыли пароль?</a>
				</div>
			</div>
		</form>

		<script type="text/javascript">
			$(document).ready(function(){
				$('#form_login input').keydown(function(e){
					if(e.which==13) $('#form_login').submit();
				});
			});
			function checkForm() {

				var flag = true,
					email = $('input[name=email]'),
					passw = $('input[name=password]');

				if(!IsValidEmail(email.val())) email.parent('div').addClass('has-error'), flag = false;
				if(passw.val() === '') passw.parent('div').addClass('has-error'), flag = false;

				return flag;
			}
		</script>

	</div>

{include file="./auth-footer.tpl"}
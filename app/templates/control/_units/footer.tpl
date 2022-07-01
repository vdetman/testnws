			</div>

			<footer class="footer">
				{$smarty.now|date_format:'%Y'} ©
			</footer>

		</section>

		<div id="modalContainer" class="modal fade" aria-hidden="true" style="display: none;"></div>
		
		{if $this->session()->getFlash('_notify', true)}
			{assign var=notify value=$this->session()->getFlash('_notify')}
			<script type="text/javascript">
				$(document).ready(function(){
					notify('{$notify.text}', {$notify.status});
				});
			</script>
		{/if}
		<input type="hidden" id="is_sended_ajax" value="0" />
		<script type="text/javascript" src="/admin/js/app.js{Func::modifyTime('/admin/js/app.js')}"></script>
	</body>
</html>
		{if $notify}
		<script type="text/javascript">
			$(document).ready(function(){
				notify('{$notify.text}', {$notify.status});
			});
		</script>
		{/if}

		<input type="hidden" id="is_sended_ajax" value="0" />
	</body>
</html>
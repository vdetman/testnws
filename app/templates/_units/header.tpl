<header class="bd-header bg-dark py-3 d-flex align-items-stretch border-bottom border-dark">
  <div class="container-fluid d-flex align-items-center">
    <h1 class="d-flex align-items-center fs-4 text-white mb-0">
      <img src="https://getbootstrap.com/docs/5.2/assets/brand/bootstrap-logo-white.svg" width="38" height="30" class="me-3" alt="Bootstrap">
    </h1>
    <ul class="nav nav-pills">
			<li class="nav-item"><a href="/news" class="nav-link text-white {if 'news'== $current}active{/if}">News</a></li>
			<li class="nav-item"><a href="/news/info" class="nav-link text-white {if 'info'== $current}active{/if}">Info</a></li>
			{if 'news'== $current}
				<li class="nav-item"><a href="#" class="nav-link text-white" onclick="refreshRss(); return false;">Подгрузить через RSS</a></li>
				<li class="nav-item"><a href="#" class="nav-link text-white" onclick="clearCache(); return false;">сбросить КЭШ</a></li>
			{/if}
		</ul>
  </div>
</header>
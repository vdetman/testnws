<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="robots" content="noindex, nofollow" />
		<link rel="shortcut icon" href="/admin/images/favicon.png" />
		<title>{$layout->page()->getTitle()}</title>

		<!-- Google Fonts -->
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css' />

		<!--Icon fonts css-->
		<link href="/admin/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" />
		<link href="/admin/plugins/ionicon/css/ionicons.min.css" rel="stylesheet" />

		<!-- Bootstrap CSS -->
		<link href="/admin/css/bootstrap.min.css" rel="stylesheet" />
		<link href="/admin/css/bootstrap-reset.css" rel="stylesheet" />

		<!--Animation css-->
		<link href="/admin/css/animate.css" rel="stylesheet" />

		<!-- sweet alerts -->
		<link href="/admin/plugins/sweet-alert/sweet-alert.min.css" rel="stylesheet" />

		<link href="/admin/plugins/toggles/toggles.css" rel="stylesheet" />

		<!-- Custom styles -->
		<link href="/admin/css/style.css" rel="stylesheet" />
		<link href="/admin/css/helper.css{Func::modifyTime('/admin/css/helper.css')}" rel="stylesheet" />
		<link href="/admin/css/style-responsive.css" rel="stylesheet" />
		<link href="/admin/plugins/timepicker/bootstrap-datepicker.min.css" rel="stylesheet" />
		<link href="/admin/plugins/notifications/notification.css" rel="stylesheet" />
		<link href="/admin/css/default.css{Func::modifyTime('/admin/css/default.css')}" rel="stylesheet" />

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
		<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<script src="js/respond.min.js"></script>
		<![endif]-->

		{if isset($addCss) && is_array($addCss)}
			{foreach from=$addCss item='file'}
				<link rel="stylesheet" type="text/css" media="screen" href="{$file}" />
			{/foreach}
		{/if}

		<!-- Basic Plugins -->
		<script type="text/javascript" src="/admin/js/jquery-2.1.4.min.js"></script>
		<script type="text/javascript" src="/admin/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="/admin/js/modernizr.min.js"></script>
		<script type="text/javascript" src="/admin/js/pace.min.js"></script>
		<script type="text/javascript" src="/admin/js/wow.min.js"></script>
		<script type="text/javascript" src="/admin/js/jquery.scrollTo.min.js"></script>
		<script type="text/javascript" src="/admin/js/jquery.nicescroll.js"></script>
		<script type="text/javascript" src="/admin/js/jquery.cookie.js"></script>
		<script type="text/javascript" src="/admin/plugins/chat/moment-2.2.1.js"></script>
		<script type="text/javascript" src="/admin/plugins/toggles/toggles.min.js"></script>
		<script type="text/javascript" src="/admin/plugins/timepicker/bootstrap-datepicker.js"></script>

		<!-- Clockpicker Plugin -->
		<link href="/admin/plugins/clockpicker/clockpicker.css" rel="stylesheet" />
		<link href="/admin/plugins/clockpicker/standalone.css" rel="stylesheet" />
		<script type="text/javascript" src="/admin/plugins/clockpicker/clockpicker.js"></script>

		<script type="text/javascript">
		$(document).ready(function(){
			$('.period input').datepicker({
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true,
				weekStart: 1
			});
			$('.datepicker').datepicker({
				autoclose: true,
				format: "dd.mm.yyyy",
				todayHighlight: true,
				weekStart: 1
			});
		});
		</script>

		<!-- Notification -->
		<script src="/admin/plugins/notifications/notify.min.js"></script>
		<script src="/admin/plugins/notifications/notify-metro.js"></script>
		<script src="/admin/plugins/notifications/notifications.js"></script>

		<script type="text/javascript">$.admin = '{ADMIN}';</script>
		<script type="text/javascript">$.module = '{$module}';</script>

		<script type="text/javascript" src="/admin/js/scripts.js{Func::modifyTime('/admin/js/scripts.js')}"></script>

		<!-- Counter up -->
		<script type="text/javascript" src="/admin/js/waypoints.min.js"></script>
		<script type="text/javascript" src="/admin/js/jquery.counterup.min.js"></script>

		<!-- Sweet Alerts -->
		<script type="text/javascript" src="/admin/plugins/sweet-alert/sweet-alert.min.js"></script>

		{if isset($addJs) && is_array($addJs)}
			{foreach from=$addJs item='file'}
				<script type="text/javascript" src="{$file}"></script>
			{/foreach}
		{/if}

	</head>
	<body>
		<div id="ajax_loader"><img src="/admin/images/loading.gif" /></div>

		<!-- Header -->
		<header class="top-head container-fluid navbar-fixed-top">
			<!-- logo -->
			<div class="logo hidden-xs">
				<a href="/{ADMIN}" class="logo-expanded"> <img src="/admin/images/favicon.png" alt="logo" /> <span class="nav-text" style="{if $menuCollapsed}display: none;{else}display: inline;{/if}">Admin</span> </a>
			</div>
			<!-- end logo -->
			<button type="button" class="navbar-toggle pull-left">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-toggle ion-navicon-round"></span>
			</button>

			<!-- Search -->
			<form role="search" method="get" action="/{ADMIN}/search" onsubmit="return $('#search_query').val().length >= 2;" class="navbar-left app-search pull-left hidden-xs">
				<input type="text" name="q" id="search_query" value="{if isset($searchQuery)}{$searchQuery}{/if}" placeholder="Найти..." class="form-control" />
			</form>
			<!-- End Search -->

			<!-- Right navbar -->
			<ul class="list-inline navbar-right top-menu top-right-menu">
				<li><a class="dropdown-toggle" href="/" target="_blank" title="Перейти на сайт"><i class="fa fa-desktop"></i></a></li>
				<li><a class="dropdown-toggle" href="#" onclick="clearCache(); return false;" title="Сбросить КЭШ"><i class="fa fa-repeat"></i></a></li>
				{if isset($unreaded.total) && $unreaded.total}
				<!-- Notification -->
				<li class="dropdown">
					<a data-toggle="dropdown" class="dropdown-toggle" href="javascript:void(0);"> <i class="fa fa-bell-o"></i> <span class="badge badge-sm up bg-pink count">{$unreaded.total}</span> </a>
					<ul class="dropdown-menu extended fadeInUp animated nicescroll" tabindex="5002">
						<li class="noti-header">
							<p>Уведомления</p>
						</li>
						{if !empty($unreaded.tickets)}
						<li>
							<a href="/{ADMIN}/tickets"> <span class="pull-left"><i class="fa fa-support fa-2x text-success"></i></span> <span>Обратная связь
								<br/>
								<small class="text-muted">{$unreaded.tickets} {Helper\Text::getEnding($unreaded.tickets, 'обращение', 'обращения', 'обращений')}</small></span> </a>
						</li>
						{/if}
					</ul>
				</li>
				<!-- End Notification -->
				{/if}

				<!-- User Menu Dropdown -->
				<li class="dropdown text-center">
					<a data-toggle="dropdown" class="dropdown-toggle" href="javascript:void(0);"> <span class="username">{$currentUser->getName()}</span> <span class="caret"></span> </a>
					<ul class="dropdown-menu extended pro-menu fadeInUp animated" tabindex="5003" style="overflow: hidden; outline: none;">
						<li>
							<a href="/{ADMIN}/logout"><i class="fa fa-sign-out"></i> Выход</a>
						</li>
					</ul>
				</li>
				<!-- End User Menu Dropdown -->
			</ul>
			<!-- End Right Navbar -->

		</header>
		<!-- End Header -->

		<!-- Aside Menu -->
		<aside class="left-panel {if $menuCollapsed}collapsed{/if}">

			<!-- Navbar -->
			<nav class="navigation">
				{include file="../_menu/menu.tpl"}
			</nav>
			<!-- End Navbar -->

		</aside>
		<!-- End Aside -->

		<section class="content">
			<!-- Page Content -->
			<div class="wraper container-fluid">
				<div class="page-title">
					<h3 class="title">{$layout->page()->getHeader()}</h3>
				</div>
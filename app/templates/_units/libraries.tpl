<!DOCTYPE HTML>
<html lang="ru">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />

		<title>{$layout->page()->getTitle()}</title>
		<link rel="shortcut icon" href="/images/favicon.jpg" />
		<meta name="robots" content="{$layout->page()->getRobots()}" />
		<meta name="keywords" content="{$layout->page()->getKeywords()}" />
		<meta name="description" content="{$layout->page()->getDescription()}" />
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

		{if isset($addMeta) && is_array($addMeta)}
			{foreach from=$addMeta item='tag'}
				{$tag}
			{/foreach}
		{/if}
		
		{if isset($canonical) && $canonical}
				<link rel="canonical" href="{$canonical}" />
		{/if}
		
    <link rel="stylesheet" type="text/css" media="screen" href="{Func::withModifyTime('/css/style.css')}" />
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
		
		{if isset($addCss) && is_array($addCss)}
			{foreach from=$addCss item='file'}
				<link rel="stylesheet" type="text/css" media="screen" href="{$file}" />
			{/foreach}
		{/if}

		<script type="text/javascript" src="/js/jquery-2.1.4.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
		<script type="text/javascript" src="{Func::withModifyTime('/js/scripts.js')}"></script>
		
		{if isset($addJs) && is_array($addJs)}
			{foreach from=$addJs item='file'}
				<script type="text/javascript" src="{$file}"></script>
			{/foreach}
		{/if}

	</head>
	<body>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="{PAGELANG}"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="{PAGELANG}"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="{PAGELANG}"> <![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="{PAGELANG}"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>{PAGETITLE}</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="stylesheet" href="/css/main.css">


		{LAYOUTMODE_STARTSCRIPT}
		{IF (!{LAYOUTMODE})}
		<!-- Add javascript libraries here -->
		{ENDIF}
	</head>
	<body>
		<nav class="main-nav">
			<ul>
				{LOOP NAVIGATION(1)}
					<li><a href="{NAVIGATION:link}" class="">{NAVIGATION:title}</a></li>
				{ENDLOOP NAVIGATION}
			</ul>
		</nav>

		<div class="main-content">
			{LOOP CONTENT(1)}{ENDLOOP CONTENT}
		</div>

		{LAYOUTMODE_ENDSCRIPT}
	</body>
</html>

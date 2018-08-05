<!doctype html>
<html lang="{PAGELANG}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Hautliebe | Haarentfernung, Falten- und Narbenbehandlung</title>
	<meta name="description" content="{PAGEVAR:cmt_meta_description:recursive}">
	<meta name="keywords" content="{PAGEVAR:cmt_meta_keywords:recursive}">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	{IF (!{LAYOUTMODE})}
		{INCLUDE:PATHTOWEBROOT . "templates/partials/pace.tpl"}
	{ENDIF}

	<link rel="shortcut icon" href="/dist/img/favicon.png" />
	<link rel="stylesheet" type="text/css" href="/dist/css/vendor/flickity.min.css" />
	<link rel="stylesheet" type="text/css" href="/dist/css/vendor/leaflet.css" />
	<link rel="stylesheet" type="text/css" href="/dist/css/main.css" />

	{INCLUDE:PATHTOWEBROOT . "templates/partials/microdata.tpl"}
	{LAYOUTMODE_STARTSCRIPT}
	{IF (!{LAYOUTMODE})}
	<script src="/dist/js/vendor/jquery.min.js" defer></script>
	<script src="/dist/js/vendor/TweenMax.js" defer></script>
	<script src="/dist/js/vendor/ScrollMagic.min.js" defer></script>
	<script src="/dist/js/vendor/animation.gsap.min.js" defer></script>
	<script src="/dist/js/vendor/flickity.pkgd.min.js" defer></script>
	<script src="/dist/js/vendor/leaflet.js" defer></script>
	<script src="/dist/js/vendor/snap.svg-min.js" defer></script>

	<!-- They belong together: jquery.event.move.js and jquery.twentytwenty.js... -->
	<!-- <script src="/dist/js/vendor/jquery.event.move.js" defer>defer</script> -->
	<!-- <script src="/dist/js/vendor/jquery.twentytwenty.js"></script> -->

	{ENDIF}
</head>
<body id="top">
	<!-- Inject SVG sprites -->
	<div style="visibility: hidden; height:0; overflow: hidden">
		<object 
			type="image/svg+xml" 
			data="/dist/img/icons.svg" 
			onload="this.parentNode.replaceChild(this.getSVGDocument().childNodes[0], this)">
		</object>
	</div>

	{INCLUDE:PATHTOWEBROOT.'templates/partials/header.tpl'}

	<div class="main-content">

		<section id="news" class="section section--1">
			<div class="outer-bound">
				{LOOP CONTENT(99)}{ENDLOOP CONTENT}

				<img class="eyecatcher eyecatcher--discount" src="/dist/img/eyecatcher_discount.svg" title="-15 % Rabatt - nur für kurze Zeit" alt="Banner zur Neueröffnungsaktion mit der Aufschrift: 15 % Rabatt - nur für kurze Zeit" />
			</div>
		</section>


		<section id="treatments" class="section section--2 section--dark section--rotated-title">
			<!-- <div class="inner&#45;bound"> -->
			<!-- 	<div class="compare"> -->
			<!-- 		<img src="/dist/img/wrinkles&#45;before&#45;1.jpg" alt="" /> -->
			<!-- 		<img src="/dist/img/wrinkles&#45;after&#45;1.jpg" alt="" /> -->
			<!-- 	</div> -->
			<!-- </div> -->
			<div class="inner-bound">
				{LOOP CONTENT(1)}{ENDLOOP CONTENT}
			</div>
		</section>

		<section id="techniques" class="section section--3">
			<div class="inner-bound">
				{LOOP CONTENT(2)}{ENDLOOP CONTENT}
			</div>
		</section>

		<section id="about" class="section section--4">
			<div class="outer-bound">
				{LOOP CONTENT(3)}{ENDLOOP CONTENT}
			</div>
		</section>

		<section id="pricing" class="section section--5 section--dark section--rotated-title">
			<div class="inner-bound">
				{LOOP CONTENT(4)}{ENDLOOP CONTENT}
				<img class="eyecatcher eyecatcher--freebie" src="/dist/img/eyecatcher_freebie.svg" title="Kostenlos: Erstberartungsgespräch und Testbehandlung (nicht für Sublative)" alt="Banner mit der Aufschrift: Kostenlos: Erstberatungsgespräch und Testbehandlung (nicht für Sublative)" />
			</div>
		</section>

		<section id="appointments" class="section section--6">
			<div class="inner-bound">
				{LOOP CONTENT(5)}{ENDLOOP CONTENT}
			</div>
		</section>

		<section id="contact" class="section section--7 section--dark">
			<div class="inner-bound">
				{LOOP CONTENT(6)}{ENDLOOP CONTENT}
			</div>
		</section>
	</div>

	{INCLUDE:PATHTOWEBROOT.'templates/partials/footer.tpl'}
	<div>
		<section id="legal" class="section section--8 section--hidden">
			<div class="inner-bound">

				{LOOP CONTENT(999)}{ENDLOOP CONTENT}
				
				<a href="#top">Nach oben</a>
			</div>

		</section>
	</div>

	{IF(!{LAYOUTMODE})}
		<script src="/dist/js/main.min.js" defer></script>
	{ENDIF}
	{LAYOUTMODE_ENDSCRIPT}
</body>
</html>

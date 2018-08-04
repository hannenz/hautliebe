<!doctype html>
<html lang="{PAGELANG}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Hautliebe</title>
	<meta name="description" content="{PAGEVAR:cmt_meta_description:recursive}">
	<meta name="keywords" content="{PAGEVAR:cmt_meta_keywords:recursive}">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	{IF (!{LAYOUTMODE})}
	<style type="text/css">
		.pace {
			-webkit-pointer-events: none;
			pointer-events: none;
			-webkit-user-select: none;
			-moz-user-select: none;
			user-select: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100vw;
			height: 100vh;
			background-color: #fff;
			z-index: 8000;
			transition: opacity 500ms ease-out;
			display: flex;
		}

		.pace-inactive {
			opacity: 0;
		}

		.pace .pace-progress {
			visibility: hidden;
			background: rgb(238, 208, 212);
			position: fixed;
			z-index: 2000;
			top: 0;
			left: 0%;
			width: 100%;
			height: 100vh;
		}
		.pace svg {
			margin: auto;
		}
	</style>
	<script src="/dist/js/vendor/pace.min.js"></script>
	<script>

		var intervalId;
		var heartPath;

		Pace.on('start', function () {
			paceElement = document.querySelector ('.pace');
			
			paceElement.innerHTML = '<svg width="100" height="100" viewBox="0 0 100 100" version="1.1"><path id="heart-path" style="fill:none;stroke:#fabfcd;stroke-width:11.94119644;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" d="M 25.588103,14.41136 C 35.780661,12.754519 45.854552,19.56175 49.99964,30.148367 54.144728,19.562496 64.218618,12.754519 74.411175,14.41136 c 11.72924,1.906113 19.164128,14.29063 16.605726,27.663271 -2.111353,15.192193 -40.607528,43.766728 -40.607528,43.766728 0,0 -37.719254,-28.574535 -41.4262494,-43.766728 C 6.4247218,28.70199 13.858864,16.317473 25.588103,14.41136 Z" id="path8" /></svg>';
			heartPath = document.getElementById('heart-path');
			heartPath.style.strokeDasharray = '256 256';
			heartPath.style.strokeDashoffset = 256;
			heartPath.style.transition = '150ms ease-out';
			intervalId = window.setInterval (function () {
				var perc = parseInt(paceElement.firstChild.getAttribute('data-progress')) / 100;
				heartPath.style.strokeDashoffset = 256 - (256 * perc);
			}, 50);
		});

		// Remove the pace overlay after it has done it's job...
		Pace.on('hide', function () {
			window.clearInterval (intervalId);
			heartPath.style.strokeDashoffset = 0;
			setTimeout (function () {
				// paceElement.parentNode.removeChild (paceElement);
			}, 500);
		});
	</script>
	{ENDIF}
	<link rel="shortcut icon" href="/dist/img/favicon.png" />
	<link rel="stylesheet" type="text/css" href="/dist/css/vendor/flickity.min.css" />
	<link rel="stylesheet" type="text/css" href="/dist/css/vendor/leaflet.css" />
	<link rel="stylesheet" type="text/css" href="/dist/css/main.css" />

	<!-- Schema.org JSON+LD -->
	<script type="application/ld+json">
		{
			"@context": "http://schema.org/",
			"@type": "WebSite",
			"name": "www.hautliebe-ulm.de",
			"url": "https://www.hautliebe-ulm.de"
		}
	</script>
	<script type="application/ld+json">
		{
			"@context": "http://schema.org/",
			"@type": "LocalBusiness",
			"name": "Hautliebe®",
			"legalName": "Hautliebe®",
			"description": "{PAGEVAR:cmt_meta_description",
			"url": "https://www.hautliebe-ulm.de",
			"email": "info@hautliebe-ulm.de",
			"telephone": "+49 (0)152 318 307 42",
			"address": {
				"@type": "PostalAddress",
				"addressLocality": "Ulm",
				"postalCode": "89073",
				"streetAddress": "Hafenbad 31"
			},
			"openingHours": [
				"Mo-Fr 10:00-18:00"
			],
			"geo": {
				"@type": "GeoCoordinates",
				"latitude": "48.40091",
				"longitude": "9.99367"
			},
			"areaServed": "Ulm, Neu-Ulm",
			"logo": "/dist/img/logo.svg"
		}
	</script>

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

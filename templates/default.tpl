<!doctype html>
<html class="no-js" lang="{PAGELANG}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Hautliebe</title>
	<meta name="description" content="{PAGEVAR:cmt_meta_description:recursive}">
	<meta name="keywords" content="{PAGEVAR:cmt_meta_keywords:recursive}">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="shortcut icon" href="/dist/img/favicon.png" />

	<link rel="stylesheet" type="text/css" href="/dist/css/vendor/flickity.min.css" />
	<link rel="stylesheet" type="text/css" href="/dist/css/vendor/leaflet.css" />
	<link rel="stylesheet" type="text/css" href="/dist/css/main.css" />

	{LAYOUTMODE_STARTSCRIPT}
	{IF (!{LAYOUTMODE})}
	<script type="text/javascript" src="/dist/js/vendor/modernizr.js"></script>
	<script src="/dist/js/vendor/ScrollMagic.min.js"></script>
	<script src="/dist/js/vendor/jquery.min.js"></script>
	<script src="/dist/js/vendor/jquery-scrollspy.min.js"></script>
	<script src="/dist/js/vendor/flickity.pkgd.min.js"></script>
	<script src="/dist/js/vendor/leaflet.js"></script>
	{ENDIF}
</head>
<body>
	<!-- Inject SVG sprites -->
	<object 
		type="image/svg+xml" 
		data="/img/icons.svg" 
		onload="this.parentNode.replaceChild(this.getSVGDocument().childNodes[0], this)">
	</object>

	{INCLUDE:PATHTOWEBROOT.'templates/partials/header.tpl'}

	<div id="top" class="main-content">

		<section id="news" class="section section--1">
			<div class="carousel">
				<div class="carousel__slide news-teaser">
					<div class="news-teaser__inner">
						<figure class="news-teaser__image">
							<img src="/media/mlog/hautliebe.svg" alt="" />
						</figure>
						<div class="news-teaser__body">
							<h2 class="news-teaser__title title"> Neueröffnung</h2>
							<h3 class="news-teaser__subheadline subheadline">01. Juli 2018 in Ulm</h3>
							<div class="news-teaser__text">
								Ich freue mich auf Ihr Kommen.
							</div>
						</div>
					</div>
				</div>
				<div class="carousel__slide news-teaser">
					<div class="news-teaser__inner">
						<figure class="news-teaser__image">
							<img src="/media/mlog/hautliebe.svg" alt="" />
						</figure>
						<div class="news-teaser__body">
							<h2 class="news-teaser__title title"> Was anderes</h2>
							<h3 class="news-teaser__subheadline subheadline">Wichtige Meldung</h3>
							<div class="news-teaser__text">
								Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod
								tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At
								vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren,
								no sea takimata sanctus est Lorem ipsum dolor sit amet.
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section id="treatments" class="section section--2 section--dark section--rotated-title">
			<div class="inner-bound">
				<!-- <h2 class="title">Behandlungs&#45;<br>angebot</h2> -->
				{LOOP CONTENT(1)}{ENDLOOP CONTENT}
			</div>
		</section>

		<section id="techniques" class="section section--3">
			<div class="inner-bound">
				<div class="bottom-aligned-columns">
					<div class="column">
						<h2 class="title">Verfahren</h2>
						<h3 class="headline headline--large">eTwo</h3>
						<p>
							Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod
							tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At
							vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren,
							no sea takimata sanctus est Lorem ipsum dolor sit amet.
						</p>
						<p>
							Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod
							tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At
							vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren,
							no sea takimata sanctus est Lorem ipsum dolor sit amet.
						</p>
					</div>
					<div class="column">
						<h3 class="headline headline--large">&amp;<br>GentleLase Pro</h3>
						<p>
							Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod
							tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At
						</p>
						<p>
							Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod
							tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At
							vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren,
							no sea takimata sanctus est Lorem ipsum dolor sit amet.
						</p>
						<p>
							Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod
							tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At
							vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren,
							no sea takimata sanctus est Lorem ipsum dolor sit amet.
						</p>
					</div>
				</div>
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
				<h2 class="title">Preise &<br>Angebote</h2>
				<p>
					Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod
					tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At
					vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren,
					no sea takimata sanctus est Lorem ipsum dolor sit amet.
				</p>
				<p>
					Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod
					tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At
					vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren,
					no sea takimata sanctus est Lorem ipsum dolor sit amet.
				</p>
				{LOOP CONTENT(4)}{ENDLOOP CONTENT}
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

	{IF(!{LAYOUTMODE})}
		<script src="/dist/js/main.min.js"></script>
	{ENDIF}
	{LAYOUTMODE_ENDSCRIPT}
</body>
</html>

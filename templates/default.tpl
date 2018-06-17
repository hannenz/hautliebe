<!doctype html>
<html class="no-js" lang="{PAGELANG}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>{PAGETITLE} - </title>
	<meta name="description" content="{PAGEVAR:cmt_meta_description:recursive}">
	<meta name="keywords" content="{PAGEVAR:cmt_meta_keywords:recursive}">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="shortcut icon" href="/favicon.png" />

	<link rel="stylesheet" type="text/css" href="/dist/css/vendor/flickity.min.css" />
	<link rel="stylesheet" type="text/css" href="/dist/css/vendor/leaflet.css" />
	<link rel="stylesheet" type="text/css" href="/dist/css/main.css" />

	{LAYOUTMODE_STARTSCRIPT}
	{IF (!{LAYOUTMODE})}
	<script type="text/javascript" src="/dist/js/vendor/modernizr.js"></script>
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
				<h2 class="title">Behandlungs-<br>angebot</h2>

				<div class="tabs">

					<div class="tab__triggers">
						<label for="tab-1">Icon 1</label>
						<label for="tab-2">Icon 2</label>
						<label for="tab-3">Icon 3</label>
					</div>

					<div class="tabs__content">

						<input id="tab-1" type="radio" name="tabs">
						<div class="tab treatment" id="tab-1">
							<header class="treatment__header">
								<h4 class="kicker">Verfahren etwo</h4>
								<h2 class="headline">Faltenbehandlung, Hautverjüngung, Hautstraffungm die Behandlung von Augenringen</h2>
							</header>
							<div class="treatment__body">
								<p>Sie wünschen sich ein jüngeres Aussehen und möchten der Faltenbildung auf natürliche Art entgegenwirken? Ohne dass Präparate in die Haut injiziert werden?  Dann ist die Behandlung mit dem eTwo genau das Richtige. Die Haut reduziert mit zunehmendem Alter die Kollagenbildung und schafft es nicht mehr die Haut straff und glatt zu halten. Durch das nicht vorhandene Kollagen und Elastin fällt die Haut ein und es entstehen Falten und Augenringe. Leider können da auch teure Feuchtigkeitscremes nur oberflächlich Abhilfe schaffen. Mit der einzigartigen Sublime- und Sublativemethode dringen wir in die tiefen Schichten der Epidermis ein und regen die Kollagen- und Elastinbildung auf ganz natürliche Weise an. Sichtbare Verbesserungen sind schon nach einer Behandlung möglich.</p>
								<p>Das Resultat: Die Hautstruktur wird verbessert und wirkt ebenmäßiger, Falten werden sichtbar aufgepolstert und Augenringe deutlich minimiert.</p>
								<p>Vorteile:</p>
								<ul class="bullets">
									<li>Für alle Hauttypen sicher und effektiv</li>
									<li>Glättet Falten jeglicher Art</li>
									<li>Kollagen und Elastin wird auf natürliche Weise aufgebaut</li>
									<li>Es wird kein Präparat in die Haut injiziert</li>
									<li>Für eine ebenmäßige Hautstruktur und eine jüngere Optik</li>
									<li>Schnell sichtbare Resultate, keine Ausfallzeit (2-3 Tage kein Make-up)</li>
									<li>Kurze Behandlungszeiten (typischerweise 20-40 Minuten)</li>
									<li>Kann im Gesicht und am Körper angewandt werden</li>
								</ul>
							</div>
						</div>

						<input id="tab-2" type="radio" name="tabs">
						<div class="tab treatment" id="tab-2">
							Inhalt Tab 2
						</div>

						<input id="tab-3" type="radio" name="tabs">
						<div class="tab treatment" id="tab-2">
							Inhalt Tab 3
						</div>
					</div>
				</div>
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
			<div class="inner-bound">
				<div class="about">
					<figure class="about__image">
						<img src="" alt="Finja Kessler" />
					</figure>
					<div class="about__text">
						<h2 class="title">Über mich</h2>
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
					<div class="about__timeline">
						<ul class="timeline">
						<li class="timeline__item"> Seit 2009 ausgelernte Orthopädietechnikerin </li>
						<li class="timeline__item">Seit 2007 zertifiziert im Bereich Nachsorgevon Narbengewebe</li>
						<li class="timeline__item">Seit 2018 ausgelernte Laserschutzbeauftragte</li>
						<li class="timeline__item">Seit 2018 Geschäfthtsinhaberin von Hautliebe</li>
					</div>
				</div>
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
				<h2 class="title">Termin nach Vereinbarung</h2>
				<p>
					Sie erreichen mich<br>
					Mo bis Fr: 8.00 — 19.00 Uhr<br>
					Sa: 8.00 - 13:00 Uhr
				</p>
				<p>
					<strong class="big">0152&thinsp;&ndash;&thinsp;3183074
				</p>
				<p>
					Oder per Mail <a href="mailto:info@hautliebe-ulm.de">info@hautliebe-ulm.de</a>
				</p>

				{LOOP CONTENT(5)}{ENDLOOP CONTENT}

				<div class="container-break">
					<div id="map"></div>
				</div>
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

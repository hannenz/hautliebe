<div class="tabs">
	<nav class="tabs__triggers">
		<a class="tabs__trigger" href="#price-offers">
			<span class="tabs__label" title="Angebote">
				{INCLUDE:PATHTOWEBROOT . "dist/img/icon_offers.svg"}
			</span>
			<!-- Angebote -->
		</a>
		{LOOP VAR(categories)}
			<a class="tabs__trigger" href="#price-category-{VAR:id}">
				<span class="tabs__label" title="{VAR:treatment_name}">
				{SWITCH ({VAR:treatment_icon})}
					{CASE (icon_wrinkles)}
						{INCLUDE: PATHTOWEBROOT . "dist/img/icon_wrinkles.svg"}
					{BREAK}
					{CASE (icon_scars)}
						{INCLUDE: PATHTOWEBROOT . "dist/img/icon_scars.svg"}
					{BREAK}
					{CASE (icon_epilation)}
						{INCLUDE: PATHTOWEBROOT . "dist/img/icon_epilation.svg"}
					{BREAK}
				{ENDSWITCH}
				</span>
				<!-- {VAR:treatment_shortname} -->
			</a>
		{ENDLOOP VAR}
	</nav>
	
	<div class="tabs__panels">
		<div id="price-offers" class="tabs__panel">
			<h2 class="headline">Angebote</h2>
			<ul class="offers">

				{LOOP VAR (offers)}

					<li class="offers__item">
						<div class="offer">
							<h2 class="offer__title subheadline">{VAR:offer_title}</h2>
							<div class="offer__body">{VAR:offer_text}</div>
						</div>
					</li>
				{ENDLOOP VAR}
			</ul>
		</div>

		{LOOP VAR(categories)}
			<div id="price-category-{VAR:id}" class="tabs__panel">

				<h2 class="headline">{VAR:treatment_name}</h2>
				<p>Sollte Ihr Bereich nicht aufgelistet sein, mache ich Ihnen gerne ein individuelles Angebot.</p>

				<table class="price-table">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>Einzel</th>
							<th>Einzelpreis im Paket<br>({VAR:treatment_package_size}&nbsp;Sitzungen)</th>
						</tr>
					</thead>
					<tbody>
						{LOOP VAR(prices)}
							<tr>
								<td class="price__title"><span>{VAR:price_title}{IF("{VAR:price_gender}" != "all")} <span class="price__gender">({VAR:price_gender_fmt})</span>{ENDIF}</span></td>
								<td class="price__price price__price--single"><span>{IF("{VAR:price_single}" != "0")}{VAR:price_single_fmt}&nbsp;&euro;{ELSE}nach Angebot{ENDIF}</span></td>
								<td class="price__price price__price--bundle"><span>{IF("{VAR:price_bundle}" != "0")}{VAR:price_bundle_fmt}&nbsp;&euro;{ELSE}nach Angebot{ENDIF}</span></td>
							</tr>
						{ENDLOOP VAR}
					</tbody>
				</table>
			</div>
		{ENDLOOP VAR}
	</div>
</div>

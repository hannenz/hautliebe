<div class="tabs">
	<nav class="tabs__triggers">
		<a class="tabs__trigger" href="#price-offers">Angebote</a>
		{LOOP VAR(categories)}
			<a class="tabs__trigger" href="#price-category-{VAR:id}">{VAR:treatment_name}</a>
		{ENDLOOP VAR}
	</nav>
	
	<div class="tabs__panels">
		<div id="price-offers" class="tabs__panel">
			<ul class="offers">
				<li class="offers__item">
					<div class="offer">
						<h2 class="offer__title headline">Kaffekränzchen</h2>
						<div class="offer__body"> Excepteur ea ex commodo irure aute, nostrud qui eiusmod dolor dolor do in in. In elit dolor in in aliquip commodo consectetur eiusmod in officia culpa adipisicing deserunt. Ut Lorem est deserunt exercitation veniam anim amet quis amet in cupidatat laborum quis. Ea in cupidatat ex id incididunt est eu fugiat in. Do mollit deserunt culpa anim ex pariatur do sed Lorem ullamco quis nulla aliquip. Eu cillum esse consequat ex elit ex, cupidatat proident. Id do culpa anim dolor ex. Occaecat ad esse minim non adipisicing in cupidatat. Mollit do amet ad consectetur fugiat, id amet ut in.</div>
					</div>
				</li>
			</ul>
		</div>

		{LOOP VAR(categories)}
			<div id="price-category-{VAR:id}" class="tabs__panel">

				<h3 class="headline">{VAR:treatment_name}</h3>
				<p>Sollte Ihr Bereich nicht aufgelistet sein, mache ich Ihnen gerne ein individuelles Angebot</p>

				<table class="price-table">
					<thead>
						<tr>
							<td></td>
							<td>Einzel</td>
							<td>Einzelpreis im Paket<br>({VAR:treatment_package_size} Sitzungen)</td>
					</thead>
					<tbody>
						{LOOP VAR(prices)}
							<tr>
								<td class="price__title"><span>{VAR:price_title}{IF("{VAR:price_gender}" != "all")} <span class="price__gender">({VAR:price_gender_fmt})</span>{ENDIF}</span></td>
								<td class="price__price price__price--single"><span>{VAR:price_single_fmt} &euro;</span></td>
								<td class="price__price price__price--bundle"><span>{VAR:price_bundle_fmt} &euro;</span></td>
							</tr>
						{ENDLOOP VAR}
					</tbody>
				</table>
			</div>
		{ENDLOOP VAR}
	</div>
</div>

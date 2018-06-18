{IF({LAYOUTMODE})}
	<div>
		<h3>Kartenansicht</h3>
		<div>
			<label>Breitengrad (Längengrad)</label>
			<div>{HEAD:1}</div>
			<label>Längengrad (Longitude)</label>
			<div>{HEAD:2}</div>
			<label>Zoom (1-17)</label>
			<div>{HEAD:3}</div>
		</div>
	</div>
{ELSE}
<div class="container-break">
	<div id="map" data-latitude="{HEAD:1}" data-longitude="{HEAD:2}" data-zoom="{HEAD:3}"></div>
</div>
{ENDIF}


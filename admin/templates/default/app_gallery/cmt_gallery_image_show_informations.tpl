<div id="cmtImageInformationsContainer">
    <h3><a href="#">Bildtexte</a></h3>
    <div>
    	<table summary="image text">
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Titel: </span></td>
    			<td class="propertyValue">{VAR:gallery_image_title}</td>
    		</tr>
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Bildbeschreibung: </span></td>
    			<td class="propertyValue">{VAR:gallery_image_description}</td>
    		</tr>
    	</table>
    </div>
    <h3><a href="#">Datei-Informationen</a></h3>
    <div>
    	<table summary="file informations">
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Dateiname: </span></td>
    			<td class="propertyValue">{VAR:gallery_image_file}</td>
    		</tr>
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Dateipfad: </span></td>
    			<td class="propertyValue">{VAR:imagesBasePath}{VAR:gallery_image_internal_filename}</td>
    		</tr>
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Gr&ouml;&szlig;e: </span></td>
    			<td class="propertyValue">{VAR:fileSizeKiloBytes} KB</td>
    		</tr>
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Datei zuletzt ge&auml;ndert: </span></td>
    			<td class="propertyValue">{VAR:fileDate}, {VAR:fileTime} Uhr</td>
    		</tr>
    	</table>
    </div>
    <h3><a href="#">Bild-Informationen</a></h3>
    <div>
    	<table summary="image informations">
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Bildformat: </span></td>
    			<td class="propertyValue">{VAR:imageType} ({VAR:mimeType})</td>
    		</tr>
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Bild aufgenommen: </span></td>
    			<td class="propertyValue">{VAR:imageDate}, {VAR:imageTime} Uhr</td>
    		</tr>
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Dimensionen (B x H, Pixel): </span></td>
    			<td class="propertyValue">{VAR:width} x {VAR:height}</td>
    		</tr>
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Farbformat: </span></td>
    			<td class="propertyValue">{IF ("{VAR:isColor}" == "1")}Farbe{ELSE}s/w{ENDIF}</td>
    		</tr>
    	</table>
    </div>
    <h3><a href="#">weitere Bild-Informationen</a></h3>
    <div>
    	<table summary="additional image informations">
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Kamera: </span></td>
    			<td class="propertyValue">{VAR:cameraType} {VAR:cameraModel}</td>
    		</tr>
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Blende: </span></td>
    			<td class="propertyValue">{VAR:apertureValue} (APEX: {VAR:apertureValueApex})</td>
    		</tr>
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Brennweite: </span></td>
    			<td class="propertyValue">{VAR:focalLength} mm</td>
    		</tr>
    	</table>
    </div>
    <h3><a href="#">GPS-Angaben</a></h3>
    <div>
    	{IF ({ISSET:hasGPS:VAR})}
    	<table summary="additional image informations">
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Breitengrad: </span></td>
    			<td class="propertyValue">{VAR:latitude}</td>
    		</tr>
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">L&auml;ngengrad: </span></td>
    			<td class="propertyValue">{VAR:longitude}</td>
    		</tr>
    		<tr>
    			<td class="propertyName"><span class="cmtServiceTextSpecial">Koordinaten: </span></td>
    			<td class="propertyValue">{VAR:coordinates}</td>
    		</tr>
    	</table>
    	{ELSE}
    	<p>
    		Keine GPS-Daten in der Bilddatei verf&uuml;gbar.
    	</p>
    	{ENDIF}
    </div>
</div>
<script type="text/javascript">
	jQuery(function() {
		$( "#cmtImageInformationsContainer" ).accordion();
	});
</script>

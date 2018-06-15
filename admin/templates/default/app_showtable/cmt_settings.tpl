<form name="editSettings" action="{SELFURL}" method="POST" enctype="multipart/form-data">
	<div class="editEntrySubheadline">Tabellenlayout</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Icon</div>
		<div class="cmtEditRowField">
			<input class="cmtFormFile" type="text" name="cmtsetting_icon_path" id="cmtsetting_icon_path" value="{VAR:thumbnails_base_path}" />&nbsp;
			<a href="{SELFURL}&amp;action={VAR:action}&amp;cmt_extapp=fileselector" data-field="cmtsetting_icon_path" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector">Datei wählen</a>
		</div>
		<div class="cmtEditRowDescription">
			Hier kann ein individuelles Icon für die Tabelle ausgewählt werden.
		</div>
	</div>
	<div class="editEntrySubheadline">Such- und Sortierfeldeinstellungen</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Sortierung</div>
		<p>
			Anzahl der Auswahlfelder f&uuml;r die Sortierreihenfolgen:&nbsp;&nbsp;<input type="text" name="cmtsetting_sort_fields" class="small" value="{VAR:sort_fields}"/>
		</p>
		<p>
			Anzahl der Auswahlfelder f&uuml;r die Sortierrichtung:&nbsp;&nbsp;<input type="text" name="cmtsetting_sort_directions" class="small" value="{VAR:sort_directions}"/>
		</p>		
		<p>
			<input type="checkbox" id="cmtsetting_sort_aliases" name="cmtsetting_sort_aliases" value="1" {IF ({ISSET:sort_aliases:VAR})}checked="checked"{ENDIF}>
			<label for="cmtsetting_sort_aliases">Sortierfelder: Aliase f&uuml;r MySQL-Feldnamen anzeigen?</label>
		</p>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Suche in Tabellendaten</div>
		<p>
			Anzahl der Suchfelder:&nbsp;&nbsp;<input type="text" name="cmtsetting_search_fields" class="small" value="{VAR:search_fields}"/>
		</p>
		<p>
			<input type="checkbox" name="cmtsetting_search_aliases" id="cmtsetting_search_aliases" value="1" {IF ({ISSET:search_aliases:VAR})}checked="checked"{ENDIF}>
			<label for="cmtsetting_search_aliases">Suchfelder: Aliase f&uuml;r MySQL-Feldnamen anzeigen?</label>
		</p>
	</div>
	<div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Einträge pro Seite / Paging</div>
		<p>
			Anzahl der Eintr&auml;ge pro Seite: <input type="text" name="cmtsetting_show_ippnumber" class="small" value="{VAR:show_ippnumber}" />
		</p>
		<p>
			<input type="checkbox" name="cmtsetting_show_ipp" id="cmtsetting_show_ipp" value="1" {IF ({ISSET:show_ipp:VAR})}checked="checked"{ENDIF}>
			<label for="cmtsetting_show_ipp">Feld 'Anzahl der Eintr&auml;ge pro Seite ausw&auml;hlen' anzeigen</label>
		</p>
		<p>
			<input type="checkbox" id="cmtsetting_show_pageselect" name="cmtsetting_show_pageselect" value="1" {IF ({ISSET:show_pageselect:VAR})}checked="checked"{ENDIF} />
			<label for="cmtsetting_show_pageselect">Links zu Folgeseiten anzeigen, wenn mehr Suchtreffer als pro Seite erlaubt</label>
		</p>
		<p>
			<input type="checkbox" name="cmtsetting_show_iteminfos" id="cmtsetting_show_iteminfos" value="1" {IF ({ISSET:show_iteminfos:VAR})}checked="checked"{ENDIF} />
			<label for="cmtsetting_show_iteminfos">Information anzeigen, wieviel Eintr&auml;ge ausgew&auml;hlt sind</label>
		</p>
	</div>

	<!--  script includes -->
	<div class="editEntrySubheadline">Include-Dateien</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Tabellenübersicht</div>
		<div class="cmtEditRowField">
			<input class="cmtFormFile" type="text" name="cmtsetting_cmt_include_overview" id="cmtsetting_cmt_include_overview" value="{VAR:cmt_include_overview}" />&nbsp;
			<a href="{SELFURL}&amp;action={VAR:action}&amp;cmt_extapp=fileselector" data-field="cmtsetting_cmt_include_overview" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector">Datei wählen</a>
		</div>
		<div class="cmtEditRowDescription">
			Diese Datei wird in der Tabellenübersicht eingebunden und ausgeführt.
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Detailansicht</div>
		<div class="cmtEditRowField">
			<input class="cmtFormFile" type="text" name="cmtsetting_cmt_include_edit" id="cmtsetting_cmt_include_edit" value="{VAR:cmt_include_edit}" />&nbsp;
			<a href="{SELFURL}&amp;action={VAR:action}&amp;cmt_extapp=fileselector" data-field="cmtsetting_cmt_include_edit" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector">Datei wählen</a>
		</div>
		<div class="cmtEditRowDescription">
			Diese Datei wird in der Detailansicht eines Eintrags eingebunden und ausgeführt.
		</div>
	</div>

	<!-- media settings -->
	<div class="editEntrySubheadline">Tabellenmedien</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<p class="cmtEditRowDescription">
			Damit diese Einstellungen wirksam werden, müssen die Skripte zur Verwaltung von Tabellenmedien im Reiter "Tabellenstruktur" sowohl in die Übersicht als auch die Detailansicht der Tabelle eingebunden sein!
		</p>
		<div class="cmtEditRowHead">Zielordner für Bilddateien</div>
		<div class="cmtEditRowField">
			<input class="cmtFormFile" type="text" name="cmtsetting_media_image_path" id="cmtsetting_media_image_path" value="{VAR:media_image_path}" />&nbsp;
			<a href="{SELFURL}&amp;action={VAR:action}&amp;cmt_extapp=fileselector" data-field="cmtsetting_media_image_path" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector">Datei wählen</a>
		</div>
		<div class="cmtEditRowDescription">
			Hier kann ein Ordner ausgewählt werden, in den die hochgeladenen Bilddateien dieser Tabelle gespeichert werden.
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Dimensionen für Vorschaubilder (Thumbnails): Breite x Höhe</div>
		<div class="cmtEditRowField">
			<input class="cmtFormFile small" type="text" name="cmtsetting_media_thumbnail_width" id="cmtsetting_media_thumbnail_width" value="{VAR:media_thumbnail_width}" />&nbsp;X
			<input class="cmtFormFile small" type="text" name="cmtsetting_media_thumbnail_height" id="cmtsetting_media_thumbnail_height" value="{VAR:media_thumbnail_height}" />
		</div>
		<div class="cmtEditRowDescription">
			Optional kann hier eine Breite und Höhe für das erzeugte Vorschaubild (Thumbnail) angegeben werden. Bitte tragen Sie die Breite und die Höhe in Pixel ohne Angabe der Einheit ein, z.B.: <code>280</code>
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Zielordner für Dokumente</div>
		<div class="cmtEditRowField">
			<input class="cmtFormFile" type="text" name="cmtsetting_media_document_path" id="cmtsetting_media_document_path" value="{VAR:media_document_path}" />&nbsp;
			<a href="{SELFURL}&amp;action={VAR:action}&amp;cmt_extapp=fileselector" data-field="cmtsetting_media_document_path" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector">Datei wählen</a>
		</div>
		<div class="cmtEditRowDescription">
			In den hier ausgewählten Ordner werden Dokumentdateien der Tabelle gespeichert.
		</div>
	</div>

	<!--  more options -->
	<div class="editEntrySubheadline">Weitere Optionen</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Datenbank-Abfrage anzeigen</div>
		<div class="cmtEditRowField">
			<input type="checkbox" name="cmtsetting_show_query" id="cmtsetting_show_query" value="1" {IF ({ISSET:show_query:VAR})}checked="checked"{ENDIF}>
			<label for="cmtsetting_show_query">aktuelle SQL-Abfrage (Query) anzeigen</label>
		</div>
		<div class="cmtEditRowDescription">
			Die aktuell ausgeführte Datenbankabfrage wird über der Datentabelle angezeigt.
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Button "neuer Eintrag" anzeigen</div>
		<div class="cmtEditRowField">
			<input type="checkbox" name="cmtsetting_add_item" id="cmtsetting_add_item" value="1" {IF ({ISSET:add_item:VAR})}checked="checked"{ENDIF} />
			<label for="cmtsetting_add_item">Button "Eintrag hinzuf&uuml;gen" anzeigen</label>
		</div>
		<div class="cmtEditRowDescription">
			Hier kann der Button zum Erstellen neuer Datensätze für alle Nutzer ausgeblendet werden.
		</div>
	</div>

	<div class="serviceContainer">
		<button class="cmtButton cmtButtonSave" name="action">speichern</button>
		<input type="hidden" name="sid" value="{SID}"/>
		<input type="hidden" name="cmt_slider" value="{VAR:cmt_slider}"/>
		<input type="hidden" name="id" value="{VAR:tableId}"/>
		<input type="hidden" name="iconPath" value="{VAR:iconPath}"/>	
		<input type="hidden" name="action" value="save"/>
	</div>
</form>
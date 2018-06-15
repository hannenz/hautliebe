<link rel="Stylesheet" href="templates/{CONSTANT:CMT_STYLE}app_paperboy/css/cmt_paperboy_style.css" type="text/css" />
<!-- messages -->
{IF ({ISSET:cmtError:VAR})}
<div class="cmtMessage cmtMessageError">
	{SWITCH ("{VAR:cmtErrorType}")}
		{CASE ("delete")}
			Der Abonnent konnte nicht gelöscht werden.
		{BREAK}

		{CASE ("edit")}
			Der Abonnent konnte nicht gespeichert werden.
		{BREAK}
t 		{CASE ("subscription")}
			Wegen eines Datenbankfehlers konnten die ausgewählten Newsletter nicht abonniert werden.
		{BREAK}
		{CASE ("import-no-upload")}
			Keine Datei zum hochladen ausgewählt
		{BREAK}
		{CASE ("import-illegal-upload")}
			Beim Upload der Datei ist ein Fehler aufgetreten.
		{BREAK}
		{CASE ("import-no-newsletter")}
			Sie müssen mindestens einen Newsletter auswählen
		{BREAK}
	{ENDSWITCH}
</div>
{ENDIF}

{IF ({ISSET:cmtSuccess:VAR})}
<div class="cmtMessage cmtMessageSuccess">
	{SWITCH ("{VAR:cmtSuccessType}")}
		{CASE ("delete")}
			Der Abonnent wurde erfolgreich gelöscht.
		{BREAK}
		
		{CASE ("edit")}
			Der Abonnent wurde erfolgreich gespeichert.
		{BREAK}
		{CASE ("subscription")}
			Der Abonnent und der / die ausgewählten Newsletter wurden erfolgreich gespeichert.
		{BREAK}
		{CASE ("import")}
			Alle {VAR:subscribersImported} Abonnenten wurden erfolgreich importiert.
		{BREAK}
	{ENDSWITCH}
</div>
{ENDIF}
{IF ({ISSET:cmtWarning:VAR})}
<div class="cmtMessage cmtMessageWarning">
	{SWITCH ("{VAR:cmtWarningType}")}
		
		{CASE ("newSubscription")}
			Es wurden keine Newsletterabonnements für den neuen Abonnenten ausgewählt!
		{BREAK}
		{CASE ("newSubscriberNotActive")}
			Der Status des neuen Abonnenten ist "nicht aktiv". Daher wurden keine Newsletterabonnements gespeichert!
		{BREAK}
		{CASE ("editSubscriberNotActive")}
			Der Status des neuen Abonnenten ist "nicht aktiv". Daher wurden etwaige Newsletterabonnements ebenfalls deaktiviert!
		{BREAK}
		{CASE ("import")}
			{VAR:subscribersImported} von {VAR:subscribersToImport} Abonnenten wurden erfolgreich importiert.
		{BREAK}
	{ENDSWITCH}
</div>
{ENDIF}

<!-- user quick select -->
<div class="serviceContainer clearfix">
	<div class="serviceContainerInner clearfix">
		<div class="serviceElementContainer">
			<form action="{SELFURL}&amp;cmtAction=" method="post">
				<span class="serviceText">Schnellsuche nach E-Mailadresse:</span><br />
				<input type="text" name="subscribersEmail" value="{VAR:subscribersEmail}" />
				<button type="submit" class="cmtButton cmtButtonSearch" value="">suchen</button> 
				<a href="{SELFURL}&amp;cmtAction=resetQuickSearch" class="cmtButton cmtButtonDelete">zur&uuml;cksetzen</a>
			</form>
		</div>
	</div>
</div>

<div class="cmtLayer">
	<div class="cmt-layer-icon cmt-font-icon cmt-icon-import"></div>
	<div class="cmtLayerHandle">
		<div class="cmt-layer-icon cmt-font-icon cmt-icon-import"></div>
		<div class="cmt-layer-open-close cmt-font-icon cmt-icon-open-close"></div>
		E-Mailadresen aus CSV importieren
	</div>

	<div class="cmtLayerContent">
		<form action="{SELFURL}&amp;cmtAction=importCSV" method="post" enctype="multipart/form-data">
			<div class="serviceContainer clearfix">
				<div class="serviceContainerInner clearfix">
					<div class="serviceHead">CSV-Datei</div>
					<div class="serviceElementContainer">
						<input class="cmtFormFile" type="text" id="csvfile" name="import_csv_file" />
						<input class="cmtFormCustomFileInput" type="file" id="csvfile" name="import_csv_file_newfile" />
					</div>
					<div class="lastServiceElementContainer">
						<input type="submit" class="cmtButton cmtButtonSave" title="Seiten exportieren" value="CSV Importieren" />
					</div>
				</div>
				<div class="serviceContainerInner clearfix">
					<div class="serviceHead">Optionen</div>
					<div class="serviceElementContainer">
						<label for="skipfirstline"><span class="serviceText">Erste Zeile überpringen</span></label>
						<input name="skipfirstline" id="skipfirstline" type="checkbox" checked />
					</div>
					<div class="serviceElementContainer">
						<label for="delimiter"><span class="serviceText">Trennzeichen</span></label>
						<input type="text" name="delimiter" id="delimiter" size="3" value=";" />
					</div>
					<div class="lastServiceElementContainer">
						<label for="enclosure"><span class="serviceText">Texteinschluss</span></label>
						<input type="text" name="enclosure" id="encloseure" size="3" value="&quot;" />
					</div>
				</div>
				<div class="serviceContainerInner clearfix">
					<div class="serviceHead">Newsletter abonnieren</div>
					{LOOP TABLE(paperboy_newsletters)}
					<input type="checkbox" name="newsletter[]" value="{FIELD:id}" id="newsletter-{FIELD:id}" /><label for="newsletter-{FIELD:id}">{FIELD:newsletter_name}</label><br>
					{ENDLOOP TABLE}
				</div>
				&nbsp;
			</div>
		</form>
	</div>
</div>

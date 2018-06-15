<div class="serviceContainer">
	<form id="selectTemplateFor" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">
		<span class="serviceText">Templates f&uuml;r</span>&nbsp;
		{VAR:selectTable}
		&nbsp;&nbsp;
		<button class="cmtButton cmtButtonEdit" type="submit">bearbeiten</button>
		<input type="hidden" name="action" value="edit" />
		<input type="hidden" name="sid" value="{SID}" />
		<input type="hidden" name="cmt_slider" value="{VAR:cmt_slider}" />
	</form>
</div>
{IF ("{VAR:messageType}" == "success")}
<div class="cmtMessage cmtMessageSuccess">
	Die Template-Einstellungen für den Eintrag <b>{VAR:cmt_tablename}</b> wurden erfolgreich gespeichert.
</div>
{ENDIF}
{IF ("{VAR:messageType}" == "error")}
<div class="cmtMessage cmtMessageError">
	Wegen eines Datenbankfehlers konnte der Eintrag nicht gespeichert werden! ({VAR:saveErrorText})
</div>
{ENDIF}
{IF ("{VAR:messageType}" == "info")}
<div class="cmtMessage cmtMessageInfo">

</div>
{ENDIF}
{IF ("{VAR:messageType}" == "warning")}
<div class="cmtMessage cmtMessageInfo cmtIgnore">
	Bitte wählen Sie einen Eintrag aus, dessen Template-Einstellungen bearbeitet werden sollen.
</div>
{ENDIF}

{IF ({ISSET:showTemplates:VAR})}
<div>
	<form name="selectTemplates" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">
		<h3 class="cmtSubheadline">Templatenutzung allgemein</h3>
		<div class="cmtEditRow cmtEditRow0">
			<div class="cmtEditRowField">
				<input id="dontUseTemplates" type="checkbox" value="1" name="dont_use_templates" {IF ({ISSET:dont_use_templates:VAR})} checked="checked"{ENDIF} />
				<label for="dontUseTemplates">keine Templates verwenden</label>
			</div>
			<div class="cmtEditRowDescription">
			 	Weder eigene noch Content-o-Mat Templates werden verwendet. Für eigene Skripte geeignet: Nur die vom Skript erzeugte Variable <code>$content</code> wird ausgegeben.
			 </div>
		</div>
		
		<!-- Übersichtsseite -->
		<h3 class="cmtSubheadline">Übersichtsseite</h3>
		<div class="cmtEditRow cmtEditRow1">
			<div class="cmtEditRowHead">
				Rahmentemplate
			</div>
			<div class="cmtEditRowField">
				<input type="text" name="overview_frame" value="{VAR:overview_frame}" />
				<a href="{SELFURL}&amp;cmt_extapp=fileselector&cmt_field=overview_frame" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector" title="Template ausw&auml;hlen" data-field="overview_frame">
	 				ausw&auml;hlen
	 			</a>
			</div>
			<div class="cmtEditRowDescription">
			 	Dieses Template wird für die Darstellung der gesamten Übersichtsseite verwendet. Die Tabellenreihen sind 
			 	über das Makro <code>&#123;VAR:cmtRowsContent&#125;</code> im Rahmentemplate verfügbar.
			 </div>
		</div>
		<div class="cmtEditRow cmtEditRow0">
			<div class="cmtEditRowHead">
				Reihentemplate
			</div>
			<div class="cmtEditRowField">
				<input type="text" name="overview_row" value="{VAR:overview_row}" />
				<a href="{SELFURL}&amp;cmt_extapp=fileselector&cmt_field=overview_row" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector" title="Template ausw&auml;hlen" data-field="overview_row">
	 				ausw&auml;hlen
	 			</a>
			</div>
			<div class="cmtEditRowDescription">
			 	Dieses Template wird für die Darstellung einer Tabellenreihe auf der Übersichtsseite verwendet.
			 </div>
		</div>
		
		<!-- Detailseite -->
		<h3 class="cmtSubheadline">Detailsseite</h3>
		<div class="cmtEditRow cmtEditRow1">
			<div class="cmtEditRowHead">
				Gesamttemplate
			</div>
			<div class="cmtEditRowField">
				<input type="text" name="edit_entry" value="{VAR:edit_entry}" />
				<a href="{SELFURL}&amp;cmt_extapp=fileselector&cmt_field=overview_frame" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector" title="Template ausw&auml;hlen" data-field="edit_entry">
	 				ausw&auml;hlen
	 			</a>
			</div>
			<div class="cmtEditRowDescription">
			 	Dieses Template wird für die Darstellung der gesamten Detailseite eines Eintrags verwendet.
			 </div>
		</div>
		
		<!-- Footer -->
		<div class="serviceContainer serviceFooter">
			<button class="cmtButton cmtButtonSave" type="submit">speichern</button>
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="sid" value="{SID}" />
			<input type="hidden" name="cmt_slider" value="{VAR:cmt_slider}" />
			<input type="hidden" name="id" value="{VAR:entryID}" />
		</div>
	</form>
</div>
{ENDIF}
<link rel="Stylesheet" href="{CMT_TEMPLATE}app_pages/css/app_pages_style.css" type="text/css">
<script type="text/javascript" src="{CMT_TEMPLATE}app_pages/javascript/cmt_languages_functions.js"></script>
<script type="text/javascript" src="{PATHTOADMIN}javascript/jquery-plugins/jquery-ui-customeffects/jquery.ui.effect-blind.js"></script>

<div class="appHeadlineContainer">
	<div class="appIcon"><img src="{IF ({ISSET:icon:VAR})}{VAR:icon}{ELSE}{CMT_TEMPLATE}app_pages/img/app_pages_icon.png{ENDIF}" alt="" /></div>
	<h1>Sprachversion bearbeiten - <span>neu erstellen</span></h1>
</div>

<!-- Meldung -->
<div id="cmtMessageContainer">
{IF ({ISSET:noLanguageNameError:VAR})}
	<div class="cmtMessage cmtMessageError error">
		Bitte geben Sie ein Sprachkürzel an!
	</div>
{ENDIF}
{IF ({ISSET:wrongLanguageNameError:VAR})}
	<div class="cmtMessage cmtMessageError error">
		Das Sprachkürzel enthält unerlaubte Zeichen! Erlaubte Zeichen sind: _, $, A bis Z, a bis z und 0 bis 9. 
	</div>
{ENDIF}
{IF ({ISSET:editLanguageTablesError:VAR})}
	<div class="cmtMessage cmtMessageError error">
		Eine der Sprachtabellen ({VAR:newTableName}) konnte nicht bearbeiten werden. 
	</div>
{ENDIF}
{IF ({ISSET:editLanguageSavingError:VAR})}
	<div class="cmtMessage cmtMessageError error">
		Der Spracheintrag konnte nicht gespeichert werden. (Datenbankfehler: "{VAR:dbError}")
	</div>
{ENDIF}
{IF ({ISSET:editLanguageOldDataError:VAR})}
	<div class="cmtMessage cmtMessageError error">
		Die Daten des urspr&uuml;nglichen Spracheintrags konnten nicht ermittelt werden. Dies deutet auf Inkonsistenzen oder Datenverlust in der Datenbanktabellenstruktur hin. Bitte kontaktieren Sie den Administrator.
	</div>
{ENDIF}


{IF ({ISSET:newLanguageError:VAR})}	
	<div class="cmtMessage cmtMessageError error">
		Die neue Sprachversion konnte nicht erzeugt werden. Es trat ein Fehler bei der 
		{IF ({ISSET:errorTablePages:VAR})} Tabelle für Seiten {ENDIF}
		{IF ({ISSET:errorTableContent:VAR})} Tabelle für die Inhalte {ENDIF}
		{IF ({ISSET:errorTableLinks:VAR})} Tabelle für Links {ENDIF}
		auf.
	</div>
{ENDIF}



</div>

<form class="cmtForm" name="cmtEditLanguageForm" id="cmtEditLanguageForm" action="{SELFURL}" method="POST" enctype="multipart/form-data">
	<div class="serviceContainerButtonRow">
		<a title="zurück" href="{SELFURL}&amp;cmtTab={VAR:cmtTab}&amp;cmtAction=showLanguages" class="cmtButton cmtButtonBack cmtButtonNoText"></a>
		<button type="submit" value="speichern" name="saveEntry" class="cmtButton cmtButtonSave">speichern</button>
	</div>	
	
	<!-- Eintrag -->
	{IF ("{VAR:action}" == "newLanguage" || "{VAR:action}" == "duplicateLanguage" || "{VAR:cmtAction}" == "newLanguage" || "{VAR:cmtAction}" == "duplicateLanguage")}
	<h3 class="editEntrySubheadline">Struktur und Inhalte kopieren</h3>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="editEntryRowField">
			<input type="radio" name="cmtCopyMode" {IF (!{ISSET:cmtCopyMode} || "{VAR:cmtCopyMode}" == "copyAll")}checked="checked"{ENDIF} id="cmtCopyAll" value="copyAll" /><label for="cmtCopyAll">Seiten und Seiteninhalte kopieren</label>
		</div>
		<div class="editEntryRowField">
			<input type="radio" name="cmtCopyMode" {IF ("{VAR:cmtCopyMode}" == "copyPages")}checked="checked"{ENDIF} id="cmtCopyPages" value="copyPages" /><label for="cmtCopyPages">nur Seiten kopieren</label>
		</div>
		<div class="editEntryRowField">
			<input type="radio" name="cmtCopyMode" {IF ("{VAR:cmtCopyMode}" == "copyNone")}checked="checked"{ENDIF} id="cmtCopyNone" value="copyNone" /><label for="cmtCopyNone">nur leere Sprachversion erstellen, weder Seiten noch Seiteninhalte kopieren</label><br />
		</div>
		<div id="copyFromLanguageContainer">
			<label for="cmtCopyFromLanguage">Kopieren von Sprachversion: </label>
			<select name="cmtCopyFromLanguage" id="cmtCopyFromLanguage">
				{VAR:selectCopyFromLanguage}
			</select>
		</div>
	</div>
	{ENDIF}
	<h3 class="editEntrySubheadline">Sprachversionsdaten</h3>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="editEntryRowHead">{VAR:cmt_languagename_alias}</div>
		<div class="editEntryRowField">
			{VAR:cmt_languagename}
		</div>
		<div class="editEntryRowDescription">
			{VAR:cmt_languagename_description}
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="editEntryRowHead">{VAR:cmt_language_alias}</div>
		<div class="editEntryRowField">
			{VAR:cmt_language}
		</div>
		<div class="editEntryRowDescription">
			{VAR:cmt_language_description}
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="editEntryRowHead">{VAR:cmt_charset_alias}</div>
		<div class="editEntryRowField">
			<select name="cmt_charset">
				{VAR:cmtCharset}
			</select>
		</div>
		<div class="editEntryRowDescription">
			{VAR:cmt_charset_description}
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="editEntryRowHead">{VAR:cmt_domain_id_alias}</div>
		<div class="editEntryRowField">
			{VAR:cmt_domain_id}
		</div>
		<div class="editEntryRowDescription">
			{VAR:cmt_domain_id_description}
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="editEntryRowHead">{VAR:cmt_position_alias}</div>
		<div class="editEntryRowField">
			{VAR:cmt_position}
		</div>
		<div class="editEntryRowDescription">
			{VAR:cmt_position_description}
		</div>
	</div>

	<div class="serviceFooter">
		<div class="serviceContainerButtonRow">
			<a title="zurück" href="{SELFURL}&amp;cmtTab={VAR:cmtTab}&amp;cmtAction=showLanguages" class="cmtButton cmtButtonBack cmtButtonNoText"></a>
			<button type="submit" value="speichern" name="saveEntry" class="cmtButton cmtButtonSave">speichern</button>
		</div>
	</div>
	<!-- outdated varnames -->
	<input type="hidden" value="{VAR:cmtAction}Save" name="action" id="cmtAction">
	<input type="hidden" value="{VAR:cmtApplicationID}" name="launch">
	<input type="hidden" value="{VAR:cmtTab}" name="cmtTab">

	<!-- new varnames -->	
	<input type="hidden" value="{VAR:cmtAction}Save" name="cmtAction" id="cmtAction">
	<input type="hidden" value="{VAR:cmtApplicationID}" name="cmtApplicationID">
	<input type="hidden" value="{VAR:cmtTab}" name="cmtTab">

	<input type="hidden" value="{VAR:id}" name="id[0]">

</form>

<!-- Dialogfenster Inhalte -->
<div id="cmtDialogConfirmDeletion" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		M&ouml;chten Sie diesen Eintrag wirklich l&ouml;schen?
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">l&ouml;schen</span>
	</div>
</div>
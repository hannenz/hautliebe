<link rel="Stylesheet" href="{CMT_TEMPLATE}app_pages/css/app_pages_style.css" type="text/css">

<div class="appHeadlineContainer">
	<div class="appIcon"><img src="{IF ({ISSET:icon:VAR})}{VAR:icon}{ELSE}{CMT_TEMPLATE}app_pages/img/app_pages_icon.png{ENDIF}" alt="" /></div>
	<h1>Website bearbeiten und gestalten</h1>
</div>
<div class="cmtPageTabs ui-tabs">
	<ul id="selectTabsHead" class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		{VAR:tabsContent}
	</ul>
</div>
<!-- Meldung -->

<div id="cmtMessageContainer">
{IF ({ISSET:deleteLanguageError:VAR})}
	<div class="cmtMessage cmtMessageError error">
		Die Sprachversion konnte nicht gel&ouml;scht werden.
	</div>
{ENDIF}
{IF ({ISSET:deleteLanguageTableError:VAR})}
	<div class="cmtMessage cmtMessageError error">
		Die gew&auml;hlte Sprachversion konnte nicht vollst&auml;ndig gel&ouml;scht werden. Abbruch bei Tabelle "{VAR:deleteLanguageTableError}".
	</div>
{ENDIF}
{IF ({ISSET:deleteLanguageEntryError:VAR})}
	<div class="cmtMessage cmtMessageError error">
		Der Spracheintrag in der Tabelle "cmt_content_languages" konnte nicht gel&ouml;scht werden (ID: {VAR:languageID}). Bitte entfernen Sie diesen manuell.<br /> 
		Alle anderen Tabellen und Daten der Sprachversion wurden erfolgreich gel&ouml;scht.
	</div>
{ENDIF}
{IF ({ISSET:deleteLanguageInsufficientNrError:VAR})}
	<div class="cmtMessage cmtMessageError error">
		Die letzte verf&uuml;gbare Sprachversion kann nicht gel&ouml;scht werden.
	</div>
{ENDIF}

{IF ({ISSET:newLanguageSuccess:VAR})}	
	<div class="cmtMessage cmtMessageSuccess success">
		Die neue Sprachversion wurde erfolgreich erzeugt.
	</div>
{ENDIF}
{IF ({ISSET:editLanguageSuccess:VAR})}	
	<div class="cmtMessage cmtMessageSuccess success">
		Die Sprachversion wurde erfolgreich bearbeitet.
	</div>
{ENDIF}
{IF ({ISSET:deleteLanguageSuccess:VAR})}
	<div class="cmtMessage cmtMessageSuccess success">
		Die Sprachversion "{VAR:languageName}" wurde erfolgreich gel&ouml;scht.
	</div>
{ENDIF}
</div>
<div class="serviceContainer">
	<div class="serviceContainerInner clearfix">
		<div class="serviceElementContainer">
			<a href="{SELFURL}&amp;cmtTab={VAR:cmtTab}&amp;action=newLanguage" class="cmtButton cmtButtonNewEntry" title="Neuee Sprachversion">Neue Sprachversion</a>
		</div>
	</div>
</div>

<form id="cmtLanguagesForm" action="{SELFURL}" method="post" >	
	<div>
		<table id="cmtLanguagesTable" class="cmtTable">
			<thead>
				<tr>
					<td>Sprache</td>
					<td>Abkürzung</td>
					<td>Domain</td>
					<td>Zeichensatz</td>
					<td class="tableActionHeadCell">Bearbeiten</td>
				</tr>
			</thead>
			<tbody>
		{VAR:languagesContent}
			</tbody>
		</table>
	</div>
	<div class="serviceContainerFooter clearfix">
<!--
		<div class="serviceContainerInner">
			<div class="serviceElementContainer">
				<button class="cmtButton cmtButtonSelectMultiple cmtSelectableSelectAll">alle ausw&auml;hlen</button>&nbsp;
				<button class="cmtButton cmtButtonUnselectMultiple cmtSelectableUnselectAll">Auswahl entfernen</button>
			</div>
			<div class="serviceElementContainer">
				<button class="cmtButton cmtButtonDeleteMultiple cmtSetAction cmtDialog cmtDialogConfirm" type="submit" data-action-type="deleteMultiple" data-action-target-id="cmtAction" data-dialog-submit-form-id="cmtPagesForm" data-dialog-content-id="cmtDialogConfirmMultipleDeletion">ausgew&auml;hlte l&ouml;schen</button>&nbsp;
				<input type="hidden" name="cmtAction" id="cmtAction" value=""/>
 				<button class="cmtButton cmtButtonDuplicateMultiple cmtSetAction" type="submit" data-action-type="duplicateMultiple" data-action-target-id="cmtAction">duplizieren</button>
			</div>
		</div>
		-->
	</div>
</form>

<!-- Dialogfenster Inhalte -->
<div id="cmtDialogConfirmDeletion" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		<p><span class="cmtDialogVar"></span></p>
		<p>M&ouml;chten Sie diese Sprachversion wirklich l&ouml;schen? Achtung: Es werden alle Seiten und Seiteninhalte gel&ouml;scht!</p>
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">l&ouml;schen</span>
	</div>
</div>

<div id="cmtDialogConfirmMultipleDeletion" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		M&ouml;chten Sie die ausgew&auml;hlten Einträge wirklich l&ouml;schen?
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">l&ouml;schen</span>
	</div>
</div>

<script type="text/javascript">
	cmtPages.options.pagesLanguage = '{VAR:cmtLanguage}';
	{IF ({ISSET:cmtNodeID:VAR})}cmtPages.options.nodeID = '{VAR:cmtNodeID}';{ENDIF}
</script>
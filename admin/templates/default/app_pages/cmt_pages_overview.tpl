<link rel="Stylesheet" href="{CMT_TEMPLATE}app_pages/css/app_pages_style.css" type="text/css">

<script src="{PATHTOADMIN}javascript/jquery-plugins/jquery-cookie/jquery.cookie.js" type="text/javascript"></script>
<script type="text/javascript" src="{PATHTOADMIN}javascript/jquery-plugins/jquery-ui-treetable/ui.treetable.js"></script>
<script type="text/javascript" src="{CMT_TEMPLATE}app_pages/javascript/cmt_pages_functions.js"></script>

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
	{VAR:userMessage}
	<div id="cmtMessageSaveOrderSuccess" class="cmtMessage cmtMessageSuccess cmtIgnore success">
		Die Seitenstruktur wurde erfolgreich gespeichert.
	</div>
	<div id="cmtMessageSaveOrderError" class="cmtMessage cmtMessageError cmtIgnore error">
		Die Seitenstruktur konnte wegen eines Fehlers nicht gespeichert werden.
	</div>
	
{IF ({ISSET:deletionSuccess:VAR})}	
	<div class="cmtMessage cmtMessageSuccess success">
		Die Seite und alle Unterseiten wurden erfolgreich gel&ouml;scht.
	</div>
{ENDIF}
{IF ({ISSET:deletionError:VAR})}
	<div class="cmtMessage error">
		Die Seite konnte wegen eines Fehlers nicht gel&ouml;scht werden.
	</div>
{ENDIF}
{IF ({ISSET:multipleDeletionSuccess:VAR})}	
	<div class="cmtMessage cmtMessageSuccess success">
		Die ausgew&auml;hlten Seiten und alle Unterseiten wurden erfolgreich gel&ouml;scht.
	</div>
{ENDIF}
{IF ({ISSET:multipleDeletionError:VAR})}
	<div class="cmtMessage cmtMessageError error">
		Die ausgew&auml;hlten Seiten konnten wegen eines Fehlers nicht gel&ouml;scht werden.
	</div>
{ENDIF}
{IF ({ISSET:duplicateContents:VAR})}	
	<div class="cmtMessage cmtMessageWarning cmtIgnore">
		<p>Sollen die Inhalte der Seite ebenfalls dupliziert werden?</p>
		<div>
			<button class="cmtButton cmtButtonBack">nicht duplizieren</button> 
			<a class="cmtButton cmtButtonDuplicateEntry" href="{SELFURL}&amp;cmtAction=duplicateContents&amp;edited_id={VAR:cmtEditedID}&amp;cmtDuplicatedID={VAR:cmtDuplicatedID}">Inhalte jetzt duplizieren</a>
		</div>
	</div>
{ENDIF}
{IF ({ISSET:duplicateContentsSuccess:VAR})}
	<div class="cmtMessage cmtMessageSuccess success">
		Die Inhalte wurden erfolgreich dupliziert.
	</div>
{ENDIF}
{IF ({ISSET:duplicateContentsError:VAR})}
	<div class="cmtMessage cmtMessageError error">
		Die Inhalte konnten wegen eines Fehlers nicht dupliziert werden.
	</div>
{ENDIF}
</div>
<div class="serviceContainer">
	<div class="serviceContainerInner clearfix">
		<div class="serviceElementContainer">
			<a href="{SELFURL}&cmt_dbtable={VAR:cmtPagesTable}&cmt_returnto={VAR:cmtReturnToApplicationID}&cmtNodeID={VAR:cmtNodeID}&cmt_parentid={VAR:cmtNodeID}&action=new&cmtAction=new" class="cmtButton cmtButtonNewPage" title="Neue Seite / Ordner">Neue Seite</a>
		</div>

		<div class="serviceElementContainer">
			<a id="cmtButtonSavePagesOrder" href="{SELFURL}&amp;cmtAction=savePagesOrder" class="cmtButton cmtButtonSave" title="Reihenfolge speichern">Reihenfolge speichern</a>
		</div>

		<div class="serviceElementContainer">
			<a id="cmtButtonSavePagesOrder" href="{SELFURL}&amp;cmtAction=savePagesToFiles" class="cmtButton cmtButtonSave" title="Seiten exportieren">Seiten exportieren</a>
		</div>
	</div>
</div>



<div class="serviceContainer">
	<div class="serviceContainerInner clearfix">
		<span class="serviceText">Aktuelle Position: </span>
		{IF ({ISSET:breadcrumbs:VAR})}<a class="breadcrumbRoot" href="{SELFURL}&amp;cmtCurrentNodeID=&amp;cmtLanguage={VAR:cmtLanguage}">Root</a>{VAR:breadcrumbs}{ELSE}
		<span class="breadcrumbRoot">Root</span>{ENDIF}
	</div>
</div>




<form id="cmtPagesForm" action="{SELFURL}" method="post" >	
	<div>
		<table id="cmtPagesTable" class="cmtTable">
			<thead>
				<tr>
					<td>Seite</td>
					<td class="cmtPagesTableIDCol">ID</td>
					<td class="tableActionHeadCell cmtPagesTableActionCol">Bearbeiten</td>
				</tr>
			</thead>
			<tbody>
		{VAR:pagesContent}
			</tbody>
		</table>
	</div>
	<div class="serviceContainerFooter clearfix">
		<div class="serviceContainerInner">
			<div class="serviceElementContainer">
				<button class="cmtButton cmtButtonSelectMultiple cmtSelectableSelectAll">alle ausw&auml;hlen</button>&nbsp;
				<button class="cmtButton cmtButtonUnselectMultiple cmtSelectableUnselectAll">Auswahl entfernen</button>
			</div>
			<div class="serviceElementContainer">
<!-- 				<span class="serviceText">Ausgew&auml;hlte Eintr&auml;ge:</span> -->
				<button class="cmtButton cmtButtonDeleteMultiple cmtSetAction cmtDialog cmtDialogConfirm" type="submit" data-action-type="deleteMultiple" data-action-target-id="cmtAction" data-dialog-submit-form-id="cmtPagesForm" data-dialog-content-id="cmtDialogConfirmMultipleDeletion">ausgew&auml;hlte l&ouml;schen</button>&nbsp;
				<input type="hidden" name="cmtAction" id="cmtAction" value=""/>
<!-- 				<button class="cmtButton cmtButtonDuplicateMultiple cmtSetAction" type="submit" data-action-type="duplicateMultiple" data-action-target-id="cmtAction">duplizieren</button> -->
			</div>
		</div>
	</div>
</form>



<!-- Dialogfenster Inhalte -->
<div id="cmtDialogConfirmDeletion" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		<p><span class="cmtDialogVar"></span></p>
		<p>M&ouml;chten Sie diesen Eintrag wirklich l&ouml;schen? Achtung: Es werden auch alle Unterseiten und Seiteninhalte gel&ouml;scht!</p>
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
	{IF ({ISSET:cmtCurrentNodeID:VAR})}cmtPages.options.nodeID = '{VAR:cmtCurrentNodeID}';{ENDIF}
</script>
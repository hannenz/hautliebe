<link rel="Stylesheet" href="{CMT_TEMPLATE}/app_showtable/css/cmt_showtable_style.css" type="text/css" />
<link rel="Stylesheet" href="{CMT_TEMPLATE}/app_mlog/css/cmt_mlog_style.css" type="text/css" />
<script src="{PATHTOADMIN}javascript/jquery-plugins/jquery-iframe-post-form/jquery.iframe-post-form.js" type="text/javascript"></script>

{IF (!{ISSET:cmtDialog})}
<div class="tableHeadlineContainer">
	<div class="tableIcon"><img src="{CMT_TEMPLATE}administration/img/default_table_icon_xlarge.png" alt="" /></div>
	<h1>{VAR:tableTitle}</h1>
</div>
{ENDIF}
{VAR:onLoadCode}
{IF ({ISSET:userMessage:VAR})}
<!-- Meldung -->
<div id="cmtMessageContainer">
	{VAR:userMessage}
</div>
{ENDIF}
<!-- zus&auml;tzliche Funktionsfelder -->
{IF ({ISSET:addService:VAR})}{VAR:addService}{ENDIF}
<form  name="cmtSearchForm" id="cmtSearchForm" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">
{IF ({ISSET:sortFields:VAR} || {ISSET:sortDirections:VAR} || {ISSET:searchFields:VAR})}
<!-- Sortier- und Suchfelder -->
<div class="cmtLayer {IF ({ISSET:searchActive:VAR})}cmtLayerActive{ENDIF}">
	<div class="cmtLayerHandle" id="cmtLayerSearchAndSort">
		<div class="cmt-layer-icon cmt-font-icon cmt-icon-search"></div>
		<div class="cmt-layer-open-close cmt-font-icon cmt-icon-open-close"></div>
		Suchen und sortieren
	</div>
	<div class="cmtLayerContent">
		<div class="serviceContainer" id="layer_{VAR:launch}_searchContent">
			<div class="serviceContainerInner">
		{IF ({ISSET:sortFields:VAR})}
				<div class="serviceElementContainer">{VAR:sortFields}</div>
		{ENDIF}
		{IF ({ISSET:sortDirections:VAR})}
				<div class="serviceElementContainer">{VAR:sortDirections}</div>
		{ENDIF}
		{IF ({ISSET:searchFields:VAR})}
				<div class="serviceElementContainer">{VAR:searchFields}</div>
		{ENDIF}
				<div class="lastServiceElementContainer">&nbsp;<br />
					<button class="cmtButton" type="submit" name="showSelection">zeigen</button> 
					<button class="cmtButton cmtButtonReset cmtButtonNoIcon" type="reset">zur&uuml;cksetzen</button>
				</div>
			</div>
		</div>
	</div>
</div>
{ENDIF}
{IF ({ISSET:newEntryLink:VAR} || {ISSET:itemsPerPage:VAR} || {ISSET:selectPage:VAR})}
<!-- Neuer Eintrag und Eintr&auml;gepro Seite -->
<div class="serviceContainer">
	<div class="serviceContainerInner clearfix">
{IF ({ISSET:newEntryLink:VAR})}
		<div class="serviceElementContainer">
			<a href="{VAR:newEntryLink}" class="cmtButton cmtButtonNewEntry" title="Neuer Eintrag">Neuer Eintrag</a>
		</div>
{ENDIF}
{IF ({ISSET:showIpp:VAR})}
		<div class="serviceElementContainer">
				<span class="serviceText">Eintr&auml;ge pro Seite:</span>&nbsp;{VAR:showIpp}&nbsp;<button class="cmtButton" type="submit" value="&auml;ndern" name="changeIpp">&auml;ndern</button>
		</div>
{ENDIF}
{IF ({ISSET:currentRows:VAR})}
		<div class="serviceElementContainer serviceElementFollowingText">
			<span class="serviceText"><strong>{VAR:currentRows}</strong> von <strong>{VAR:allRows}</strong> Eintr&auml;gen ausgew&auml;hlt.</span>
		</div>
{ENDIF}
	</div>
</div>
{IF ({ISSET:selectPage:VAR})}
<div class="serviceContainer">
	<div class="serviceContainerInner">
		<div class="serviceElementContainer">
			<div id="selectPageContainer" class="selectPageContainer">{VAR:selectPage}</div>
		</div>
		<div class="serviceElementContainer serviceElementFollowingText">
			<span class="serviceText">Seite <strong>{VAR:pagingCurrentPage}</strong> von <strong>{VAR:pagingTotalPages}</strong></span>
		</div>
	</div>
</div>
{ENDIF}
{ENDIF}
{IF ({ISSET:actQuery:VAR})}
<!-- Ausgef&uuml;hrte Query -->
<div class="serviceContainer">
	<span class="serviceText"><strong>Ausgef&uuml;hrte Query: &nbsp;</strong>{VAR:actQuery}</span>
</div>
{ENDIF}
<input type="hidden" name="launch" value="{VAR:launch}">
</form>
<form id="cmtFormEditMultiple" name="cmtFormEditMultiple" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">
<div class="tableBody">
{VAR:cmtRowsContent}
{VAR:content}
</div>
<div class="serviceFooter serviceContainer">
	<div class="serviceContainerInner">
{IF ({ISSET:deleteMultiple:VAR} || {ISSET:duplicateMultiple:VAR})}
		<div class="serviceElementContainer">
			<button class="cmtButton cmtButtonSelectMultiple cmtSelectableSelectAll">alle auswählen</button>&nbsp;
			<button class="cmtButton cmtButtonUnselectMultiple cmtSelectableUnselectAll">Auswahl entfernen</button>
		</div>
		<div class="serviceElementContainer">
			<span class="serviceText">Ausgewählte Einträge:</span>
			{IF ({ISSET:deleteMultiple:VAR})}<button class="cmtButton cmtButtonDeleteMultiple cmtSetAction cmtDialog cmtDialogConfirm" type="submit" data-action-type="deleteMultiple" data-action-target-id="cmtAction" data-dialog-submit-form-id="cmtFormEditMultiple" data-dialog-content-id="cmtDialogConfirmMultipleDeletion">l&ouml;schen</button>&nbsp;{ENDIF}
			{IF ({ISSET:duplicateMultiple:VAR})}<button class="cmtButton cmtButtonDuplicateMultiple cmtSetAction" type="submit" data-action-type="duplicateMultiple" data-action-target-id="cmtAction">duplizieren</button>{ENDIF}
		</div>
		<div class="serviceElementContainer serviceFooterPagingContainer">
			{VAR:pagingSelectFirstPage} 
			{VAR:pagingSelectPrevPage} 
			{VAR:pagingSelectAll}
			{VAR:pagingSelectNextPage} 
			{VAR:pagingSelectLastPage}   
		</div>
{ENDIF}
	</div>
</div>
{VAR:serviceFooter}
<input type="hidden" name="action" value="{VAR:action}" id="cmtAction" />
<input type="hidden" name="cmt_dbtable" value="{VAR:cmt_dbtable}" />
<input type="hidden" name="sid" value="{VAR:sid}" />
{IF ({ISSET:footerHtml:VAR})}<div class="serviceContainerFooter clearfix"><div class="serviceContainerInner">{VAR:footerHtml}</div></div>{ENDIF}
</form>

<!-- Dialogfenster Inhalte -->
<div id="cmtDialogConfirmDeletion" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		M&ouml;chten Sie den Mlog-Artikel mit der ID-Nr. <span class="cmtDialogVar"></span> wirklich l&ouml;schen?
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

{VAR:onCompleteCode}
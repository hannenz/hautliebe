<!-- Meldung -->
{IF ({ISSET:userMessage:VAR})}{VAR:userMessage}{ENDIF}
<!-- Tabellenauswahl -->
<div class="serviceContainer">
	<div class="serviceContainerInner">
		<div class="serviceElementContainer">
			<form name="selectTableForm" id="selectTableForm" action="{SELFURL}" method="post" enctype="application/x-www-form-urlencoded">
				<span class="serviceText">Tabelle: </span><select id="cmtTablename" name="cmt_tablename">
				{VAR:selectTable}</select>&nbsp;<input type="submit" class="cmtButton" name="selectTablename" value="ausw&auml;hlen" />
				<input type="hidden" name="cmtPage" value="1"/>
			</form>
		</div>
		<div class="serviceElementContainer serviceElementFollowingText">
			<span class="serviceText"><strong>{VAR:fieldsInTable} </strong> Felder in dieser Tabelle</span>
		</div>
	</div>
</div>
<!-- Service: Neues Feld und Anzahl der Einträge -->
<div class="serviceContainer">
	<div class="serviceContainerInner">
		<div class="serviceElementContainer">
			<a class="cmtButton cmtButtonNewEntry" href="{SELFURL}&amp;action=new&amp;cmt_tablename={VAR:cmt_tablename}&amp;cmtIpp={VAR:cmtIpp}&amp;cmtPage={VAR:cmtPage}">
				Neues Feld
			</a>
		</div>
		<div class="serviceElementContainer">
			<form id="selectTableForm" action="{SELFURL}" method="post" enctype="application/x-www-form-urlencoded">
				<span class="serviceText">Eintr&auml;ge pro Seite: </span><input type="text" size="2" value="{VAR:cmtIpp}" name="cmtIpp"/>
				<input class="cmtButton" type="submit" name="changeIpp" value="&auml;ndern"/>
				<input type="hidden" name="cmtCurrentTable" value="{VAR:cmtCurrentTable}"/>
			</form>
		</div>
		<div class="serviceElementContainer selectPageContainer">
			{VAR:pagingSelectFirstPage} 
			{VAR:pagingSelectPrevPage} 
			{VAR:pagingSelectAll}
			{VAR:pagingSelectNextPage} 
			{VAR:pagingSelectLastPage}  			
		</div>
		<div class="serviceElementContainer serviceElementFollowingText">
			<span class="serviceText">Seite <strong>{VAR:pagingCurrentPage}</strong> von <strong>{VAR:pagingTotalPages}</strong></span>
		</div>
	</div>
</div>

<!-- Service: Indizes -->
<!-- <div class="cmtLayer"> -->
<!-- 	<div class="cmtLayerHandle" id="cmtLayerIndex"> -->
<!-- 		<div class="cmtLayerHandleIndicator ui-icon {IF ("{VAR:layerStatus}" == "1")}ui-icon-triangle-1-n{ELSE}ui-icon-triangle-1-s{ENDIF}"></div> -->
<!-- 		Indizes erstellen und bearbeiten -->
<!-- 	</div> -->
<!-- 	<div id="cmtLayerContentIndex" class="cmtLayerContent serviceContainer"> -->
<!-- 		<div class="serviceContainerInner"> -->
<!-- 			<div class="serviceElementContainer"> -->
<!-- 				<a class="cmtButton cmtButtonAddIndex" href="{SELFURL}&amp;action=newIndex">Index hinzuf&uuml;gen</a> -->
<!-- 			</div> -->
<!-- 		{IF ({ISSET:selectIndex:VAR})} -->
<!-- 			<div class="serviceElementContainer"> -->
<!-- 				<form name="selectTableForm" id="selectTableForm" action="{SELFURL}" method="post" enctype="application/x-www-form-urlencoded"> -->
<!-- 					<span class="serviceText">Indexname (Typ: Felder): </span>{VAR:selectIndex}&nbsp; -->
<!-- 					<input type="submit" name="editIndex" value="bearbeiten" /> -->
<!-- 					<input type="hidden" name="action" value="editIndex" /> -->
<!-- 				</form> -->
<!-- 			</div> -->
<!-- 		{ELSE} -->
<!-- 			<div class="serviceElementContainer">Keine Indizes f&uuml;r die Felder dieser Tabelle definiert.</div> -->
<!-- 		{ENDIF} -->
<!-- 		</div> -->
<!-- 	</div> -->
<!-- </div> -->
<!-- Feldertabelle -->
<form name="cmt_editform" id="cmt_editform" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">
	<table id="fieldsTable" summary="" class="cmtTable">
		<thead>
			<tr>
				<td class="tableDataHeadCell" width="30%">Feldname</td>
				<td class="tableDataHeadCell" width="30%">Alias</td>
				<td class="tableDataHeadCell" width="30%">Feldtyp</td>
				<td class="tableDataHeadCell" width="5%">Volltextsuche</td>
				<td class="tableDataHeadCell" width="5%">Index</td>
				<td nowrap class="tableActionHeadCell">Funktionen</td>
			</tr>
		</thead>
		<tbody>
			{VAR:contentTable}
		</tbody>
	</table>
	<div class="serviceFooter serviceContainer clearfix">
		<div class="serviceContainerInner clearfix" style="min-height: 36px">
			<div class="serviceElementContainer serviceFooterPagingContainer clearfix">
				{VAR:pagingSelectFirstPage} 
				{VAR:pagingSelectPrevPage} 
				{VAR:pagingSelectAll}
				{VAR:pagingSelectNextPage} 
				{VAR:pagingSelectLastPage}   
			</div>
			<div>
				<input type="hidden" name="cmtTable" value="{VAR:cmtTable}" />
				<!--  is cmtTable field outdated? -->
				<input type="hidden" name="sid" value="{SID}"/>
				<input type="hidden" name="action" value=""/>
				<input type="hidden" name="cmtCurrentTable" value="{VAR:cmtCurrentTable}"/>
			</div>
		</div>
	</div>
</form>

<!-- Dialogfenster Inhalte -->
<div id="cmtDialogConfirmDeletion" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		M&ouml;chten Sie das Feld <span class="cmtDialogVar"></span> wirklich l&ouml;schen?
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">l&ouml;schen</span>
	</div>
</div>
<script type="text/javascript" src="{CMT_TEMPLATE}app_tablebrowser/javascript/cmt_tablebrowser_edittable.js"></script>
<form name="editTableForm" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">

{IF (!{ISSET:cmtDialog})}
{ENDIF}

<!-- Meldung -->
{IF ({ISSET:userMessage:VAR})}{VAR:userMessage}{ENDIF}
<!-- Service -->
<form name="editApplicationForm" action="{SELFURL}" method="POST" enctype="multipart/form-data">
	<div class="serviceContainerButtonRow">
		<a title="zurück" href="{SELFURL}&amp;cmt_slider={VAR:cmt_slider}" class="cmtButton cmtButtonBack cmtButtonNoText"></a>
		<button type="submit" value="speichern" name="saveEntry" class="cmtButton cmtButtonSave">speichern</button>
	</div>
	<div class="cmtTabs">
		<ul>
			<li><a href="#tabs-1">Tabelle</a></li>
			<li><a href="#tabs-2">optionale Angaben</a></li>
		</ul>
		<div id="tabs-1">
			<!--- Anfang --->
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG:0}">
				<div class="editEntryRowHead">MySQL-Name der Tabelle</div>
				<div class="editEntryRowField">
					{VAR:cmt_tablename}
				</div>
				<div class="editEntryRowDescription">
					Geben Sie hier den internen Namen der Datenbanktabelle ein (max. 64 Zeichen, keine Umlaute, keine Leerzeichen)
				</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="editEntryRowHead">Aliasname</div>
				<div class="editEntryRowField">
					{VAR:cmt_showname}
				</div>
				<div class="editEntryRowDescription">
					Aliasname der Tabelle, der in der Navigation angezeigt wird (alle Zeichen erlaubt)
				</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="editEntryRowHead">Gruppe</div>
				<div class="editEntryRowField">
					{VAR:cmt_group}
				</div>
				<div class="editEntryRowDescription">
					Gruppe, in der die Tabelle angezeigt werden soll.
				</div>
			</div>
<!-- 			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}"> -->
<!-- 				<div class="editEntryRowHead">Position</div> -->
<!-- 				<div class="editEntryRowField"> -->
<!-- 					{VAR:cmt_itempos} -->
<!-- 				</div> -->
<!-- 				<div class="editEntryRowDescription"> -->
<!-- 					Position in der Reihenfolge innerhalb der Gruppe. -->
<!-- 				</div> -->
<!-- 			</div> -->
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="editEntryRowHead">Tabelle sichtbar?</div>
				<div class="editEntryRowField">
					{VAR:cmt_itemvisible}
				</div>
				<div class="editEntryRowDescription">
					Soll die Tabelle in der Navigation angezeigt werden oder nicht?
				</div>
			</div>
			{IF ({ISSET:cmt_charset:VAR})}
			<div class="tableSubheadline">Zeichensatz und Sortierreihenfolge</div>
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG:1}">
				<div class="editEntryRowHead">Zeichensatz</div>
				<div class="editEntryRowField">
					<select name="cmt_charset" id="cmt_charset">{VAR:cmt_charset}</select>
				</div>
				<div class="editEntryRowDescription">
					Stellen Sie hier den Zeichensatz f&uuml;r die Tabelle ein. Idealerweise sollte "UTF-8 Unicode" verwendet werden.
				</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="editEntryRowHead">Sortierreihenfolge</div>
				<div class="editEntryRowField">
					<select name="cmt_collation" id="cmt_collation">{VAR:cmt_collation}</select>
				</div>
				<div class="editEntryRowDescription">
					Gibt an, nach welchen Kriterien die Daten in der Tabelle sortiert werden sollen.
				</div>
			</div>
			{ENDIF}
		</div>
		<div id="tabs-2">
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="editEntryRowHead">Systemtabelle</div>
				<div class="editEntryRowField">
					{VAR:cmt_systemtable}
				</div>
				<div class="editEntryRowDescription">
					Handelt es sich bei der Tabelle um eine f&uuml;r das CMS relevante Systemtabelle?
				</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="editEntryRowHead">Zielfenster</div>
				<div class="editEntryRowField">
					{VAR:cmt_target}
				</div>
				<div class="editEntryRowDescription">
					Zielfenster f&uuml;r den Navigationslink: Die Tabelle wird in dem hier eingetragenen Zielfenster angezeigt (z.B. '_blank', '_top' oder 'myframe').<br>
					Standard ist: leer.
				</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="editEntryRowHead">URL-/Querystring Variablen</div>
				<div class="editEntryRowField">
					{VAR:cmt_queryvars}
				</div>
				<div class="editEntryRowDescription">
					Hier k&ouml;nnen Variablen angegeben werden, die an den Link im Navigationsmen&uuml;s geh&auml;ngt werden.<br>
					Variablen wie folgt notieren: myVar1=10, etc. Mehrere Variablen m&uuml;ssen mit einer Zeilenschaltung getrennt werden.
				</div>
			</div>
		</div>
	</div>
	<!--- Ende --->
	
	<div class="serviceFooter">
		<div class="serviceContainerButtonRow">
			<a title="zurück" href="{SELFURL}&amp;cmt_slider={VAR:cmt_slider}" class="cmtButton cmtButtonBack cmtButtonNoText"></a>
			<button type="submit" value="speichern" name="saveEntry" class="cmtButton cmtButtonSave">speichern</button>
		</div>
		<input type="hidden" name="cmt_type" value="table">
		<input type="hidden" name="cmt_itempos" value="{FIELD:cmt_itempos}">
		<input type="hidden" name="save" value="1">
		<input type="hidden" name="action" value="{VAR:action}">
		<input type="hidden" name="id[0]" value="{VAR:id}">
	</div>
</form>

<!-- Dialogfenster Inhalte -->
<!--
<div id="cmtDialogConfirmDeletion" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		M&ouml;chten Sie diesen Eintrag wirklich l&ouml;schen?
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">l&ouml;schen</span>
	</div>
</div>
-->
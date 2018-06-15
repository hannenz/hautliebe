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

<!-- Eintrag -->
	<div class="cmtTabs">
		<ul>
			<li><a href="#tabs-1">Anwendung</a></li>
			<li><a href="#tabs-2">optionale Angaben</a></li>
		</ul>
		<div id="tabs-1">
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG:0}">
				<div class="editEntryRowHead">Anwendung</div>
				<div class="editEntryRowField">
					{VAR:cmt_include}
				</div>
				<div class="editEntryRowDescription">W&auml;hlen Sie hier die Datei aus, die eingebunden werden soll.</div>	
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="editEntryRowHead">Aliasname</div>
				<div class="editEntryRowField">
					{VAR:cmt_showname}
				</div>
				<div class="editEntryRowDescription">
					Aliasname der Anwendung, der in der Navigation angezeigt wird (alle Zeichen erlaubt)
				</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="editEntryRowHead">Gruppe</div>
				<div class="editEntryRowField">
					{VAR:cmt_group}
				</div>
				<div class="editEntryRowDescription">
					Gruppe, in der die Anwendung angezeigt werden soll.
				</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="editEntryRowHead">Anwendung sichtbar?</div>
				<div class="editEntryRowField">
					{VAR:cmt_itemvisible}
				</div>
				<div class="editEntryRowDescription">
					Soll die Anwendung in der Navigation angezeigt werden oder nicht?
				</div>
			</div>
		</div>
		<div id="tabs-2">
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="editEntryRowHead">Systemtabelle</div>
				<div class="editEntryRowField">
					{VAR:cmt_systemtable}
				</div>
				<div class="editEntryRowDescription">
					Handelt es sich bei dem Eintrag um eine f&uuml;r das CMS relevante Anwendung?
				</div>
			</div>
			<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
				<div class="editEntryRowHead">Zielfenster</div>
				<div class="editEntryRowField">
					{VAR:cmt_target}
				</div>
				<div class="editEntryRowDescription">
					Zielfenster f&uuml;r den Navigationslink: Die Anwendung wird in dem hier eingetragenen Zielfenster angezeigt (z.B. '_blank', '_top' oder 'myframe').<br>
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
					Variablen wie folgt notieren: <i>myVar1=10</i>, etc. Mehrere Variablen m&uuml;ssen mit einer Zeilenschaltung getrennt werden.
				</div>
			</div>	
		</div>
	</div>
	
	<div class="serviceFooter">
		<div class="serviceContainerButtonRow">
			<a title="zurück" href="{SELFURL}&amp;cmt_slider=1" class="cmtButton cmtButtonBack cmtButtonNoText"></a>
			<button type="submit" value="speichern" name="saveEntry" class="cmtButton cmtButtonSave">speichern</button>
		</div>
		<input type="hidden" value="{VAR:cmt_slider}" name="cmt_slider">
		<input type="hidden" name="cmt_type" value="application">
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
<!--- Ende --->
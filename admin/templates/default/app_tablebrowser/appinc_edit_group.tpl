{IF (!{ISSET:cmtDialog})}
{ENDIF}

<form name="editTableForm" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">
	<div class="serviceContainerButtonRow">
		<a title="zurück" href="{SELFURL}&amp;cmt_slider={VAR:cmt_slider}" class="cmtButton cmtButtonBack cmtButtonNoText"></a>
		<button type="submit" value="speichern" name="saveEntry" class="cmtButton cmtButtonSave">speichern</button>
	</div>
	{IF ({ISSET:userMessage:VAR})}{VAR:userMessage}{ENDIF}
	<!--- Anfang --->
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG:0}">
		<div class="editEntryRowHead">Gruppenname</div>
		<div class="editEntryRowField">
			{VAR:cmt_groupname}
		</div>
		<div class="editEntryRowDescription">
			Bitte tragen Sie hier den Namen der Gruppe ein. Er darf alle Zeichen (auch Leerzeichen und Umlaute) enthalten.
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="editEntryRowHead">Gruppenposition</div>
		<div class="editEntryRowField">
			{VAR:cmt_grouppos}
		</div>
		<div class="editEntryRowDescription">
			Dieses Feld gibt die Position dieser Gruppe innerhalb der Gruppenreihenfolge an.
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="editEntryRowHead">Gruppe sichtbar?</div>
		<div class="editEntryRowField">
			{VAR:cmt_visible}
		</div>
		<div class="editEntryRowDescription">
			Soll die Gruppe in der Navigation angezeigt werden oder nicht?
		</div>
	</div>
	<div class="tableSubheadline">Weitere Einstellungen</div>
	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="editEntryRowHead">Import-Gruppe</div>
		<div class="editEntryRowField">
			{VAR:cmt_isimportgroup}
		</div>
		<div class="editEntryRowDescription">
			Eine Gruppe kann als Import-Gruppe definiert werden. Alle in das CMS importierten Tabellen werden zuk&uuml;nftig zuerst in diese Gruppe geschoben.<br>
			Es wird dringend geraten, eine Gruppe als Import-Gruppe zu definieren.
		</div>
	</div>
<!--- Ende --->
	<div class="serviceFooter">
		<div class="serviceContainerButtonRow">
			<a title="zurück" href="{SELFURL}&amp;cmt_slider={VAR:cmt_slider}" class="cmtButton cmtButtonBack cmtButtonNoText"></a>
			<button type="submit" value="speichern" name="saveEntry" class="cmtButton cmtButtonSave">speichern</button>
		</div>
		<input type="hidden" name="cmt_type" value="group">
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
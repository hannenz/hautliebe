<!-- Meldung -->
{IF ({ISSET:userMessage:VAR})}
<div id="cmtMessageContainer">
	<div class="cmtMessage cmtMessage{VAR:userMessageType}">
		{IF ("{VAR:userMessageText}" == "userDeleted")}Der Benutzer wurde erfolgreich gelöscht!{ENDIF}
		{IF ("{VAR:userMessageText}" == "userNotDeleted")}Der Benutzer konnte nicht gelöscht werden!{ENDIF}
		{IF ("{VAR:userMessageText}" == "groupDeleted")}Die Gruppe wurde erfolgreich gelöscht!{ENDIF}
		{IF ("{VAR:userMessageText}" == "groupNotDeleted")}Die Gruppe konnte nicht gelöscht werden!{ENDIF}
	</div>
</div>
{ENDIF}
<!-- Service -->
<div class="serviceContainer">
	<div class="serviceContainerInner">
		<div class="serviceElementContainer">
			<a href="{SELFURL}&amp;cmt_dbtable={VAR:cmtUserTable}&amp;cmt_returnto={VAR:cmtReturnToID}&amp;action=new" class="cmtButton cmtButtonNewUser" title="Neuer Benutzer">Neuer Benutzer</a>
		</div>
		<div class="serviceElementContainer">
			<a href="{SELFURL}&amp;cmt_dbtable={VAR:cmtUserGroupTable}&amp;cmt_returnto={VAR:cmtReturnToID}&amp;action=new" class="cmtButton cmtButtonNewUserGroup" title="Neue Gruppe">Neue Gruppe</a>
		</div>
	</div>
</div>
<table class="">
	{VAR:contentGroups}
</table>

<!-- Dialogfenster Inhalte -->
<div id="cmtDialogConfirmDeletionUser" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		<p>M&ouml;chten Sie den Benutzer</p>
		<div class="cmtDialogVar"></div>
		<p>wirklich l&ouml;schen?</p>
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">l&ouml;schen</span>
	</div>
</div>

<div id="cmtDialogConfirmDeletionUserGroup" class="cmtDialogContentContainer">
	<div class="cmtDialogContent">
		<p>M&ouml;chten Sie die Gruppe</p>
		<div class="cmtDialogVar"></div>
		<p>wirklich l&ouml;schen?</p>
		<p>&nbsp;<br />Es werden auch unwiderruflich alle Benutzer dieser Gruppe gelöscht!</p>
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
<!-- Meldung -->
{IF ({ISSET:userMessageText:VAR})}
<div id="cmtMessageContainer">
	<div class="cmtMessage cmtMessage{VAR:userMessageType}">
		{IF ("{VAR:userMessageText}" == "userDeleted")}Der Benutzer wurde erfolgreich gelöscht!{ENDIF}
		{IF ("{VAR:userMessageText}" == "userNotDeleted")}Der Benutzer konnte nicht gelöscht werden!{ENDIF}
		{IF ("{VAR:userMessageText}" == "groupAccessSaved" && "{VAR:userMessageType}" == "Success" )}Die Gruppenrechte wurde erfolgreich gespeichert und die Nutzerrechte in der Gruppe wurden angepasst.{ENDIF}
		{IF ("{VAR:userMessageText}" == "groupAccessSaved" && "{VAR:userMessageType}" == "Error" )}Die Gruppenrechte konnten wegen eines Fehlers nicht gespeichert werden!{ENDIF}
		{IF ("{VAR:userMessageText}" == "userAccessSaved" && "{VAR:userMessageType}" == "Success" )}Die Benutzerrechte wurde erfolgreich gespeichert.{ENDIF}
		{IF ("{VAR:userMessageText}" == "userAccessSaved" && "{VAR:userMessageType}" == "Error" )}Die Benutzerrechte konnten wegen eines Fehlers nicht gespeichert werden!{ENDIF}

	</div>
</div>
{ENDIF}
<!-- Service -->
<div class="serviceContainer">
	<div class="serviceContainerInner">
		<div class="serviceElementContainer cmtSelectUserGroupContainer">
			<form id="cmtSelectUserGroupForm" action="{SELFURL}&amp;action=editUserGroupRights" method="post">
				<select id="cmtSelectUserGroup" name="userGroupID" data-submit-onchange="true">
					<option value="">-- Gruppe ausw&auml;hlen --</option>
					{VAR:cmtGroupSelectOptions}
				</select>
				<button class="cmtButton cmtButtonEdit">Gruppe bearbeiten</button>
			</form>
		</div>
		<div class="serviceElementContainer cmtSelectUserContainer">
			<form id="cmtSelectUserForm" action="{SELFURL}&amp;action=editUserRights" method="post">
				<select id="cmtSelectUser" name="userID" data-submit-onchange="true">
					<option value="">-- Benutzer ausw&auml;hlen --</option>
					{VAR:cmtUserSelectOptions}
				</select>
				<button class="cmtButton cmtButtonEdit">Benutzer bearbeiten</button>
			</form>
		</div>
	</div>
</div>
{IF ({ISSET:editUserGroupContent:VAR} || {ISSET:editUserContent:VAR})}

{IF ({ISSET:editUserGroupContent:VAR})}
<form id="cmtEditUserGroupRights" action="{SELFURL}&amp;action=editUserGroupRights" method="post">
	<table class="cmtTable">
		<thead>
			<tr>
				<td>Anwendung / Tabelle</td>
				<td>Zugriff</td>
				<td>neu</td>
				<td>bearbeiten</td>
				<td>duplizieren</td>
				<td>löschen</td>
				<td>betrachten</td>
			</tr>
		</thead>
		<tbody>
			{VAR:editUserGroupContent}
		</tbody>
	</table>
{ELSE}
<form id="cmtEditUserRights" action="{SELFURL}&amp;action=editUserRights" method="post">
	<table class="cmtTable">
		<thead>
			<tr>
				<td>Anwendung / Tabelle</td>
				<td>Zugriff</td>
				<td>neu</td>
				<td>bearbeiten</td>
				<td>duplizieren</td>
				<td>löschen</td>
				<td>betrachten</td>
			</tr>
		</thead>
		<tbody>
			{VAR:editUserContent}
		</tbody>
	</table>
{ENDIF}
	<div class="serviceFooter">
		<div class="serviceContainerButtonRow">
			{IF ({ISSET:editUserGroupContent:VAR})}
			<button class="cmtButton cmtButtonSave">Gruppenrechte speichern</button>
			{ELSE}
			<button class="cmtButton cmtButtonSave">Benutzerrechte speichern</button>
			{ENDIF}
			<input type="hidden" name="cmtSave" value="1" />
			<input type="hidden" name="userID" value="{VAR:userID}" />
			<input type="hidden" name="userGroupID" value="{VAR:userGroupID}" />
		</div>
	</div>
</form>
{ELSE}
<div class="cmtMessage cmtMessageInfo cmtIgnore">Bitte wählen Sie zum Einstellen der Rechte eine Benutzergruppe oder einen Benutzer aus.</div>
{ENDIF}
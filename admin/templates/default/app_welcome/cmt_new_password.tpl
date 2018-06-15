{IF ({ISSET:passwordError})}
<div class="cmtMessage cmtMessageError">
	{IF ({ISSET:passwordsNotEqualError:VAR})}
	Die Passwörter sind nicht identisch. Bitte geben Sie beide erneut ein.
	{ENDIF}
	{IF ({ISSET:passwordTooShortError:VAR})}
	Das eingegebene Passwort ist zu kurz. Ein gültiges Passwort muss mindestens 8 Zeichen lang sein.
	{ENDIF}
	{IF ({ISSET:passwordSaveError:VAR})}
	Das neue Passwort konnte wegen eines Datenbankfehlers nicht gespeichert werden.
	{ENDIF}
</div>
{ENDIF}
<form action="{SELFURL}&amp;cmtAction=changePassword" method="post" id="passwordChangeForm">
	<div class="cmtEditEntryRow cmtEditEntryRow0">
<p>
	Bitte geben Sie hier ein neues Passwort ein und wiederholen Sie es im zweiten Feld. Das Passwort muss mindestens 8 Zeichen lang sein.
</p>
		<div class="editEntryRowHead">Neues Passwort</div>
		<div class="editEntryRowField">
			<input type="text" name="newPassword1" value="{VAR:newPassword1}">
		</div>
	</div>
	<div class="cmtEditEntryRow cmtEditEntryRow1">
		<div class="editEntryRowHead">Neues Passwort (Wiederholung)</div>
		<div class="editEntryRowField">
			<input type="text" name="newPassword2" value="{VAR:newPassword2}">
		</div>
	</div>
	<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
		<div class="ui-dialog-buttonset">
			<a class="cmtButton cmtButtonBack" href="">abbrechen</a>
			<button class="cmtButton cmtButtonSave" value="speichern" type="submit">
				speichern
			</button>
		</div>
	</div>
</form>


<script>
	cmtDashboard.initPasswordChange({
		formId: 'passwordChangeForm'
	});
	
	{IF ({ISSET:passwordError})}
		cmtPage.initButtons('#passwordChangeForm');
	{ENDIF}
	
	{IF ({ISSET:passwordSaveSuccess})}
		location.reload();
	{ENDIF}
	
</script>

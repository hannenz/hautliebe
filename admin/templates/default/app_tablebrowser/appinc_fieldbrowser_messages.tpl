{IF ("{VAR:message}" == "deleteField")}
	{IF ({ISSET:successId:VAR})}<div class="success">Das Feld/ die Felder mit der/ den ID(s) {VAR:successId} wurde(n) erfolgreich gel&ouml;scht.</div>{ENDIF}
	{IF ({ISSET:errorId:VAR})}<div class="error">Das Feld/ die Felder mit der/ den ID(s) {VAR:errorId} wurde(n) konnten nicht gel&ouml;scht werden.</div>{ENDIF}
{ENDIF}

{IF ("{VAR:message}" == "editField")}
	{IF ("{ISSET:successId:VAR}")}<div class="success">Das Feld ID: {VAR:successIds} wurde erfolgreich bearbeitet.</div>{ENDIF}
	{IF ("{ISSET:errorId:VAR}")}<div class="error">Das Feld ID: {VAR:successIds} konnte nicht bearbeitet werden.</div>{ENDIF}
{ENDIF}
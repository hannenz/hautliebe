{IF ({ISSET:dbErrorMessage:VAR})}
<div class="messageError">Die Einstellungen konnten wegen eines Datenbankfehlers nicht gespeichert: {VAR:dbErrorMessage} (Fehler-Nr.: {VAR:dbErrorNr})</div>
{ELSE}
<div class="messageError">{VAR:errorMessage}</div>
{ENDIF}
{IF ('{VAR:errorNr}' == '3001')}
Die von Ihnen angegebene E-Mailadresse ist ung&uuml;ltig.
{ENDIF}

{IF ('{VAR:errorNr}' == '3002')}
Diese E-Mailadresse existiert bereits, ist aber noch nicht freigeschaltet. 
{ENDIF}

{IF ('{VAR:errorNr}' == '3003')}
Diese E-Mailadresse existiert bereits. 
{ENDIF}

{IF ('{VAR:errorNr}' == '3033')}
Bitte w&auml;hlen Sie eine CSV Datei aus. 
{ENDIF}

{IF ('{VAR:errorNr}' == '3011')}
	Bitte w&auml;hlen Sie mindestens einen Newsletter aus!
{ENDIF}
{IF ("{VAR:error}" == "indexNameMissing")}Bitte geben Sie einen Namen f&uuml;r den Index an!{ENDIF}
{IF ("{VAR:error}" == "indexNameAlreadyExists")}Es existiert bereits ein Index mit diesem Namen!{ENDIF}
{IF ("{VAR:error}" == "cantRemoveOldIndex")}Die &Auml;nderungen konnten nicht gespeichert werden, da sich der Index nicht l&ouml;schen l&auml;sst! ({VAR:dbErrorNr}: {VAR:dbMessage}){ENDIF}
{IF ("{VAR:error}" == "cantSaveIndex")}Der Index konnte nicht gespeichert werden! ({VAR:dbErrorNr}: {VAR:dbMessage}){ENDIF}
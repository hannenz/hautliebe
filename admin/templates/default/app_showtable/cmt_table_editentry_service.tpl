<div class="serviceContainerButtonRow">
	<a class="cmtButton cmtButtonBack" href="{VAR:backURL}{IF ({ISSET:entryID:VAR})}#entry-{VAR:entryID}{ENDIF}" title="zur&uuml;ck"></a>
	{IF ({ISSET:saveItem:VAR})}
	<button class="cmtButton cmtButtonSave" name="saveEntry" value="speichern" type="submit">speichern</button>
	{ENDIF}
	{IF ({ISSET:deleteItem:VAR})}
	<span class="serviceSeparator serviceSeparatorButtonFollows"></span>
	<a class="cmtButton cmtButtonDelete cmtDialog cmtDialogConfirm" data-dialog-cancel-url="" data-dialog-confirm-url="{VAR:deletionURL}" data-dialog-var="" data-dialog-content-id="cmtDialogConfirmDeletion" href="Javascript: void(0);">l&ouml;schen</a>
	{ENDIF}

{IF ({ISSET:viewItem:VAR})}
	<!-- anzeigen -->
	<span class="serviceSeparator serviceSeparatorButtonFollows"></span>
	{IF ({ISSET:showPrevURL:VAR})}
	<a class="cmtButton cmtButtonPrevEntry" href="{VAR:showPrevURL}" title="zum vorherigen Eintrag"></a>
	{ELSE}
	<span class="cmtButton cmtDisabled cmtButtonPrevEntry" title="kein vorheriger Eintrag vorhanden"></span>
	{ENDIF}
	{IF ({ISSET:showNextURL:VAR})}
	<a class="cmtButton cmtButtonNextEntry" href="{VAR:showNextURL}" title="zum folgenden Eintrag"></a>
	{ELSE}
	<span class="cmtButton cmtDisabled cmtButtonNextEntry" title="kein folgender Eintrag vorhanden"></span>
	{ENDIF}
{ENDIF}

{IF ({ISSET:editItem:VAR})}
	<!-- speichern und anzeigen -->
	<span class="serviceSeparator serviceSeparatorButtonFollows"></span>
	{IF ({ISSET:savePrevItem:VAR})}
	<button class="cmtButton cmtSetAction cmtButtonSaveShowPrev" data-action-target-id="cmtAction" data-action-type="savenshow_prev" type="submit" title="speichern und zeige vorherigen Eintrag"></button>
	{ELSE}
	<span class="cmtButton cmtDisabled cmtButtonSaveShowPrev" title="kein vorheriger Eintrag vorhanden"></span>
	{ENDIF}
	{IF ({ISSET:saveNextItem:VAR})}
	<button class="cmtButton cmtSetAction cmtButtonSaveShowNext" data-action-target-id="cmtAction" data-action-type="savenshow_next" type="submit" title="speichern und zeige n&auml;chsten Eintrag"></button>
	{ELSE}
	<span class="cmtButton cmtDisabled cmtButtonSaveShowNext" title="kein folgender Eintrag vorhanden"></span>
	{ENDIF}
{ENDIF}

{IF ({ISSET:deleteItem:VAR})}
	<!-- löschen -->
	<span class="serviceSeparator serviceSeparatorButtonFollows"></span>
	{IF ({ISSET:deletePrevURL:VAR})}
	<a class="cmtButton cmtButtonDeleteShowPrev cmtDialog cmtDialogConfirm" data-dialog-cancel-url="" data-dialog-confirm-url="{VAR:deletePrevURL}" data-dialog-var="" data-dialog-content-id="cmtDialogConfirmDeletion" href="Javascript: void(0);" title="diesen Eintrag l&ouml;schen und zum vorherigen wechseln"></a>
	{ELSE}
	<span class="cmtButton cmtDisabled cmtButtonDeleteShowPrev" title="kein vorheriger Eintrag vorhanden"></span>
	{ENDIF}
	{IF ({ISSET:deleteNextURL:VAR})}
	<a class="cmtButton cmtButtonDeleteShowNext cmtDialog cmtDialogConfirm" data-dialog-cancel-url="" data-dialog-confirm-url="{VAR:deleteNextURL}" data-dialog-var="" data-dialog-content-id="cmtDialogConfirmDeletion" href="Javascript: void(0);" title="diesen Eintrag l&ouml;schen und zum n&auml;chsten wechseln"></a>
	{ELSE}
	<span class="cmtButton cmtDisabled cmtButtonDeleteShowNext" title="kein folgender Eintrag vorhanden"></span>
	{ENDIF}
	{ENDIF}
	{IF ('{VAR:cmt_action}' == 'edit' && "{VAR:totalEntries}" != "0")}
	<span class="serviceSeparator"></span>
	<span class="serviceText">Zeige Eintrag <strong>{VAR:currentEntry}</strong> von <strong>{VAR:totalEntries}</strong></span>{ENDIF}
</div>
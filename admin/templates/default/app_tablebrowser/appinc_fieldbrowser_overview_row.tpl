<tr class="cmtHover cmtSelectable">
	<td class="cmtTableData cmtAlternate{VAR:alternationFlag}">{VAR:cmt_fieldname}</td>
	<td class="cmtTableData cmtAlternate{VAR:alternationFlag}">{VAR:cmt_fieldalias}</td>
	<td class="cmtTableData cmtAlternate{VAR:alternationFlag}">{VAR:cmt_fieldtype}</td>
	<td class="cmtTableData cmtAlternate{VAR:alternationFlag}">{IF ({ISSET:isFulltext:VAR})}ja{ELSE}nein{ENDIF}</td>
	<td class="cmtTableData cmtAlternate{VAR:alternationFlag}">{IF ({ISSET:isIndex:VAR})}ja{ELSE}nein{ENDIF}</td>
	<td class="cmtTableFunctions cmtAlternate{VAR:alternationFlag}">
		<a class="cmtIcon cmtButtonEditEntry" href="{SELFURL}&amp;action=edit&amp;id[]={VAR:id}" title="Feld bearbeiten"></a>
		<a class="cmtIcon cmtButtonDuplicateEntry" href="{SELFURL}&amp;action=duplicate&amp;id[]={VAR:id}" title="Feld duplizieren"></a>
		<a class="cmtIcon cmtButtonDeleteEntry cmtDialog cmtDialogConfirm" href="Javascript: void(0);" data-dialog-content-id="cmtDialogConfirmDeletion" data-dialog-var="{VAR:cmt_fieldname}" data-dialog-confirm-url="{SELFURL}&amp;action=delete&amp;id[]={VAR:id}" data-dialog-cancel-url="" title="Feld l&ouml;schen"></a>
		<input class="cmtSelectableStatus cmtIgnore" type="checkbox" name="id[{VAR:rowCounter}]" />
	</td>
</tr>
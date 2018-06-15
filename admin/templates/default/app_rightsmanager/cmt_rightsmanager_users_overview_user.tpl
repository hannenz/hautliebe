<tr class="cmtHover cmtUserRow">
	<td class="cmtTableData cmtAlternate{VAR:alternationFlag} cmtUserName">{VAR:cmt_username} {IF ({ISSET:cmt_useralias})}({VAR:cmt_useralias}){ENDIF}</td>
	<td class="cmtTableFunctions cmtAlternate{VAR:alternationFlag} cmtSelectableDontSelect">
		<a class="cmtIcon cmtButtonEditEntry" href="{SELFURL}&amp;cmt_dbtable={VAR:cmtUserTable}&amp;id[]={VAR:id}&amp;cmt_returnto={VAR:cmtReturnToID}&amp;action=edit"></a>
		<a class="cmtIcon cmtButtonDuplicateEntry" href="{SELFURL}&amp;cmt_dbtable={VAR:cmtUserTable}&amp;id[]={VAR:id}&amp;cmt_returnto={VAR:cmtReturnToID}&amp;action=duplicate"></a>
		<a class="cmtIcon cmtButtonDeleteEntry cmtDialog cmtDialogConfirm" data-dialog-cancel-url="" data-dialog-confirm-url="{SELFURL}&amp;id[]={VAR:id}&amp;action=deleteUser" data-dialog-title="Benutzer löschen" data-dialog-var="{VAR:cmt_username} {IF ({ISSET:cmt_useralias})}({VAR:cmt_useralias}){ENDIF}" data-dialog-content-id="cmtDialogConfirmDeletionUser" href="Javascript:void(0);"></a>
		<input class="cmtSelectableStatus cmtIgnore" type="checkbox" value="{VAR:id}" name="id[{VAR:id}]">
	</td>
</tr>
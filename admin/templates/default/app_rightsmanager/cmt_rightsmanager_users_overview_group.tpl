<tr class="cmtHover cmtUserGroupRow">
	<td class="cmtTableData cmtUserGroupName">{VAR:cmt_groupname}</td>
	<td class="cmtTableFunctions cmtSelectableDontSelect">
		<a class="cmtIcon cmtButtonEditEntry" href="{SELFURL}&amp;cmt_dbtable={VAR:cmtUserGroupTable}&amp;id[]={VAR:id}&amp;cmt_returnto={VAR:cmtReturnToID}&amp;action=edit"></a>
		{IF ("{VAR:cmt_grouptype}" != "admin")}
		<a class="cmtIcon cmtButtonDuplicateEntry" href="{SELFURL}&amp;cmt_dbtable={VAR:cmtUserGroupTable}&amp;id[]={VAR:id}&amp;cmt_returnto={VAR:cmtReturnToID}&amp;action=duplicate"></a>
		<a class="cmtIcon cmtButtonDeleteEntry cmtDialog cmtDialogConfirm" data-dialog-cancel-url="" data-dialog-confirm-url="{SELFURL}&amp;id[]={VAR:id}&amp;action=deleteGroup" data-dialog-title="Gruppe löschen" data-dialog-var="{VAR:cmt_groupname} (id: {VAR:id})" data-dialog-content-id="cmtDialogConfirmDeletionUserGroup" href="Javascript:void(0);"></a>
		<input class="cmtSelectableStatus cmtIgnore" type="checkbox" value="{VAR:id}" name="groupID[{VAR:id}]">
		{ENDIF}
	</td>
</tr>
{VAR:contentUsers}
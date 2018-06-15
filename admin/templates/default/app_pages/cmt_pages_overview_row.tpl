<tr id="cmtNode-{VAR:id}" class="cmtPageRow cmtHover cmtNode cmtChildOf-{VAR:cmt_parentid} cmtSelectable" data-children="{VAR:hasChildren}">
	<td>
		<div class="cmtNodeDraggable">
			<div class="cmtPageIcon cmtPageType{VAR:cmt_type:ucfirst} cmtPageStatus{VAR:cmt_showinnav}">
				{IF ({ISSET:cmt_isroot:VAR})}<span class="cmtPageIsHomepage"> </span>{ENDIF}
				{IF ({ISSET:cmt_protected:VAR})}<span class="cmtPageProtected"> </span>{ENDIF}
			</div>
			<a class="cmtPageTitle" href="{SELFURL}&amp;cmtNodeID={VAR:id}&amp;cmtLanguage={VAR:cmtLanguage}&amp;cmtCurrentNodeID={VAR:id}">{VAR:cmt_title}</a>
		</div>
	</td>
	<td class="cmtPagesTableIDCell">
		{VAR:id}
	</td>
	<td class="cmtPagesTableActionCell">
		{IF ("{VAR:cmt_type}" == "page")}
		<a class="cmtIcon cmtButtonLayoutPage" target="_top" title="Seite layouten" href="{SELFURL}&amp;cmtLaunchApplicationID={VAR:cmtLayoutApplicationID}&amp;cmtPageID={VAR:id}&amp;cmtLanguage={VAR:cmtLanguage}&amp;cmt_returnto={VAR:cmtReturnToApplicationID}&amp;cmtAction=layout"></a>
		{ENDIF}
		<a class="cmtIcon cmtButtonEditPage" title="Seiteneigenschaften bearbeiten" href="{SELFURL}&amp;id[]={VAR:id}&amp;cmt_dbtable={VAR:cmtPagesTable}&amp;cmt_returnto={VAR:cmtReturnToApplicationID}&amp;action=edit"></a>
		<a class="cmtIcon cmtButtonDuplicatePage" title="Seite duplizieren" href="{SELFURL}&amp;id[]={VAR:id}&amp;cmt_dbtable={VAR:cmtPagesTable}&amp;cmt_returnto={VAR:cmtReturnToApplicationID}&amp;action=duplicate"></a>
		<a class="cmtIcon cmtButtonDeletePage cmtDialog cmtDialogConfirm" title="Seite l&ouml;schen" href="Javascript:void(0);" data-dialog-title="Eintrag l&ouml;schen" data-dialog-cancel-url="" data-dialog-confirm-url="{SELFURL}&amp;id[]={VAR:id}&amp;cmt_dbtable={VAR:cmtPagesTable}&amp;cmt_returnto={VAR:cmtReturnToApplicationID}&amp;action=delete" data-dialog-var="{VAR:cmt_title:htmlentities} ({VAR:id})" data-dialog-content-id="cmtDialogConfirmDeletion"></a>
		<input type="checkbox" class="cmtSelectableStatus cmtIgnore" value="{VAR:id}" name="id[]" />
	</td>	
</tr>
{VAR:subPagesContent}
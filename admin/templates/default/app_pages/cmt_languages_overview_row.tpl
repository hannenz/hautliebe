<tr id="cmtNode-{VAR:id}" class="cmtPageRow cmtHover cmtSelectable">
	<td class="cmtTableData cmtAlternate{VAR:alternationFlag}">
		{VAR:cmt_languagename}
	</td>
	<td class="cmtTableData cmtAlternate{VAR:alternationFlag}">
		{VAR:cmt_language}
	</td>
	<td class="cmtTableData cmtAlternate{VAR:alternationFlag}">
		{VAR:cmt_domain_id}
	</td>
	<td class="cmtTableData cmtAlternate{VAR:alternationFlag}">
		{VAR:cmt_charset}
	</td>
	<td class="cmtTableFunctions cmtAlternate{VAR:alternationFlag}">
		<a class="cmtIcon cmtButtonEditEntry" title="Sprachversion bearbeiten" href="{SELFURL}&amp;id[]={VAR:id}&amp;action=editLanguage"></a>
<!--		<a class="cmtIcon cmtButtonDuplicateEntry" title="Sprachversion duplizieren" href="{SELFURL}&amp;id[]={VAR:id}&amp;cmt_dbtable={VAR:cmtPagesTable}&amp;cmt_returnto={VAR:cmtReturnToApplicationID}&amp;action=duplicateLanguageEntry"></a> -->
		<a class="cmtIcon cmtButtonDeleteEntry cmtDialog cmtDialogConfirm" title="Sprachversion l&ouml;schen" href="Javascript:void(0);" data-dialog-title="Sprachversion l&ouml;schen" data-dialog-cancel-url="" data-dialog-confirm-url="{SELFURL}&amp;cmtTab=languages&amp;id[]={VAR:id}&amp;action=deleteLanguage" data-dialog-var="{VAR:cmt_languagename:htmlentities} ({VAR:cmt_language})" data-dialog-content-id="cmtDialogConfirmDeletion"></a>
		<input type="checkbox" class="cmtSelectableStatus cmtIgnore" value="{VAR:id}" name="id[]" />
	</td>	
</tr>
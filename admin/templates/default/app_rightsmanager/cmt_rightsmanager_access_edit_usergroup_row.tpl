<tr class="cmtHover">
	<td class="cmtTableData cmtApplicationName cmtAlternate{VAR:alternationFlag}" style="background-image: url({VAR:applicationIcon})">
	{VAR:cmt_showname}
	</td>
	<td class="cmtTableData cmtApplicationRightCell cmtAlternate{VAR:alternationFlag}">
		<input type="checkbox" name="cmtRights[{VAR:id}][access]" {VAR:accessSelected} class="cmtCheckboxAccess" data-access-for="{VAR:id}" />
	</td>
	<td class="cmtTableData cmtApplicationRightCell cmtAlternate{VAR:alternationFlag}">
		<input type="checkbox" name="cmtRights[{VAR:id}][new]" {VAR:newSelected} />
	</td>
	<td class="cmtTableData cmtApplicationRightCell cmtAlternate{VAR:alternationFlag}">
		<input type="checkbox" name="cmtRights[{VAR:id}][edit]" {VAR:editSelected} />
	</td>
	<td class="cmtTableData cmtApplicationRightCell cmtAlternate{VAR:alternationFlag}">
		<input type="checkbox" name="cmtRights[{VAR:id}][duplicate]" {VAR:duplicateSelected} />
	</td>
	<td class="cmtTableData cmtApplicationRightCell cmtAlternate{VAR:alternationFlag}">
		<input type="checkbox" name="cmtRights[{VAR:id}][delete]" {VAR:deleteSelected} />
	</td>
	<td class="cmtTableData cmtAlternate{VAR:alternationFlag}">
		<input type="checkbox" name="cmtRights[{VAR:id}][view]" {VAR:viewSelected} />
	</td>
</tr>
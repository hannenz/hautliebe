<tr class="cmtHover">
	<td class="cmtTableData cmtApplicationName cmtAlternate{VAR:alternationFlag}" style="background-image: url({VAR:applicationIcon})">
<!--	<img src="" alt="" />-->
	{VAR:cmt_showname}
	</td>
	<td class="cmtTableData cmtApplicationRightCell cmtAlternate{VAR:alternationFlag}">
		{IF ({ISSET:showRightAccess:VAR})}<input type="checkbox" name="cmtRights[{VAR:id}][access]" {VAR:accessSelected} class="cmtCheckboxAccess" data-access-for="{VAR:id}" />{ELSE}&nbsp;{ENDIF}
	</td>
	<td class="cmtTableData cmtApplicationRightCell cmtAlternate{VAR:alternationFlag}">
		{IF ({ISSET:showRightNew:VAR})}<input type="checkbox" name="cmtRights[{VAR:id}][new]" {VAR:newSelected} />{ELSE}&nbsp;{ENDIF}
	</td>
	<td class="cmtTableData cmtApplicationRightCell cmtAlternate{VAR:alternationFlag}">
		{IF ({ISSET:showRightEdit:VAR})}<input type="checkbox" name="cmtRights[{VAR:id}][edit]" {VAR:editSelected} />{ELSE}&nbsp;{ENDIF}
	</td>
	<td class="cmtTableData cmtApplicationRightCell cmtAlternate{VAR:alternationFlag}">
		{IF ({ISSET:showRightDuplicate:VAR})}<input type="checkbox" name="cmtRights[{VAR:id}][duplicate]" {VAR:duplicateSelected} />{ELSE}&nbsp;{ENDIF}
	</td>
	<td class="cmtTableData cmtApplicationRightCell cmtAlternate{VAR:alternationFlag}">
		{IF ({ISSET:showRightDelete:VAR})}<input type="checkbox" name="cmtRights[{VAR:id}][delete]" {VAR:deleteSelected} />{ELSE}&nbsp;{ENDIF}
	</td>
	<td class="cmtTableData cmtApplicationRightCell cmtAlternate{VAR:alternationFlag}">
		{IF ({ISSET:showRightView:VAR})}<input type="checkbox" name="cmtRights[{VAR:id}][view]" {VAR:viewSelected} />{ELSE}&nbsp;{ENDIF}
	</td>
</tr>
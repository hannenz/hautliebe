		{IF({ISSET:dirReadable:VAR})}
		<tr class="cmtHover {IF({ISSET:dirWriteable:VAR})}cmtSelectable{ENDIF}" data-nodetype="directory">
			<td class="cmtDirectoryCell {IF({ISSET:dirWriteable:VAR})}cmtDropZone{ENDIF}" data-path="{VAR:currentDirectory}{VAR:dirName}">
				{IF({ISSET:dirWriteable:VAR})}
				<div class="cmtNodeContent cmtDraggable" data-path="{VAR:currentDirectory}{VAR:dirName}">
					<a href="{SELFURL}&amp;cmtDirectory={VAR:currentDirectory}{VAR:dirName}" data-typecategory="directory" class="cmtDirectory  {IF ("{VAR:dirSize}" == "0")}cmtDirectoryEmpty{ENDIF}"></a>
					<span id="entry{VAR:entryNr}" class="cmtEntryName" data-current-directory="{VAR:currentDirectory}">{VAR:dirName}</span>
					<div class="cmtRenameEntryContainer">
						<input type="text" id="renameEntry{VAR:entryNr}" class="cmtEntryNewName" />
						<a href="#" class="cmtEditFileBack"></a>
						<a href="#" class="cmtEditFileSave"></a>
					</div>
				</div>
				{ELSE}
				<div class="cmtNodeContent">
					<a href="{SELFURL}&amp;cmtDirectory={VAR:currentDirectory}{VAR:dirName}" data-typecategory="directory" class="cmtDirectory  {IF ("{VAR:dirSize}" == "0")}cmtDirectoryEmpty{ENDIF}"></a>
					<span id="entry{VAR:entryNr}" class="">{VAR:dirName}</span>
				</div>
				{ENDIF}
			</td>
		{ELSE}
		<tr class="cmtDisabled" data-nodetype="directory">
			<td class="cmtDirectoryCell">
				<span class="cmtIcon cmtDisabled cmtDirectory cmtDirectoryEmpty"></span>
				<span id="entry{VAR:entryNr}" class="">{VAR:dirName}</span>
			</td>
		{ENDIF}
			<td id="fileType{VAR:entryNr}" >Ordner</td>
			<td>{VAR:dirSize} Element/e</td>
			<td>{VAR:dirDate}</td>
			<td>{VAR:dirRights}</td>
			<td>
				{IF({ISSET:dirWriteable:VAR})}
				<a class="cmtIcon cmtButtonDeleteEntry cmtDialog cmtDialogConfirm" data-title="Verzeichnis l&ouml;schen" data-dialog-cancel-url="" data-dialog-confirm-url="{SELFURL}&amp;action=delete&amp;form_checked[]={VAR:dirName}&amp;cmtDirectory={VAR:currentDirectory}" data-dialog-var="{VAR:dirName}" data-dialog-content-id="cmtDialogConfirmDirectoryDeletion" href="Javascript:void(0);"></a>
				<a class="cmtIcon cmtButtonDuplicateEntry cmtDialog" title="Ordner duplizieren" data-dialog-var="{VAR:dirName}" data-dialog-content-id="cmtDialogDuplicateDirectory" data-dialog-width="400" href="Javascript:void(0);"></a>
				{ELSE}
				<span class="cmtIcon cmtDisabled cmtButtonDeleteEntry"></span>
				<span class="cmtIcon cmtDisabled cmtButtonDuplicateEntry"></span>
				{ENDIF}
				<input type="checkbox" class="cmtSelectableStatus cmtIgnore" value="{VAR:dirName}" name="checked_file" />
			</td>
		</tr>

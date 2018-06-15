		{IF({ISSET:fileReadable:VAR})}
		<tr class="cmtHover {IF({ISSET:fileWriteable:VAR})}cmtSelectable{ENDIF}" data-nodetype="file">
			<td class="cmtFileCell">
		
				{IF({ISSET:fileWriteable:VAR} && {ISSET:currentDirectoryWriteable:VAR})}
				<div class="cmtNodeContent cmtDraggable" data-path="{VAR:currentDirectory}{VAR:fileName}">
					<a class="cmtFile cmtFile{VAR:fileType:strtoupper}" href="{VAR:fileURL}" data-type="{VAR:fileType}" data-typecategory="{VAR:fileTypeCategory}" data-imageWidth="{VAR:imageWidth}" data-imageHeight="{VAR:imageHeight}" data-filepath="{VAR:fileURL}" data-filename="{VAR:fileName}"></a>
					<span id="entry{VAR:entryNr}" class="cmtEntryName" data-current-directory="{VAR:currentDirectory}">{VAR:fileName}</span>
					<div class="cmtRenameEntryContainer">
						<input type="text" id="renameEntry{VAR:entryNr}" class="cmtEntryNewName" />
						<a href="#" class="cmtEditFileBack"></a>
						<a href="#" class="cmtEditFileSave"></a>
					</div>
				</div>
				{ELSE}
				<div class="cmtNodeContent">
					<a class="cmtFile cmtFile{VAR:fileType:strtoupper}" href="{VAR:fileURL}" data-type="{VAR:fileType}" data-typecategory="{VAR:fileTypeCategory}" data-imageWidth="{VAR:imageWidth}" data-imageHeight="{VAR:imageHeight}" data-filepath="{VAR:fileURL}" data-filename="{VAR:fileName}"></a>
					<span id="entry{VAR:entryNr}" class="">{VAR:fileName}</span>
				</div>
				{ENDIF}
			</td>
			<td>{VAR:fileType}</td>			
			<td>{VAR:fileSize}</td>
			<td>{VAR:fileDate}</td>
			<td>{VAR:fileRights}</td>
			<td>
				{IF({ISSET:fileWriteable:VAR}  && {ISSET:currentDirectoryWriteable:VAR})}
				<a class="cmtIcon cmtButtonDeleteEntry cmtDialog cmtDialogConfirm" title="Datei l&ouml;schen" data-dialog-cancel-url="" data-dialog-confirm-url="{SELFURL}&amp;cmtAction=delete&amp;form_checked[]={VAR:fileName}&amp;cmtDirectory={VAR:currentDirectory}" data-dialog-var="{VAR:fileName}" data-dialog-content-id="cmtDialogConfirmFileDeletion" href="Javascript:void(0);"></a>
				<a class="cmtIcon cmtButtonDuplicateEntry cmtDialog" title="Datei duplizieren" data-dialog-var="{VAR:fileName}" data-dialog-content-id="cmtDialogDuplicateFile" data-dialog-width="400" href="Javascript:void(0);"></a>
				{ELSE}
				<span class="cmtIcon cmtDisabled cmtButtonDeleteEntry"></span>
				<span class="cmtIcon cmtDisabled cmtButtonDuplicateEntry"></span>
				{ENDIF}
				<a class="cmtIcon cmtButtonDownloadFile" href="{SELFURL}&amp;action=download&amp;form_checked[]={VAR:fileName}&amp;cmtDirectory={VAR:currentDirectory}" id="entry{VAR:entryNr}Download"></a>
				<input type="checkbox" class="cmtSelectableStatus cmtIgnore" value="{VAR:fileName}" name="checked_file" />
			</td>
		</tr>
		{ELSE}
		<tr class="cmtDisabled" data-nodetype="file">
			<td class="cmtFileCell">
				<span class="cmtIcon cmtDisabled cmtFile cmtFile{VAR:fileType:strtoupper}"></span>
				<span id="entry{VAR:entryNr}" class="">{VAR:fileName}</span>
			</td>
			<td>{VAR:fileType}</td>			
			<td>{VAR:fileSize}</td>
			<td>{VAR:fileDate}</td>
			<td>{VAR:fileRights}</td>
			<td>
				<span class="cmtIcon cmtDisabled cmtButtonDeleteEntry"></span>
				<span class="cmtIcon cmtDisabled cmtButtonDuplicateEntry"></span>
				<span class="cmtIcon cmtDisabled cmtButtonDownloadFile"></span>
			</td>
		</tr>
		{ENDIF}
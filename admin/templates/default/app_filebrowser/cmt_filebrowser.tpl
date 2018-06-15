<script type="text/javascript" src="{PATHTOADMIN}javascript/ace/ace.js"></script>
<link rel="Stylesheet" href="{CMT_TEMPLATE}/app_filebrowser/css/cmt_filebrowser_style.css" type="text/css">
<script src="{PATHTOADMIN}javascript/jquery-plugins/jquery-cookie/jquery.cookie.js" type="text/javascript"></script>
<script type="text/javascript" src="{PATHTOADMIN}javascript/jquery-plugins/jquery-ui-treetable/ui.treetable.js"></script>
<script src="{CMT_TEMPLATE}app_filebrowser/javascript/cmt_filebrowser_functions.js" type="text/javascript"></script>

<div id="cmtHeaderContainer">
	<div class="appHeadlineContainer">
		<img class="appIcon" src="{IF ({ISSET:icon:VAR})}{VAR:icon}{ELSE}{CMT_TEMPLATE}app_filebrowser/img/icon.png{ENDIF}" alt="" />
		<h1>{IF ({ISSET:show_name:VAR})}{VAR:show_name}{ELSE}Dateimanager{ENDIF}</h1>
	</div>
	
	<!-- Message -->
	<div id="cmtMessageContainer">
		{IF ({ISSET:errorMessage:VAR})}
		<div class="cmtMessage cmtMessageError">
			{IF ({ISSET:mkdirErrorDirExists:VAR})}Das Verzeichnis "{VAR:newDirName}" existiert bereits.{ENDIF}
			{IF ({ISSET:mkdirError:VAR})}Das Verzeichnis "{VAR:newDirName}" konnte nicht erstellen werden. Sie haben wahrscheinlich nicht ausreichend Dateibearbeitungsrechte.{ENDIF}
			{IF ({ISSET:deleteErrorDir:VAR})}
				{IF ("{VAR:deleteDirsNumber}" > 1 )}
				Folgende Verzeichnisse konnten nicht gelöscht werden: "{VAR:deleteDirNames}"<br />
				{ELSE}
				Konnte das Verzeichnis "{VAR:deleteDirNames}" nicht löschen.<br />
				{ENDIF}
			{ENDIF}
			{IF ({ISSET:deleteErrorFile:VAR})}
				{IF ("{VAR:deleteFilesNumber}" > 1 )}
				Folgende Dateien konnten nicht gelöscht werden: "{VAR:deleteFileNames}"
				{ELSE}
				Konnte die Datei "{VAR:deleteFileNames}" nicht löschen.
				{ENDIF}
			{ENDIF}
			{IF ({ISSET:deleteErrorNoSelection:VAR})}Bitte w&auml;hlen Sie mindestens eine Datei oder ein Verzeichnis aus, das gel&ouml;scht werden soll.{ENDIF}
			{IF ({ISSET:moveErrorNoSelection:VAR})}Bitte w&auml;hlen Sie mindestens eine Datei oder ein Verzeichnis aus, das verschoben werden soll.{ENDIF}
			{IF ({ISSET:moveErrorDirExists:VAR})}Move directory failed, Directory is allready exists.{ENDIF}
			{IF ({ISSET:moveErrorFileExists:VAR})}Move File failed, File is allready exists.{ENDIF}
			{IF ({ISSET:moveError:VAR})}Move File/directory failed: {VAR:moveError}.{ENDIF}
			{IF ({ISSET:newFileExistsError:VAR})}Die Datei "{VAR:newFileName}" konnte nicht angelegt werden, da sie bereits existiert.{ENDIF}
			{IF ({ISSET:newFileCreationError:VAR})}Die Datei "{VAR:newFileName}" konnte nicht angelegt werden.{ENDIF}
			{IF ({ISSET:uploadError:VAR})}Die Datei/en konnte/n nicht hochgeladen werden. Bitte überprüfen Sie Ihre Schreibreche für diesen Ordner.{ENDIF}
			{IF ({ISSET:duplicateFileNameMissingError:VAR})}Bitte geben Sie einen Namen für die Datei ein, die dupliziert werden soll.{ENDIF}
			{IF ({ISSET:duplicateFileSameNameError:VAR})}Ursprungs- und Zieldatei haben den selben Namen.{ENDIF}
			{IF ({ISSET:duplicateFileError:VAR})}Die Datei konnte nicht dupliziert werden.{ENDIF}
			{IF ({ISSET:duplicateFileExistsError:VAR})}Eine Datei mit gleichem Namen existiert bereits.{ENDIF}
		</div>
		{ENDIF}
		{IF ({ISSET:successMessage:VAR})}
		<div class="cmtMessage cmtMessageSuccess">
			{IF ({ISSET:mkdirSuccess:VAR})}Das Verzeichnis "{VAR:newDirName}" wurde erfolgreich erstellt.{ENDIF}
			{IF ({ISSET:deleteSuccessDir:VAR})}
				{IF ("{VAR:deleteDirsNumber}" > 1 )}
				Folgende Verzeichnisse wurden gelöscht: "{VAR:deleteDirNames}"<br />
				{ELSE}
				Das Verzeichnis "{VAR:deleteDirNames}" wurde gelöscht.<br />
				{ENDIF}
			{ENDIF}
			{IF ({ISSET:deleteSuccessFile:VAR})}
				{IF ("{VAR:deleteFilesNumber}" > 1 )}
				Folgende Dateien wurden gelöscht: "{VAR:deleteFileNames}"
				{ELSE}
				Die Datei "{VAR:deleteFileNames}" wurde gelöscht.
				{ENDIF}
			{ENDIF}
			{IF ({ISSET:moveSuccess:VAR})}Files and/or Dircetories moved successfully{VAR:deleteSuccessFileNames}{ENDIF}
			{IF ({ISSET:newFileSuccess:VAR})}Die Datei "{VAR:newFileName}" wurde erfolgreich angelegt.{ENDIF}
			{IF ({ISSET:uploadSuccess:VAR})}Die Datei/en wurde/n erfolgreich hochgeladen.{ENDIF}
			{IF ({ISSET:duplicateFileSuccess:VAR})}Die Datei wurde erfolgreich dupliziert.{ENDIF}
		</div>
		{ENDIF}
	</div>
	
	<!-- action buttons container -->
	<div class="serviceContainer">
		<div class="serviceContainerInner clearfix">
			<button id="cmtButtonNewDirectory" class="cmtButton cmtButtonNewDirectory {IF (!{ISSET:currentDirectoryWriteable:VAR})}cmtDisabled{ENDIF} cmtDialog" data-dialog-content-id="cmtDialogNewDirectory" data-dialog-width="400">Neues Verzeichnis</button>
			<button id="cmtButtonNewFile" class="cmtButton cmtButtonNewFile {IF (!{ISSET:currentDirectoryWriteable:VAR})}cmtDisabled{ENDIF} cmtDialog" data-dialog-content-id="cmtDialogNewFile" data-dialog-width="400">Neue Datei</button>			
<!-- 			<button id="cmtButtonUploadFiles" class="cmtButton cmtButtonUploadFile {IF (!{ISSET:currentDirectoryWriteable:VAR})}cmtDisabled{ENDIF}">Dateien hochladen</button> -->
			<form style="display:inline" action="" enctype="multipart/form-data" method="POST">
				<input type="file" id="cmtUploadFiles" multiple name="cmtUploadFiles[]" class="cmtFormCustomFileInput" data-label-text="Datei(en) hochladen">
				<input type="hidden" name="cmtAction" value="upload">
				<input type="hidden" name="cmtUploadURL" id="cmtUploadURL" value="{SELFURL}&cmtAction=upload">
			</form>
		</div>
	</div>
	<div class="serviceContainer">
		<div class="serviceContainerInner clearfix isActive serviceText" id="cmtDropUploadSelect">
			Um Dateien hochzuladen klicken Sie entweder auf den oben stehenden Knopf oder ziehen Sie die Dateien von Ihrem Desktop in die Ordneransicht (rechte Spalte).
		</div>
		<div class="serviceContainerInner clearfix" id="cmtDropUploadProgressBar">
			<div id="progressBar" class="cmtProgressbar" data-label-value="true" data-label-value-entity="%">
				<div class="cmtLabel"></div>
			</div>
		</div>
	</div>

	
	<!-- Breadcrumb-->
	<div class="serviceContainer">
		<div class="serviceContainerInner clearfix">
			<span class="serviceText">Aktuelles Verzeichnis:</span>&nbsp;&nbsp;<a class="cmtBreadcrumb" href="{SELFURL}&amp;cmtDirectory=">Root</a>{VAR:breadcrumbNavigation}
		</div>
	</div>
</div>
<div id="cmtContentContainer">

	<!-- Directory and files contnent -->
	<div id="directoriesContentContainer" class="clearfix">
		<div id="directoriesColumn" class="cmtAdjustHeight">
			<table id="cmtDirectoriesTable" class="cmtTable cmtIgnore">
				<thead>
					<tr>
						<td class="tableDataHeadCell">
							<div class="tableHeadText">Verzeichnis</div>
						</td>
					</tr>
				</thead>
				<tbody>
					{VAR:dirTree}
				</tbody>
			</table>
		</div>
		<div id="filesColumn" class="cmtAdjustHeight" data-path="{VAR:currentDirectory}">
			<table id="cmtFilesTable" class="cmtTable cmtIgnore">
				<thead>
					<tr>
						<td class="tableDataHeadCell">
							<div class="tableHeadText">Datei<div class="tableHeadSortDirSelect"><a href="{SELFURL}&amp;sortBy=fileName&amp;sortDir=desc&amp;cmtDirectory={VAR:directory}"><img src="{CMT_TEMPLATE}general/img/icon_desc.gif" class="imageLinked"></a>&nbsp;<a href="{SELFURL}&amp;sortBy=fileName&amp;sortDir=asc&amp;cmtDirectory={VAR:directory}"><img src="{CMT_TEMPLATE}general/img/icon_asc.gif" class="imageLinked"></a></div></div>
						</td>
						<td class="tableDataHeadCell">
							<div class="tableHeadText">Typ<div class="tableHeadSortDirSelect"><a href="{SELFURL}&amp;sortBy=fileType&amp;sortDir=desc&amp;cmtDirectory={VAR:directory}"><img src="{CMT_TEMPLATE}general/img/icon_desc.gif" class="imageLinked"></a>&nbsp;<a href="{SELFURL}&amp;sortBy=fileType&amp;sortDir=asc&amp;cmtDirectory={VAR:directory}"><img src="{CMT_TEMPLATE}general/img/icon_asc.gif" class="imageLinked"></a></div></div>
						</td>
						<td class="tableDataHeadCell">
							<div class="tableHeadText">Gr&ouml;ße<div class="tableHeadSortDirSelect"><a href="{SELFURL}&amp;sortBy=fileSize&amp;sortDir=desc&amp;cmtDirectory={VAR:directory}"><img src="{CMT_TEMPLATE}general/img/icon_desc.gif" class="imageLinked"></a>&nbsp;<a href="{SELFURL}&amp;sortBy=fileSize&amp;sortDir=asc&amp;cmtDirectory={VAR:directory}"><img src="{CMT_TEMPLATE}general/img/icon_asc.gif" class="imageLinked"></a></div></div>
						</td>
						<td class="tableDataHeadCell">
							<div class="tableHeadText">erstellt am<div class="tableHeadSortDirSelect"><a href="{SELFURL}&amp;sortBy=fileDate&amp;sortDir=desc&amp;cmtDirectory={VAR:directory}"><img src="{CMT_TEMPLATE}general/img/icon_desc.gif" class="imageLinked"></a>&nbsp;<a href="{SELFURL}&amp;sortBy=fileDate&amp;sortDir=asc&amp;cmtDirectory={VAR:directory}"><img src="{CMT_TEMPLATE}general/img/icon_asc.gif" class="imageLinked"></a></div></div>
						</td>
						<td class="tableDataHeadCell">Rechte</td>
						<td class="tableActionHeadCell">Funktionen</td>
					</tr>
				</thead>
				<tbody>
					{VAR:dirContent}
				</tbody>
			</table>
		</div>
	</div>
</div>
<div id="cmtFooterContainer">
	<div class="cmtServiceFooter serviceContainer">
		<button class="cmtButton cmtButtonSelectMultiple cmtSelectableSelectAll">alle ausw&auml;hlen</button>
		<button class="cmtButton cmtButtonUnselectMultiple cmtSelectableUnselectAll">Auswahl entfernen</button>
		<button id="cmtButtonDeleteSelectedFiles" class="cmtButton cmtButtonDeleteMultiple">alle l&ouml;schen</button>
	</div>
</div>

<!-- delete selected file dialog form -->
<div id="cmtDialogConfirmFileDeletion" class="cmtDialogContentContainer" title="Datei löschen">
	<div class="cmtDialogContent">
		<span class="cmtDialogVar"></span>
		M&ouml;chten Sie diese Datei wirklich l&ouml;schen?
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">l&ouml;schen</span>
	</div>
</div>

<!-- delete selected directory dialog form -->
<div id="cmtDialogConfirmDirectoryDeletion" class="cmtDialogContentContainer" title="Verzeichnis löschen">
	<div class="cmtDialogContent">
		<p>M&ouml;chten Sie dieses Verzeichnis wirklich l&ouml;schen?</p>
		<span class="cmtDialogVar"></span>
		<p>Achtung, es werden auch alle Dateien in diesem Verzeichnis gelöscht!</p>
	</div>
	<div class="cmtDialogButtons">
		<span class="cmtButtonCancelText" data-button-class="cmtButtonBack">abbrechen</span>
		<span class="cmtButtonConfirmText" data-button-class="cmtButtonDelete">l&ouml;schen</span>
	</div>
</div>

<!-- create new file dialog -->
<div id="cmtDialogNewFile" class="cmtDialogContentContainer" title="Neue Datei erstellen">
	<div class="cmtDialogContent"> 
		<form id="cmtNewFileForm" action="" method="POST" enctype="application/x-www-form-urlencoded">
		 	<p> 
				Geben Sie hier den Namen der neuen Datei ein.
		 	</p>
		 	<p>
		 		<input type="text" name="cmtNewFile" value="" />
		 	</p>
			<div class="cmtDialogButtons">
				<span class="cmtButton cmtButtonBack cmtDialogClose">abbrechen</span>
				<button class="cmtButton cmtButtonNewFile" type="submit">Datei erstellen</button>
	 			<input type="hidden" name="cmtAction" value="newFile">
	 			<input type="hidden" name="cmtDirectory" value="{VAR:directory}">
			</div>
		</form>
	</div>
</div>

<!-- new directory dialog -->
<div id="cmtDialogNewDirectory" class="cmtDialogContentContainer" title="Neues Verzeichnis erstellen">
	<div class="cmtDialogContent">
		<form id="cmtNewDirectoryForm" enctype="application/x-www-form-urlencoded" method="POST" action="" name="cmtNewDirectoryForm">
			<p>Geben Sie den Namen des neuen Verzeichnisses an.<br />Verwenden Sie dabei keine Sonderzeichen wie Umlaute oder Leerzeichen.</p>
			<p><input type="text" name="cmtNewDirectoryName" /></p>
			<div class="cmtDialogButtons">
				<button class="cmtButton cmtButtonBack cmtDialogClose" type="button">abbrechen</button>
				<button class="cmtButton cmtButtonNewDirectory" type="submit">Verzeichnis erstellen</button>
				<input type="hidden" value="makedir" name="cmtAction">
				<input type="hidden" name="cmtDirectory" value="{VAR:directory}">
			</div>
		</form>
	</div>
</div>

<!-- duplicate file dialog -->
<div id="cmtDialogDuplicateFile" class="cmtDialogContentContainer" title="Datei duplizieren">
	<div class="cmtDialogContent"> 
		<form id="cmtDuplicateFileForm" action="" method="POST" enctype="application/x-www-form-urlencoded">
		 	<p> 
				Geben Sie hier den neuen Namen der Datei ein.
		 	</p>
		 	<p>
		 		<input type="text" name="cmtTargetFile" class="cmtDialogVar" value="" />
		 	</p>
			<div class="cmtDialogButtons">
				<span class="cmtButton cmtButtonBack cmtDialogClose">abbrechen</span>
				<button class="cmtButton cmtButtonDuplicateEntry" type="submit">Datei duplizieren</button>
	 			<input type="hidden" name="cmtAction" value="duplicateFile">
	 			<input type="hidden" name="cmtSourceFile" value="" class="cmtDialogVar">
	 			<input type="hidden" name="cmtDirectory" value="{VAR:directory}">
			</div>
		</form>
	</div>
</div>

<!-- duplicate directory dialog -->
<div id="cmtDialogDuplicateDirectory" class="cmtDialogContentContainer" title="Ordner duplizieren">
	<div class="cmtDialogContent"> 
		<form id="cmtDuplicateFileForm" action="" method="POST" enctype="application/x-www-form-urlencoded">
		 	<p> 
				Geben Sie hier den neuen Namen des Ordners ein.
		 	</p>
		 	<p>
		 		<input type="text" name="cmtTargetDirectory" class="cmtDialogVar" value="" />
		 	</p>
			<div class="cmtDialogButtons">
				<span class="cmtButton cmtButtonBack cmtDialogClose">abbrechen</span>
				<button class="cmtButton cmtButtonDuplicateEntry" type="submit">Ordner duplizieren</button>
	 			<input type="hidden" name="cmtAction" value="duplicateDirectory">
	 			<input type="hidden" name="cmtSourceDirectory" value="" class="cmtDialogVar">
	 			<input type="hidden" name="cmtDirectory" value="{VAR:directory}">
			</div>
		</form>
	</div>
</div>
<!-- <div id="deleteSelectedFilesDialog" class="cmtDialogContentContainer" title="Dateien und Verzeichnisse löschen">  -->
<!--  	<p>  -->
<!--  		<span class="formMessage">Are you sure?</span>  -->
<!--  	</p>  -->
<!--  	<form id="deleteSelected" action="" method="POST" enctype="application/x-www-form-urlencoded">  -->
<!--  		<input type="hidden" class="formChecked" name="form_checked" value="">  -->
<!--  		<input type="hidden" name="action" value="delete">  -->
<!--  	</form> -->
<!-- 	<div class="cmtDialogButtons ui-dialog-buttonpane ui-widget-content ui-helper-clearfix"> -->
<!-- 		<div class="ui-dialog-buttonset"> -->
<!-- 			<span class="cmtButton cmtButtonBack">abbrechen</span> -->
<!-- 			<span class="cmtButton cmtButtonSave">speichern</span> -->
<!-- 		</div> -->
<!-- 	</div>  -->
<!--  </div>  -->

<div id="cmtDialogDeleteMultiple" class="cmtDialogContentContainer" title="Dateien und Verzeichnisse löschen">
	<div class="cmtServiceContainer">
		<form id="cmtDeleteMultipleForm" enctype="application/x-www-form-urlencoded" method="POST" action="" name="cmtNewDirectoryForm">
			<p>Sind Sie sicher, dass Sie diese Einträge löschen wollen?</p>
			<div class="cmtDialogButtons">
				<button class="cmtButton cmtButtonBack cmtDialogClose" type="button">abbrechen</button>
				<button class="cmtButton cmtButtonDeleteMultiple" type="submit">alle löschen</button>
		 		<input type="hidden" id="cmtSelectedEntries" name="form_checked" value=""> 
		 		<input type="hidden" name="cmtAction" value="delete"> 
			</div>
		</form>
	</div>
</div>

<!-- upload files dialog form -->
<div id="cmtDialogUploadFiles" class="cmtDialogContentContainer" data-title="Dateien hochladen">
	<div class="cmtServiceContainer">
		<p>
			<span class="serviceText">Max. m&ouml;gliche Dateigr&ouml;&szlig;e (pro Datei): <strong>{VAR:maxUploadFileSize}</strong></span>
		</p>
		{VAR:uploadFields}
	</div>
</div>

<!-- Image preview window -->
<div id="cmtDialogPreviewImage" title="" class="cmtDialogContentContainer"></div>

<!-- text editor dialog -->
<div id="cmtDialogTextEditor" class="cmtDialogContentContainer">
<!-- 	<div id="cmtTextEditor" data-language="text"></div> -->
	<div class="cmtDialogButtons ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
		<div class="ui-dialog-buttonset">
			<span class="cmtButton cmtButtonBack">abbrechen</span>
			<span class="cmtButton cmtButtonSave">speichern</span>
		</div>
	</div>
</div>

<!-- Ajax Messages -->
<div class="cmtDialogContentContainer">
	<div id="cmtRenameError">
		"<span class="cmtDialogVar cmtDialogVar1"></span>" konnte nicht in "<span class="cmtDialogVar cmtDialogVar2"></span>" umbenannt werden. Bitte &uuml;berpr&uuml;fen Sie Ihre Zugriffsrechte.
	</div>
	<div id="cmtRenameSuccess">
		"<span class="cmtDialogVar cmtDialogVar1"></span>" wurde erfolgreich in "<span class="cmtDialogVar cmtDialogVar2"></span>" umbenannt.
	</div>
	<div id="cmtSaveEditFileError">
		Die Datei konnte nicht gespeichert werden. Wahrscheinlich sind die Dateirechte nicht ausreichend gesetzt.
	</div>
	<div id="cmtSaveEditFileSuccess">
		Die Datei wurde erfolgreich gespeichert.
	</div>
	<div id="cmtMoveError">
		"<span class="cmtDialogVar cmtDialogVar1"></span>" konnte nicht nach "<span class="cmtDialogVar cmtDialogVar2"></span>" verschoben werden. Bitte &uuml;berpr&uuml;fen Sie Ihre Zugriffsrechte.
	</div>
	<div id="cmtMoveSuccess">
		"<span class="cmtDialogVar cmtDialogVar1"></span>" wurde erfolgreich nach "<span class="cmtDialogVar cmtDialogVar2"></span>" verschoben.
	</div>
</div>

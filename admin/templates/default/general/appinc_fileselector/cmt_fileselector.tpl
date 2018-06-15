<div id="cmtFileSelector">
	<div id="cmtFileSelectorBreadcrumbsContainer">
		<span class="serviceText">Verzeichnis:</span>&nbsp;&nbsp;
		<span id="cmtFileSelectorBreadcrumbs">
			{VAR:breadcrumbNavigation}
		</span>
	</div>
	<div id="cmtFileSelectorContentContainer">
		<table class="">
			<thead>
				<tr>
					<td class="cmtfileSelectorColName">Name</td>
					<td class="cmtfileSelectorColType">Typ</td>
					<td class="cmtfileSelectorColSize">Gr&ouml;&szlig;e</td>
					<td class="cmtfileSelectorColDate">Datum</td>
				</tr>
			</thead>
			<tbody id="cmtFileSelectorDirectoryContent">
			{VAR:directoryContent}
			{VAR:fileContent}
			</tbody>
		</table>
	</div>
	<div id="cmtFileSelectorFormContainer">
		<form action="" method="">
			<label for="cmtFileSelectorPath" class="fileSelectorInputLabel">Datei- / Verzeichnis-Pfad</label>
			<input type="text" name="cmtFileSelectorPath" id="cmtFileSelectorPath" value="{VAR:fsPath}" class="cmtFileSelectorPathInput">
			<a href="Javascript:void(0)" class="cmtButton cmtButtonSave" id="cmtFileSelectorSave">&uuml;bernehmen</a>
			<a href="Javascript:void(0)" class="cmtButton cmtButtonBack" id="cmtFileSelectorCancel">abbrechen</a> 
		</form>
	</div>
</div>
{VAR:cmtFileSelectorJavascript1}
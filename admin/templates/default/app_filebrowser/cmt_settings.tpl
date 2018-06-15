<form name="editSettings" action="{SELFURL}" method="POST" enctype="multipart/form-data">

	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Alternativer Name f&uuml;r diesen Dateimanager</div>
		<div class="cmtEditRowField">
			<input type="text" name="cmtsetting_show_name" id="cmtsetting_show_name" value="{VAR:show_name}" />
		</div>
		<div class="cmtEditRowDescription">
			Hier kann ein alternativer Name f&uuml;r diesen Dateimanager angegeben werden. Ist sinnvoll, wenn es mehrere Dateimanager-Anwendungen im System gibt.
		</div>
	</div>


	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Root-Verzeichnis</div>
		<div class="cmtEditRowField">
			<input class="cmtFormFile" type="text" name="cmtsetting_root" id="cmtsetting_root" value="{VAR:root}" />&nbsp;
			<a href="{SELFURL}&amp;action={VAR:action}&amp;cmt_extapp=fileselector" data-field="cmtsetting_root" class="cmtButton cmtButtonSelectFile cmtDialog cmtDialogLoadContent cmtFileSelector">Pfad wählen</a>
		</div>
		<div class="cmtEditRowDescription">
			W&auml;hlen Sie hier das Stamm-/ Root-Verzeichnis für diesen Dateimanager aus.
		</div>
	</div>

	<div class="cmtEditEntryRow cmtEditEntryRow{ALTERNATIONFLAG}">
		<div class="cmtEditRowHead">Anzahl der Upload-Felder</div>
		<div class="cmtEditRowField">
			<input type="text" name="cmtsetting_uploads" id="cmtsetting_uploads" value="{VAR:uploads}" />
		</div>
		<div class="cmtEditRowDescription">
			Geben Sie hier die Anzahl der Upload-Felder an, die angezeigt werden sollen. Der Benutzer kann in diesem Dateinmanager anschlie&szlig;end soviele Dateien auf einmal hochladen.
		</div>
	</div>
	
	<div class="serviceContainer">
		<button class="cmtButton cmtButtonSave" name="action">speichern</button>
		<input type="hidden" name="sid" value="{SID}"/>
		<input type="hidden" name="cmt_slider" value="{VAR:cmt_slider}"/>
		<input type="hidden" name="id" value="{VAR:tableId}"/>
		<input type="hidden" name="iconPath" value="{VAR:iconPath}"/>	
		<input type="hidden" name="action" value="save"/>
	</div>
</form>
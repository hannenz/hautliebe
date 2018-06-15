<form name="editSettings" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">
	<div class="editEntrySubHead">Dateimanager</div>
	<div class="dataRow{ALTERNATIONFLAG:0}">
		Icon:<br>
		{IF ("{VAR:icon}" != 'none')}<img src="{VAR:iconPath}">&nbsp;&nbsp;{ENDIF}
		<input type="radio" name="cmtsetting_icon" value="default"{IF ("{VAR:icon}" == "default")} checked="checked"{ENDIF}>&nbsp;Standard-Icon
		<input type="radio" name="cmtsetting_icon" value="none"{IF ("{VAR:icon}" == "none")} checked="checked"{ENDIF}>&nbsp;kein Icon
		<input type="radio" name="cmtsetting_icon" value="otherIcon"{IF ("{VAR:icon}" == "otherIcon")} checked="checked"{ENDIF}>&nbsp;individuelles Icon
		&nbsp;<input type="file" name="cmt_uploadicon">
	</div>

	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="formLabel">Alternativer Name für das Import-/Export-Modul</div>
		<div class="formField">
			<input name=name="cmtsetting_show_name" value="{VAR:show_name}">
		</div>
<!--		<div class="formDescription">
			Ein alternativer Name für die Titelzeile des Dateimanagers kann hier angegeben werden.
		</div>-->
	</div>
	
	<div class="editEntrySubHead">Grundeinstellungen</div>
	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="formLabel">Import/Export-Pfad</div>
		<div class="formField">
		<select name="cmtsetting_import_directory" class="formSelectFiles">
		<option value="{CONSTANT:WEBROOT}">Root-Verzeichnis</option>
		{LOOP DIRECTORY ({PATHTOWEBROOT}:showOnlyDirectories = true; fileTypes=php,html)}
		<option value="{FILE}"{IF ("{FILE}" == "{VAR:import_directory}")} selected="selected"{ENDIF}>{FILE}</option>{ENDLOOP DIRECTORY}
		</select>
		</div>
		<div class="formDescription">
			Startverzeichnis in welchem sich Import-Dateien befinden und in welches Export-Dateien abgelegt werden.
		</div>
	</div>
	
	<div class="serviceBottom"><input src="{CMT_TEMPLATE}general/img/icon_save.gif" name="action" value="save" type="image" style="margin: 0px;"/></div>
	<input type="hidden" name="sid" value="{SID}"/>
	<input type="hidden" name="cmt_slider" value="{VAR:cmt_slider}"/>
	<input type="hidden" name="id" value="{VAR:tableId}"/>
	<input type="hidden" name="iconPath" value="{VAR:iconPath}"/>
	<input type="hidden" name="action" value="save"/>
</form>
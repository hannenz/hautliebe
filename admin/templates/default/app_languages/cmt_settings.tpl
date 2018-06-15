<form name="editSettings" action="{SELFURL}" method="POST" enctype="multipart/form-data">
	<div class="editEntrySubHead">Dateimanager</div>
	<div class="dataRow{ALTERNATIONFLAG:0}">
		Icon:<br>
		{IF ({ISSET:icon:VAR})}<img src="{VAR:pathToIcon}{VAR:icon}">&nbsp;&nbsp;{ENDIF}
		<input type="radio" name="cmtsetting_icon" value="cmt_defaulttableicon.png"{IF ("{VAR:icon}" == "cmt_defaulttableicon.png" || "{VAR:icon}" == "icon.png")} checked="checked"{ENDIF}>&nbsp;Standard-Icon
		<input type="radio" name="cmtsetting_icon" value="none"{IF (!{ISSET:icon:VAR})} checked="checked"{ENDIF}>&nbsp;kein Icon
		<input type="radio" name="cmtsetting_icon" value="otherIcon"{IF ({ISSET:icon:VAR} && "{VAR:icon}" != "cmt_defaulttableicon.png" && "{VAR:icon}" != "icon.png")} checked="checked"{ENDIF}>&nbsp;individuelles Icon
		&nbsp;<input type="file" name="cmt_uploadicon">
	</div>

	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="formLabel">Alternativer Name für den Tabellenmanager</div>
		<div class="formField">
			<input name=name="cmtsetting_show_name" value="{VAR:show_name}">
		</div>
	</div>
	<div class="serviceBottom"><input src="{CMT_TEMPLATE}general/img/icon_save.gif" name="action" value="save" type="image" style="margin: 0px;"/></div>
	<input type="hidden" name="sid" value="{SID}"/>
	<input type="hidden" name="cmt_slider" value="{VAR:cmt_slider}"/>
	<input type="hidden" name="id" value="{VAR:tableId}"/>
	<input type="hidden" name="iconPath" value="{VAR:iconPath}"/>	
	<input type="hidden" name="action" value="save"/>
</form>
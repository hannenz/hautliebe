<form name="editSettings" action="{SELFURL}" method="POST" enctype="multipart/form-data">
<!-- 	<div class="editEntrySubHead">Tabellenmanager</div> -->
<!-- 	<div class="dataRow{ALTERNATIONFLAG:0}"> -->
<!-- 		Icon:<br> -->
<!-- 		{IF ({ISSET:iconPath:VAR})}<img src="{VAR:iconPath}">&nbsp;&nbsp;{ENDIF} -->
<!-- 		<input type="radio" name="cmtsetting_icon" value="default"{IF ("{VAR:icon}" == "default")} checked="checked"{ENDIF}>&nbsp;Standard-Icon -->
<!-- 		<input type="radio" name="cmtsetting_icon" value="none"{IF ("{VAR:icon}" == "none")} checked="checked"{ENDIF}>&nbsp;kein Icon -->
<!-- 		<input type="radio" name="cmtsetting_icon" value="otherIcon"{IF ("{VAR:icon}" == "otherIcon")} checked="checked"{ENDIF}>&nbsp;individuelles Icon -->
<!-- 		&nbsp;<input type="file" name="cmt_uploadicon"> -->
<!-- 	</div> -->

	<div class="dataRow{ALTERNATIONFLAG}">
		<div class="formLabel">Alternativer Name f�r den Tabellenmanager</div>
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
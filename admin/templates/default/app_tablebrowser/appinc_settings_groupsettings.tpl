<form name="editSettings" action="{SELFURL}" method="POST" enctype="multipart/form-data">
	<div class="editEntrySubHead">Erweiterte Gruppeneingeschaften</div>
	<div class="dataRow{ALTERNATIONFLAG:0}">
		Icon:<br>
		{IF ({ISSET:iconPath:VAR})}<img src="{CMT_TEMPLATE}{VAR:iconPath}">&nbsp;&nbsp;{ENDIF}
		<div style="float: left; margin-right: 12px;">
			<input type="radio" name="cmtsetting_icon" value="default"{IF ("{VAR:icon}" == "default")} checked="checked"{ENDIF}>&nbsp;Standard-Icon
		</div>
		<div style="float: left; margin-right: 12px;">		
			<input type="radio" name="cmtsetting_icon" value="none"{IF ("{VAR:icon}" == "none")} checked="checked"{ENDIF}>&nbsp;kein Icon
		</div>
		<div style="float: left; margin-right: 12px;">		
			<input type="radio" name="cmtsetting_icon" value="otherIcon"{IF ("{VAR:icon}" == "otherIcon")} checked="checked"{ENDIF}>&nbsp;individuelles Icon: &nbsp;<input type="text" value="{VAR:iconPath}" name="cmtsetting_iconPath">
			<div style="padding: 4px 0px 0px 24px">neues Icon hochladen: <input type="file" name="cmt_uploadicon"></div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="serviceBottom" style="clear: both"><input src="{CMT_TEMPLATE}general/img/icon_save.gif" name="action" value="save" type="image" style="margin: 0px;"/></div>
	<input type="hidden" name="sid" value="{SID}"/>
	<input type="hidden" name="cmt_slider" value="{VAR:cmt_slider}"/>
	<input type="hidden" name="id" value="{VAR:groupId}"/>
<!--	<input type="hidden" name="iconPath" value="{VAR:iconPath}"/> -->
	<input type="hidden" name="action" value="save"/>
	</div>
</form>
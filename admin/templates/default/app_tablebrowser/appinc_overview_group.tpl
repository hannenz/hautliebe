<div class="groupRow clearFloats">
	<div class="groupOpenClose">
		<a href="Javascript: void(0);" onclick="changeLayer('groupLayer{VAR:id}', 'groupLayer{VAR:id}_img'); this.blur();">
			<img id="groupLayer{VAR:id}_img" src="{CMT_TEMPLATE}/general/img/arrow_blue_close.gif" class="imageLinked" alt="" />
		</a>
	</div>
	<div class="groupIcon">
		{IF ({ISSET:cmt_isimportgroup:VAR})}<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_group_import.gif">{ELSE}
		<a href="{SELFURL}&action=change_visibility&cmt_visible={VAR:cmt_visible}&cmt_slider={VAR:cmt_slider}&cmt_type=group&id[]={VAR:id}">
			{IF ({VAR:cmt_visible} == 0 || {VAR:itemsInGroup} == 0)}
				<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_group_disabled.gif" class="imageLinked" alt="" />
			{ELSE}
				<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_group.gif" class="imageLinked" alt="" />
			{ENDIF}
		</a>
		{ENDIF}
	</div>
	<div class="groupName">
		{VAR:cmt_groupname}
	</div>
	<div class="groupEditButtons">
		<div class="groupButton">
			<a href="{SELFURL}&action=edit&cmt_type=group&id[]={VAR:id}"><img src="{CMT_TEMPLATE}general/img/icon_edit_24px.png" class="imageLinked" alt="" /></a>
		</div>
		<div class="groupButton">
			<a href="{SELFURL}&cmt_type=group&action=duplicate&id[]={VAR:id}"><img src="{CMT_TEMPLATE}general/img/icon_duplicate_24px.png" class="imageLinked" alt="" /></a>
		</div>
		<div class="groupButton">
			<a href="{SELFURL}&cmt_type=group&action=delete&id[]={VAR:id}"  onClick="return del_confirm_text('Wollen Sie die Gruppe {VAR:cmt_groupname} wirklich löschen? Es wird nur die Gruppe gel�scht, die Eintr�ge und Tabellen bleiben erhalten und werden der n&auml;chst h&ouml;heren Gruppe zugeordnet. ODER DEM IMPORT-ORDNER?')"><img src="{CMT_TEMPLATE}general/img/icon_delete_24px.png" class="imageLinked"></a>
		</div>
		<div class="spacerContainer" style="width: 88px">&nbsp;</div>
		<div class="groupButton">
			{IF ({VAR:newLowerPos})}
			<a href="{SELFURL}&action=move&cmt_type=group&cmt_groupid={VAR:id}&cmt_newpos={VAR:newLowerPos}">
				<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_group_up.gif" class="imageLinked" alt="" />
			</a>
			{ELSE}
			<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_group_up_disabled.gif" alt="" />
			{ENDIF}
		</div>
		<div class="groupButton">
			{IF ({VAR:newUpperPos})}
			<a href="{SELFURL}&action=move&cmt_type=group&cmt_groupid={VAR:id}&cmt_newpos={VAR:newUpperPos}">
				<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_group_down.gif" class="imageLinked" alt="" />
			</a>
			{ELSE}
			<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_group_down_disabled.gif" alt="" />
			{ENDIF}
		</div>
	</div>
</div>
<div id="groupLayer{VAR:id}" class="clearFloats">
	{VAR:contentItems}
</div>
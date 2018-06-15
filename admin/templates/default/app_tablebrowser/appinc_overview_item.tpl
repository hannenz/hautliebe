{IF ("{VAR:id}" != "{VAR:lastEditedId}")}<div class="itemRow{ALTERNATIONFLAG} clearFloats">{ELSE}<div class="itemRowLastEdited clearFloats">{ENDIF}
	<div class="itemIcon">
		{IF ("{VAR:cmt_type}" == 'table')}
		<a href="{SELFURL}&action=change_visibility&cmt_visible={VAR:cmt_itemvisible}&cmt_slider={VAR:cmt_slider}&cmt_type=table&id[]={VAR:id}">
			{IF ("{VAR:cmt_itemvisible}" == "0")}
				<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_table_disabled.gif" class="imageLinked" alt="Tabelle ausgeblendet" />
			{ELSE}
				<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_table.gif" class="imageLinked" alt="Tabelle eingeblendet" />
			{ENDIF}
		</a>
		{ELSE}
		<a href="{SELFURL}&action=change_visibility&cmt_visible={VAR:cmt_itemvisible}&cmt_slider={VAR:cmt_slider}&cmt_type=table&id[]={VAR:id}">
			{IF ("{VAR:cmt_itemvisible}" == "0")}
				<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_application_disabled.gif" class="imageLinked"  alt="Anwendung ausgeblendet" />
			{ELSE}
				<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_application.gif" class="imageLinked" alt="Anwendung eingeblendet" />
			{ENDIF}
		</a>		
		{ENDIF}
	</div>
	<div class="itemName">
		{VAR:cmt_showname}
	</div>
	<div class="itemEditButtons">
		<!-- Standard-Bearbeitungsknöpfe -->
		<div class="itemButton">
			<a href="{SELFURL}&action=edit&cmt_type={VAR:cmt_type}&id[]={VAR:id}"><img src="{CMT_TEMPLATE}general/img/icon_edit_24px.png" class="imageLinked" title="bearbeiten" alt="" /></a>
		</div>
		<div class="itemButton">
			<a href="{SELFURL}&cmt_type={VAR:cmt_type}&action=duplicate&id[]={VAR:id}"><img src="{CMT_TEMPLATE}general/img/icon_duplicate_24px.png" class="imageLinked" title="duplizieren" alt="" /></a>
		</div>
		<div class="itemButton">
			<a href="{SELFURL}&cmt_type={VAR:cmt_type}&action=delete&id[]={VAR:id}" onClick="return del_confirm_text('Wollen Sie den Eintrag {VAR:cmt_showname} wirklich löschen?')">
				<img src="{CMT_TEMPLATE}general/img/icon_delete_24px.png" class="imageLinked" title="l&ouml;schen" alt="" />
			</a>
		</div>
		<div class="spacerContainer" style="width: 16px;">&nbsp;</div>
		<!-- In andere Gruppe verschieben -->
		<div class="itemButton">
			{IF ({ISSET:newLowerGroup:VAR})}
			<a href="{SELFURL}&action=sort&cmt_type={VAR:cmt_type}&id[0]={VAR:id}&cmt_groupid={VAR:cmt_group}&cmt_newpos={VAR:newLowerGroup}">
				<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_changegroup_down.gif" class="imageLinked" alt="" />
			</a>
			{ELSE}
			<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_changegroup_down_disabled.gif" alt="" />
			{ENDIF}
		</div>
		<div class="itemButton">
			{IF ({ISSET:newUpperGroup:VAR})}
			<a href="{SELFURL}&action=sort&cmt_type={VAR:cmt_type}&id[0]={VAR:id}&cmt_groupid={VAR:cmt_group}&cmt_newpos={VAR:newUpperGroup}">
				<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_changegroup_up.gif" class="imageLinked" alt="" />
			</a>
			{ELSE}
			<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_changegroup_up_disabled.gif" alt="" />
			{ENDIF}
		</div>
		<div class="spacerContainer" style="width: 12px;">&nbsp;</div>
		<!-- innerhalb der Gruppe verschieben -->
		<div class="itemButton">
			{IF ({ISSET:newLowerPos:VAR})}
			<a href="{SELFURL}&action=move&cmt_type={VAR:cmt_type}&id[0]={VAR:id}&cmt_groupid={VAR:cmt_group}&cmt_newpos={VAR:newLowerPos}">
				<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_item_up.gif" class="imageLinked" alt="" />
			</a>
			{ELSE}
			<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_group_up_disabled.gif" alt="" />
			{ENDIF}
		</div>
		<div class="itemButton">
			{IF ({ISSET:newUpperPos:VAR})}
			<a href="{SELFURL}&action=move&cmt_type={VAR:cmt_type}&id[0]={VAR:id}&cmt_groupid={VAR:cmt_group}&cmt_newpos={VAR:newUpperPos}">
				<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_item_down.gif" class="imageLinked" alt="" />
			</a>
			{ELSE}
			<img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_group_down_disabled.gif" alt="" />
			{ENDIF}
		</div>
	</div>
	<!--<div class="clearFloats"></div>-->
</div>
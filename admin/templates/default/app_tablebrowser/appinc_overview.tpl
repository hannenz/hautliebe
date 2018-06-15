<link rel="Stylesheet" href="{CMT_TEMPLATE}app_tablebrowser/app_tablebrowser_style.css" type="text/css">
<div class="serviceContainer">
	<div class="serviceButton">
		<a href="{SELFURL}&action=new&cmt_type=group"><img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_new_group.gif" class="imageLinked" alt="neue Gruppe" />neue Gruppe</a>
	</div>
	<div class="serviceButton">
		<a href="{SELFURL}&action=new&cmt_type=table"><img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_new_table.gif" class="imageLinked" alt="neue Tabelle" />neue Tabelle</a>
	</div>
	<div class="serviceButton">
		<a href="{SELFURL}&action=new&cmt_type=application"><img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_new_app.gif" class="imageLinked" alt="neue Anwendung" />neue Anwendung</a>
	</div>
	<div class="serviceButton">
		<a href="Javascript: void(0);" onClick="parent.cmt_navigation.location.reload()"><img src="{CMT_TEMPLATE}app_tablebrowser/img/icon_refresh_navi.gif" class="imageLinked" alt="Navigation aktualisieren" />Navigation aktualisieren</a>
	</div>
	<div class="clearFloats"></div>
</div>
{IF ({ISSET:userMessage:VAR})}{VAR:userMessage}{ENDIF}
<form name="tableBrowserOverview" action="{SELFURL}" method="POST" enctype="application/x-www-form-urlencoded">
{VAR:contentGroups}
</form>
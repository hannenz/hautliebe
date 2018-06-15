<link rel="Stylesheet" href="{CMT_TEMPLATE}/app_tablebrowser/css/cmt_tablebrowser_style.css" type="text/css">

<div class="appHeadlineContainer">
	<div class="appIcon"><img src="{CMT_TEMPLATE}/app_tablebrowser/img/icon.png" alt="" /></div>
	<h1>Tabellenmanager {VAR:appTitle}</h1>
</div>
{IF (!{ISSET:dontShowTabs:VAR})}
<!-- Reiter -->
<div class="cmtPageTabs ui-tabs ui-widget ui-widget-content">
	<ul id="selectTabsHead" class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top {IF ("{CONSTANT:CMT_SLIDER}" == "1")}ui-tabs-active ui-state-active{ENDIF}">
			<a class="ui-corner-top" href="{SELFURL}&amp;cmt_slider=1"><span>Tabellen</span></a>
		</li>
		<li class="ui-state-default ui-corner-top {IF ("{CONSTANT:CMT_SLIDER}" == "2")}ui-tabs-active ui-state-active{ENDIF}">
			<a class="ui-corner-top" href="{SELFURL}&amp;cmt_slider=2"><span>Felder</span></a>
		</li>
		<li class="ui-state-default ui-corner-top {IF ("{CONSTANT:CMT_SLIDER}" == "3")}ui-tabs-active ui-state-active{ENDIF}">
			<a class="ui-corner-top" href="{SELFURL}&amp;cmt_slider=3"><span>Tabellenstruktur</span></a>
		</li>
		<li class="ui-state-default ui-corner-top {IF ("{CONSTANT:CMT_SLIDER}" == "4")}ui-tabs-active ui-state-active{ENDIF}">
			<a class="ui-corner-top" href="{SELFURL}&amp;cmt_slider=4"><span>Templates</span></a>
		</li>
		<li class="ui-state-default ui-corner-top {IF ("{CONSTANT:CMT_SLIDER}" == "5")}ui-tabs-active ui-state-active{ENDIF}">
			<a class="ui-corner-top" href="{SELFURL}&amp;cmt_slider=5"><span>Einstellungen</span></a>
		</li>
	</ul>
</div>
{ENDIF}
<!-- Inhalte -->
{VAR:contentInclude}
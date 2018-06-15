<link rel="Stylesheet" href="{CMT_TEMPLATE}/app_rightsmanager/css/cmt_rightsmanager_style.css" type="text/css" />
<script type="text/javascript" src="{CMT_TEMPLATE}/app_rightsmanager/javascript/cmt_rightmanager_functions.js"></script>
<script type="text/javascript" src="{CMT_TEMPLATE}/app_showtable/javascript/cmt_showtable_functions.js"></script>

<div class="appHeadlineContainer">
	<div class="appIcon"><img src="{IF ({ISSET:icon:VAR})}{VAR:icon}{ELSE}{CMT_TEMPLATE}app_rightsmanager/img/app_rightsmanager_icon.png{ENDIF}" alt="" /></div>
	<h1>Benutzerverwaltung</h1>
</div>
<!-- Reiter -->
<div class="cmtPageTabs ui-tabs">
	<ul id="selectTabsHead" class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top {IF ("{CONSTANT:CMT_SLIDER}" == "1")}ui-tabs-active ui-state-active{ENDIF}">
			<a class="ui-corner-top" href="{SELFURL}&amp;cmt_slider=1"><span>&Uuml;bersicht</span></a>
		</li>
		<li class="ui-state-default ui-corner-top {IF ("{CONSTANT:CMT_SLIDER}" == "2")}ui-tabs-active ui-state-active{ENDIF}">
			<a class="ui-corner-top" href="{SELFURL}&amp;cmt_slider=2"><span>Zugriffsrechte</span></a>
		</li>
	</ul>
</div>
<!-- Inhalte -->
{VAR:contentInclude}
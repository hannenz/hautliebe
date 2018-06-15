<!-- Tabs: start -->
<div id="cmtPageTabs" class="ui-tabs clearfix ui-widget ui-widget-content">
	<ul id="selectTabsHead" class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top cmtTabWithIcon cmtTabEdit {IF ("{VAR:cmtTab}" == "1")}ui-tabs-active ui-state-active{ENDIF}">
			<a href="{SELFURL}&amp;cmtTab=1&amp;cmtAction=" class="ui-corner-top" data-change-action=""><span class="ui-corner-top">Newsletter erstellen</span></a>
		</li>
		<li class="ui-state-default ui-corner-top cmtTabWithIcon cmtTabPreview {IF ("{VAR:cmtTab}" == "2")}ui-tabs-active ui-state-active{ENDIF}">
			<a href="{SELFURL}&amp;cmtTab=2&amp;cmtAction=previewNewsletter" data-change-action="previewNewsletter" class="ui-corner-top"><span class="ui-corner-top">Vorschau</span></a>
		</li>
		<li class="ui-state-default ui-corner-top cmtTabWithIcon cmtTabTest {IF ("{VAR:cmtTab}" == "3")}ui-tabs-active ui-state-active{ENDIF}">
			<a href="{SELFURL}&amp;cmtTab=3&amp;cmtAction=testNewsletter" data-change-action="testNewsletter" class="ui-corner-top"><span class="ui-corner-top">Testversand</span></a>
		</li>
		<li class="ui-state-default ui-corner-top cmtTabWithIcon cmtTabSend {IF ("{VAR:cmtTab}" == "4")}ui-tabs-active ui-state-active{ENDIF}">
			<a href="{SELFURL}&amp;cmtTab=4&amp;cmtAction=startNewsletterDelivery" data-change-action="startNewsletterDelivery" class="ui-corner-top"><span class="ui-corner-top">Versand</span></a>
		</li>
	</ul>
</div>
<!-- Tabs: end -->
<li class="ui-state-default ui-corner-top {IF ({ISSET:cmtActiveTab:VAR})}ui-tabs-active ui-state-active{ENDIF}">
	<a class="ui-corner-top cmtLanguage cmtLanguage-{VAR:languageShortcut}" href="{SELFURL}&amp;cmt_slider={VAR:currentTab}&amp;cmtTab={VAR:currentTab}&amp;cmtLanguage={VAR:languageShortcut}"><span>{VAR:languageName}</span></a>
</li>
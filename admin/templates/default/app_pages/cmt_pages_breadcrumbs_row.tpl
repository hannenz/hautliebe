 / 
{IF (!{ISSET:isLastBreadcrumb:VAR})}
<a href="{SELFURL}&amp;cmtCurrentNodeID={VAR:id}&amp;cmtLanguage={VAR:cmtLanguage}&amp;cmt_showlanguage={VAR:cmtLanguage}">{VAR:cmt_title}</a> 
{ELSE}
<span class="lastBreadcrumb">{VAR:cmt_title}</span>
{ENDIF}
 
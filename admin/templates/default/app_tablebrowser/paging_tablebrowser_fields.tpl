<a class="{IF ("{VAR:page}" == "{VAR:currentPage}")}selectPageCurrent{ELSE}selectPage{ENDIF}" href="{SELFURL}&amp;cmtPage={VAR:page}&amp;cmtIpp={VAR:itemsPerPage}">{VAR:page}</a>
{SPLITDATAHERE}
 
{SPLITDATAHERE}
{IF ({ISSET:prevPage:VAR})}<a class="{IF ("{VAR:page}" == "{VAR:currentPage}")}selectPageCurrent{ELSE}selectPage{ENDIF}" href="{SELFURL}&amp;cmtPage={VAR:page}&amp;cmtIpp={VAR:itemsPerPage}">&laquo;&nbsp;zur&uuml;ck</a>{ELSE}<span class="selectPageDisabled">&laquo;&nbsp;zur&uuml;ck</span>{ENDIF}
{SPLITDATAHERE}
{IF ({ISSET:nextPage:VAR})}<a class="{IF ("{VAR:page}" == "{VAR:currentPage}")}selectPageCurrent{ELSE}selectPage{ENDIF}" href="{SELFURL}&amp;cmtPage={VAR:page}&amp;cmtIpp={VAR:itemsPerPage}">vorw&auml;rts&nbsp;&raquo;</a>{ELSE}<span class="selectPageDisabled">vorw&auml;rts&nbsp;&raquo;</span>{ENDIF}
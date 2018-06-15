{IF (!{ISSET:hasTextEditor:VAR})}
<!-- <script type="text/javascript" src="{PATHTOADMIN}javascript/ace/ace.js"></script> -->
{ENDIF}
<div id="{VAR:cmtField}_editor" class="cmtEditor" data-field="{VAR:cmtField}" data-theme="{VAR:editorTheme}" data-language="{VAR:editorLanguage}">{VAR:cmtFieldData:cmt_htmlentities}</div>
<!-- 
{VAR:cmtFieldData:cmt_htmlentities}
 -->